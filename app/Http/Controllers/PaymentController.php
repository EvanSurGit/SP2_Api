<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Panier;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PaymentController extends Controller
{
    public function index(Request $request)
    {
        return $this->show($request);
    }

    public function show(Request $request)
    {
        $address = session('checkout.address');
        return view('checkout.payment', compact('address'));
    }

    public function process(Request $request)
    {
      $validated = $request->validate([
          'payment_method' => ['required', 'in:paypal,cheque,card'],
          'card_name'      => ['exclude_unless:payment_method,card', 'required', 'string', 'max:120'],
          'card_number'    => ['exclude_unless:payment_method,card', 'required', 'string', 'regex:/^\d{12,19}$/'],
          'card_exp'       => ['exclude_unless:payment_method,card', 'required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
          'card_cvc'       => ['exclude_unless:payment_method,card', 'required', 'string', 'regex:/^\d{3,4}$/'],
      ]);
  
      $method = $validated['payment_method'];
      $user   = $request->user();
  
      // ?? Récup panier actif
      $panier = Panier::where('user_id', $user->id)
          ->where('status', 'open')
          ->with('puzzles')
          ->first();
  
      if (!$panier || $panier->puzzles->isEmpty()) {
          return redirect()->route('cart.index')->with('error', 'Panier vide');
      }
  
      // ?? Calcul total
      $total = $panier->puzzles->reduce(function ($carry, $p) {
          return $carry + ($p->pivot->prix * $p->pivot->quantite);
      }, 0);
  
      // ?? Définir statut selon paiement
      $status = match ($method) {
          'cheque' => 'en_attente',
          'paypal' => 'payée',
          'card'   => 'payée',
      };

      // ?? TRANSACTION COMPLÈTE
      DB::transaction(function () use ($user, $panier, $total, $status) {
  
          // ? Création commande
          $order = Order::create([
              'user_id' => $user->id,
              'total' => $total,
              'status' => $status,
              'date_commande' => now(),
          ]);
  
          // ? Lignes commande
          foreach ($panier->puzzles as $p) {
              $order->puzzles()->attach($p->id, [
                  'quantite' => $p->pivot->quantite,
                  'prix' => $p->pivot->prix,
              ]);
          }
  
          // ? Fermer panier
          $panier->status = 'completed';
          $panier->save();
  
          // ? Nouveau panier vide
          Panier::create([
              'user_id' => $user->id,
              'status' => 'open',
              'total' => 0,
          ]);
      });

      // ==========================
      // REDIRECTIONS
      // ==========================
  
      if ($method === 'paypal') {
          return redirect()->away('https://www.paypal.com/fr/home');
      }
  
      if ($method === 'cheque') {
          session()->put('checkout.invoice_user_id', $user->id);
          return redirect()->route('checkout.invoice');
      }
  
      // Carte bancaire
      session()->put('checkout.payment_choice', [
          'method' => 'card',
          'card'   => [
              'name'  => $request->card_name,
              'last4' => substr(preg_replace('/\D/', '', $request->card_number), -4),
              'exp'   => $request->card_exp,
          ],
      ]);

      return redirect()
          ->route('checkout.success')
          ->with('showReviewPopup', true)
          ->with('message', 'Paiement validé ! Commande créée.');
    }

    public function confirm()
    {
        $address = session('checkout.address');
        $payment = session('checkout.payment_choice');
        return view('checkout.confirmation', compact('address', 'payment'));
    }

    public function success()
    {
        return view('checkout.success');
    }

    // âœ… GÃ©nÃ©ration du PDF et redirection aprÃ¨s
    public function invoice()
    {
        $userId = session('checkout.invoice_user_id');
        $user = \App\Models\User::find($userId);
    
        if (!$user) {
            return redirect()->route('checkout.success');
        }
    
        $panier = Panier::where('user_id', $user->id)
            ->latest('id')
            ->with(['puzzles' => fn($q) => $q->withPivot(['quantite','prix'])])
            ->first();

        // ðŸŸ¢ On vÃ©rifie et on marque bien comme "completed"
        if ($panier && $panier->status === 'open') {
            $panier->status = 'completed';
            $panier->save();
        }
    
        $lines = $panier
            ? $panier->puzzles->map(fn($p) => [
                'name'       => $p->nom ?? $p->name ?? 'Puzzle',
                'quantity'   => (int)($p->pivot->quantite ?? 1),
                'unit_price' => (float)($p->pivot->prix ?? $p->prix ?? 0),
                'line_total' => (int)($p->pivot->quantite ?? 1) * (float)($p->pivot->prix ?? $p->prix ?? 0),
            ])
            : collect();

        $total = $lines->sum('line_total');
        $invoiceNo = 'FAC-' . now()->format('Ymd-His') . '-' . $user->id;
    
        $cheque_to = config('app.cheque_to', 'WoodyCraft');
        $cheque_address = config('app.cheque_address', "â€” Adresse postale de l'entreprise â€”");
    
        $pdf = Pdf::loadView('pdf.facture-cheque', [
            'user' => $user,
            'lines' => $lines,
            'total' => $total,
            'invoiceNo' => $invoiceNo,
            'generated_at' => now(),
            'cheque_to' => $cheque_to,
            'cheque_address' => $cheque_address,
        ])->setPaper('a4');
    
        $fileName = "facture-cheque-{$invoiceNo}.pdf";
        $pdfData = base64_encode($pdf->output());
    
        return view('checkout.invoice', compact('pdfData', 'fileName'));
    }
}

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Facture {{ $invoiceNo }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2, h3 { margin: 0 0 6px 0; }
    .muted { color:#666; font-size:11px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f5f5f5; }
    tfoot td { font-weight: bold; }
  </style>
</head>
<body>
  <h2>Facture {{ $invoiceNo }}</h2>
  <p class="muted">Générée le {{ \Carbon\Carbon::parse($generated_at)->format('d/m/Y H:i') }}</p>

  <h3>Client</h3>
  <p>
    {{ $user->name ?? $user->email }}<br>
    @if(!empty($address))
      {{ $address['numero'] ?? '' }} {{ $address['rue'] ?? '' }}<br>
      {{ $address['cp'] ?? '' }} {{ $address['ville'] ?? '' }}
    @endif
  </p>

  <h3>Détails</h3>
  <table>
    <thead>
      <tr>
        <th>Produit</th>
        <th>Qté</th>
        <th>PU (€)</th>
        <th>Total ligne (€)</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($lines as $line)
        <tr>
          <td>{{ $line['name'] }}</td>
          <td>{{ $line['quantity'] }}</td>
          <td>{{ number_format($line['unit_price'], 2, ',', ' ') }}</td>
          <td>{{ number_format($line['line_total'], 2, ',', ' ') }}</td>
        </tr>
      @empty
        <tr><td colspan="4">Aucun article.</td></tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" style="text-align:right;">TOTAL</td>
        <td>{{ number_format($total, 2, ',', ' ') }} €</td>
      </tr>
    </tfoot>
  </table>

  <h3>Règlement par chèque</h3>
  <p>
    À l’ordre de : <strong>{{ $cheque_to }}</strong><br>
    Adresse d’envoi :<br>
    {!! nl2br(e($cheque_address)) !!}
  </p>
</body>
</html>

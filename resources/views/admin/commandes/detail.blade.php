@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Détail de la commande #{{ $commande->id }}</h2>
        <a href="{{ route('commandes.en_attente') }}" class="btn btn-secondary">
            ? Retour à la liste
        </a>
    </div>

    {{-- Informations client --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <strong>?? Informations client</strong>
        </div>
        <div class="card-body">
            <p><strong>Nom :</strong> {{ $commande->user->name ?? 'N/A' }}</p>
            <p><strong>Email :</strong> {{ $commande->user->email ?? 'N/A' }}</p>
            <p><strong>Date de commande :</strong> {{ $commande->created_at->format('d/m/Y à H:i') }}</p>
            <p>
                <strong>Statut :</strong>
                <span class="badge 
                    @if($commande->status === 'livrée') bg-success
                    @elseif($commande->status === 'expédiée') bg-primary
                    @elseif($commande->status === 'validée') bg-info
                    @elseif($commande->status === 'annulée') bg-danger
                    @else bg-warning text-dark
                    @endif">
                    {{ $commande->status }}
                </span>
            </p>
        </div>
    </div>

    {{-- Articles commandés --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <strong>?? Articles commandés</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Produit</th>
                        <th class="text-center">Quantité</th>
                        <th class="text-end">Prix unitaire</th>
                        <th class="text-end">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commande->puzzles as $puzzle)
                    <tr>
                        <td>{{ $puzzle->nom }}</td>
                        <td class="text-center">{{ $puzzle->pivot->quantite }}</td>
                        <td class="text-end">{{ number_format($puzzle->pivot->prix, 2) }} €</td>
                        <td class="text-end">
                            {{ number_format($puzzle->pivot->quantite * $puzzle->pivot->prix, 2) }} €
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Aucun article</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Adresse de livraison --}}
    @if($commande->adresseLivraison)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <strong>?? Adresse de livraison</strong>
        </div>
        <div class="card-body">
            <p>{{ $commande->adresseLivraison->adresse ?? 'N/A' }}</p>
        </div>
    </div>
    @endif

    {{-- Total --}}
    <div class="card">
        <div class="card-body text-end">
            <h4><strong>Total : {{ number_format($commande->total, 2) }} €</strong></h4>
        </div>
    </div>

</div>
@endsection
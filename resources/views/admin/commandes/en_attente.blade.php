@extends('layouts.admin')

@section('content')
<h1>Commandes en attente</h1>

@if(session('success'))
    <div>{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Statut</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($commandes as $commande)
        <tr>
            <td>{{ $commande->id }}</td>
            <td>{{ $commande->user->name ?? 'Inconnu' }}</td>
            <td>{{ $commande->status }}</td>
            <td>{{ number_format($commande->total, 2) }} €</td>
            <td>
                @if($commande->status === 'payée')
                    <form action="{{ route('admin.commandes.valider', $commande->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Valider</button>
                    </form>
                @endif

                @if($commande->status === 'validée')
                    <form action="{{ route('admin.commandes.expedier', $commande->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit">Expédier</button>
                    </form>
                @endif

                <form action="{{ route('admin.commandes.supprimer', $commande->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Supprimer cette commande ?')">Supprimer</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
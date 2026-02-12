@extends('admin.layout')

@section('admin-content')
    <style>
        @media print {
            .cards-view {
                display: none !important;
            }

            .print-table-view {
                display: block !important;
            }
        }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h2 class="mb-1">Liste des Joueurs</h2>
            <div class="text-muted">Déjà joué : {{ $playedCount }}</div>
        </div>
        <div class="no-print d-flex gap-2">
            <button onclick="printReport()" class="btn btn-secondary"><i class="bi bi-printer"></i> Imprimer / PDF</button>
            <a href="{{ route('admin.users.export') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exporter CSV</a>
        </div>
    </div>

    <div class="row g-3 cards-view">
        @foreach($players as $player)
        @php
            $isWinner = $player->has_played && $player->price && $player->price !== 'Perdu';
            $isLoser = $player->has_played && (!$player->price || $player->price === 'Perdu');
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 {{ $isWinner ? 'border border-success border-2' : '' }} {{ $isLoser ? 'border border-danger border-2' : '' }}">
                <div class="card-body">
                    <h6 class="mb-2">
                        #{{ $players->firstItem() + $loop->index }} — {{ $player->nom }} {{ $player->prenom }}
                    </h6>
                    <div><strong>ID:</strong> {{ $player->id }}</div>
                    <div><strong>Téléphone:</strong> {{ $player->telephone }}</div>
                    <div><strong>RGPD:</strong> {{ $player->is_accept ? 'Oui' : 'Non' }}</div>
                    <div>
                        <strong>Statut:</strong>
                        @if($player->has_played)
                            {{ $player->price && $player->price !== 'Perdu' ? 'Gagné' : 'Perdu' }}
                        @else
                            Pas encore joué
                        @endif
                    </div>
                    @if($player->has_played && $player->price)
                        <div><strong>Lot:</strong> {{ $player->price }}</div>
                    @endif
                    <div><strong>Inscrit le:</strong> {{ $player->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-3 d-flex justify-content-center">
        {{ $players->links() }}
    </div>

    <div class="print-table-view d-none">
        <h4 class="mb-3">Rapport des joueurs</h4>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Téléphone</th>
                    <th>RGPD</th>
                    <th>Statut</th>
                    <th>Lot</th>
                    <th>Inscrit le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($players as $player)
                    <tr>
                        <td>{{ $players->firstItem() + $loop->index }}</td>
                        <td>{{ $player->id }}</td>
                        <td>{{ $player->nom }}</td>
                        <td>{{ $player->prenom }}</td>
                        <td>{{ $player->telephone }}</td>
                        <td>{{ $player->is_accept ? 'Oui' : 'Non' }}</td>
                        <td>
                            @if($player->has_played)
                                {{ $player->price && $player->price !== 'Perdu' ? 'Gagné' : 'Perdu' }}
                            @else
                                Pas encore joué
                            @endif
                        </td>
                        <td>{{ $player->has_played ? ($player->price ?? '-') : '-' }}</td>
                        <td>{{ $player->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

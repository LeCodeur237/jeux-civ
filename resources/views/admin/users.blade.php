@extends('admin.layout')

@section('admin-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <h2 class="mb-3 mb-md-0">Liste des Utilisateurs</h2>
        <div class="no-print d-flex gap-2">
            <button onclick="printReport()" class="btn btn-secondary"><i class="bi bi-printer"></i> Imprimer / PDF</button>
            <a href="{{ route('admin.users.export') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exporter CSV</a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" type="button"
                role="tab">Utilisateurs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="players-tab" data-bs-toggle="tab" data-bs-target="#players-pane" type="button"
                role="tab">Joueurs</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="users-pane" role="tabpanel">
            <div class="row g-3">
                @foreach($users as $user)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-2">{{ $user->nom }} {{ $user->prenom }}</h6>
                            <div><strong>ID:</strong> {{ $user->id }}</div>
                            <div><strong>Age:</strong> {{ $user->age }}</div>
                            <div><strong>Profession:</strong> {{ $user->profession }}</div>
                            <div><strong>Téléphone:</strong> {{ $user->phone }}</div>
                            <div><strong>Inscrit le:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>

        <div class="tab-pane fade" id="players-pane" role="tabpanel">
            <div class="row g-3">
                @foreach($players as $player)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-2">{{ $player->nom }} {{ $player->prenom }}</h6>
                            <div><strong>ID:</strong> {{ $player->id }}</div>
                            <div><strong>Téléphone:</strong> {{ $player->telephone }}</div>
                            <div><strong>RGPD:</strong> {{ $player->is_accept ? 'Oui' : 'Non' }}</div>
                            <div><strong>Inscrit le:</strong> {{ $player->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3">
                {{ $players->links() }}
            </div>
        </div>
    </div>
@endsection

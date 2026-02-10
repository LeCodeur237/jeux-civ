@extends('admin.layout')

@section('admin-content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <h2 class="mb-3 mb-md-0">Liste des Utilisateurs</h2>
        <div class="no-print d-flex gap-2">
            <button onclick="printReport()" class="btn btn-secondary"><i class="bi bi-printer"></i> Imprimer / PDF</button>
            <a href="{{ route('admin.users.export') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exporter CSV</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Age</th>
                            <th>Profession</th>
                            <th>Téléphone</th>
                            <th>Inscrit le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->nom }}</td>
                            <td>{{ $user->prenom }}</td>
                            <td>{{ $user->age }}</td>
                            <td>{{ $user->profession }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layout')

@section('admin-content')
    <h2 class="mb-4">Gestion des Cadeaux</h2>

    <div class="row">
        <!-- Formulaire d'ajout -->
        <div class="col-md-4 mb-4 no-print">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Ajouter un cadeau</div>
                <div class="card-body">
                    <form action="{{ route('admin.gifts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nom affiché (Roue)</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: T-shirt">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nom interne (Jeu)</label>
                            <input type="text" name="game_name" class="form-control" required placeholder="Ex: tshirt_epa">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (Optionnel)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des cadeaux -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Nom Jeu</th>
                                <th class="no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gifts as $gift)
                            <tr>
                                <td>
                                    @if($gift->image)
                                        <img src="{{ asset('images/' . $gift->image) }}" alt="img" style="width: 40px; height: 40px; object-fit: contain;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $gift->name }}</td>
                                <td>{{ $gift->game_name }}</td>
                                <td class="no-print">
                                    <form action="{{ route('admin.gifts.delete', $gift->id) }}" method="POST" onsubmit="return confirm('Supprimer ce cadeau ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

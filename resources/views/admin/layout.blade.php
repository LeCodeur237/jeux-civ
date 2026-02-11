<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Jeu EPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            width: 250px;
            background-color: #343a40;
            color: white;
            flex-shrink: 0;
        }

        .sidebar a {
            color: rgba(255, 255, 255, .8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #7d1900;
            color: white;
        }

        .content {
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                min-height: auto;
            }
        }

        @media print {

            .sidebar,
            .no-print {
                display: none !important;
            }

            .content {
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-md-row min-vh-100">
        <div class="sidebar d-flex flex-column p-3">
            <h4 class="mb-4 text-center">Admin EPA</h4>
            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Utilisateurs
            </a>
            {{-- <a href="{{ route('admin.gifts') }}" class="{{ request()->routeIs('admin.gifts') ? 'active' : '' }}">
                <i class="bi bi-gift me-2"></i> Cadeaux
            </a> --}}
            <a href="{{ url('/valentines-day') }}" class="mt-auto border-top pt-3">
                <i class="bi bi-house me-2"></i> Retour au Jeu
            </a>
        </div>
        <div class="content flex-grow-1">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @yield('admin-content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script>
        function printReport() {
            window.print();
        }
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Actividades</title>
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('styles')
</head>

<body>
    @include('layouts.sidebar')
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm" style="margin-left:250px;">
        <div class="container">
            <a class="navbar-brand" href="{{ route('activities.index') }}">
                <i class="fas fa-tasks"></i> Gestión de Actividades
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('activities.index') }}">
                            <i class="fas fa-tasks"></i> Actividades
                        </a>
                    </li>
                    <li class="nav-item dropdown {{ request()->routeIs('requirements.*') ? 'active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" id="requirementsDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="color: #ffffff !important; font-weight: 500; transition: all 0.3s ease; background-color: rgba(255, 255, 255, 0.1) !important; border-radius: 5px; padding: 8px 12px;">
                            <i class="fas fa-clipboard-list mr-1"></i> Requerimientos
                        </a>
                        <div class="dropdown-menu shadow-lg border-0" aria-labelledby="requirementsDropdown"
                            style="border-radius: 10px; min-width: 220px;">
                            <h6 class="dropdown-header text-primary font-weight-bold"
                                style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 10px 10px 0 0; margin: 0; padding: 12px 20px;">
                                <i class="fas fa-clipboard-list mr-2"></i>Gestión de Requerimientos
                            </h6>
                            <a class="dropdown-item py-2 px-3" href="{{ route('requirements.index') }}"
                                style="transition: all 0.2s ease;">
                                <i class="fas fa-list text-info mr-2"></i> Lista de Requerimientos
                            </a>
                            <a class="dropdown-item py-2 px-3" href="{{ route('requirements.create') }}"
                                style="transition: all 0.2s ease;">
                                <i class="fas fa-plus text-success mr-2"></i> Nuevo Requerimiento
                            </a>
                            <div class="dropdown-divider my-1"></div>
                            <a class="dropdown-item py-2 px-3" href="{{ route('requirements.report') }}"
                                style="transition: all 0.2s ease;">
                                <i class="fas fa-chart-bar text-warning mr-2"></i> Reporte de Requerimientos
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="contenido-principal">
        <div class="container mt-4">
            @yield('content')
        </div>
    </main>

           

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

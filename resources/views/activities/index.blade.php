@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid px-4">
        <!-- Header Section -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-gradient mb-2">
                        <i class="fas fa-tasks text-primary"></i> Gestión de Actividades
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle"></i>
                        Administra todas las actividades del sistema y sus subactividades
                    </p>
                </div>
                <div class="action-buttons">
                    <a href="{{ route('activities.create') }}" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-plus"></i> Nueva Actividad
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Search and Filters Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-search text-primary"></i> Búsqueda y Filtros
                    </h5>
                    <button class="btn btn-outline-secondary btn-sm" id="toggleFilters">
                        <i class="fas fa-filter"></i> <span id="filterToggleText">Mostrar Filtros</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="search-container">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control form-control-lg" id="searchInput"
                                    placeholder="Buscar en actividades, casos, analistas, comentarios, correos..."
                                    autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="search-suggestions" id="searchSuggestions" style="display: none;"></div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-keyboard"></i>
                                Atajos: <kbd>Ctrl+K</kbd> o <kbd>Ctrl+F</kbd> para buscar, <kbd>Esc</kbd> para limpiar
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="search-stats">
                            <div class="d-flex align-items-center justify-content-end">
                                <span class="badge badge-info mr-2" id="searchResultsCount" style="display: none;">
                                    <i class="fas fa-list-ol"></i> <span id="resultsNumber">0</span> resultados
                                </span>
                                <div class="loading-spinner" id="searchSpinner" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón para mostrar/ocultar estadísticas -->
        <button class="btn btn-outline-secondary btn-sm mb-3" id="toggleStatistics">
            <i class="fas fa-chart-bar"></i> <span id="statisticsToggleText">Mostrar Estadísticas</span>
        </button>
        <!-- Statistics Cards -->
        <div id="statisticsCards" class="row mb-4">
            <!-- Primera fila: 4 columnas principales -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-primary">
                        <div class="stats-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->count() }}</h3>
                            <p>Total Actividades</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-secondary">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('no_iniciada');})->count() }}
                            </h3>
                            <p>No Iniciadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-info">
                        <div class="stats-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('en_ejecucion');})->count() }}
                            </h3>
                            <p>En Ejecución</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-success">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('culminada');})->count() }}
                            </h3>
                            <p>Culminadas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda fila: Estados intermedios y especiales -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-warning">
                        <div class="stats-icon">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('en_espera_de_insumos');})->count() }}
                            </h3>
                            <p>En Espera</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card" style="background-color: #fd7e14;">
                        <div class="stats-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('en_certificacion_por_cliente');})->count() }}
                            </h3>
                            <p>En Certificación</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card" style="background-color: #20c997;">
                        <div class="stats-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('pases_enviados');})->count() }}
                            </h3>
                            <p>Pases Enviados</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-dark">
                        <div class="stats-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ number_format(($activities->filter(function ($activity) {return $activity->hasStatus('culminada');})->count() /max($activities->count(), 1)) *100,1) }}%
                            </h3>
                            <p>% Completado</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tercera fila: Estados finales -->
            <div class="row mb-4">
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card" style="background-color: #6c757d;">
                        <div class="stats-icon">
                            <i class="fas fa-pause"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('pausada');})->count() }}
                            </h3>
                            <p>Pausadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stats-card bg-danger">
                        <div class="stats-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->filter(function ($activity) {return $activity->hasStatus('cancelada');})->count() }}
                            </h3>
                            <p>Canceladas</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="stats-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stats-content">
                            <h3>{{ $activities->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                            <p>Este Mes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleStatisticsBtn = document.getElementById('toggleStatistics');
                const statisticsCards = document.getElementById('statisticsCards');
                const statisticsToggleText = document.getElementById('statisticsToggleText');
                // Inicialmente ocultar las estadísticas
                statisticsCards.style.display = 'none';
                toggleStatisticsBtn.addEventListener('click', function() {
                    if (statisticsCards.style.display === 'none') {
                        statisticsCards.style.display = 'block';
                        statisticsToggleText.textContent = 'Ocultar Estadísticas';
                    } else {
                        statisticsCards.style.display = 'none';
                        statisticsToggleText.textContent = 'Mostrar Estadísticas';
                    }
                });
            });
        </script>

        <!-- Search Results Alert -->
        <div class="alert alert-info" id="searchResultsAlert" style="display: none;">
            <div class="d-flex align-items-center">
                <i class="fas fa-search mr-2"></i>
                <div>
                    <strong>Resultados de búsqueda:</strong>
                    <span id="searchResultsText"></span>
                    <button class="btn btn-sm btn-outline-info ml-2" id="showAllResults">
                        <i class="fas fa-eye"></i> Ver todos los resultados
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ml-1" id="clearSearchResults">
                        <i class="fas fa-times"></i> Limpiar búsqueda
                    </button>
                </div>
            </div>
        </div>

        <!-- Activities Table -->
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> <span id="tableTitle">Lista de Actividades</span>
                    </h5>
                    <div class="header-actions">
                        <button class="btn btn-sm btn-warning mr-2" id="clearAllColumnFilters" style="display: block;">
                            <i class="fas fa-times-circle"></i> Limpiar Filtros
                        </button>
                        <small class="text-light">
                            <i class="fas fa-info-circle"></i>
                            Haz clic en <i class="fas fa-chevron-right"></i> para ver subactividades
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div id="tableContainer">
                        <table class="table table-hover mb-0 modern-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0" style="position: relative;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sortable" data-sort="caso" style="cursor: pointer;">
                                                <i class="fas fa-hashtag text-primary"></i> Caso
                                                <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-file-alt text-primary"></i> Nombre
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-align-left text-primary"></i> Descripción
                                    </th>
                                    <th class="border-0" style="position: relative;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sortable" data-sort="status" style="cursor: pointer;">
                                                <i class="fas fa-flag text-primary"></i> Estado
                                                <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                                            </div>
                                            <div class="custom-dropdown">
                                                <button class="btn btn-sm btn-outline-secondary filter-toggle"
                                                    type="button" data-filter="status" style="padding: 2px 6px;">
                                                    <i class="fas fa-filter"></i>
                                                </button>
                                                <div class="custom-dropdown-menu" id="status-filter-menu"
                                                    style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                                                    <h6 class="dropdown-header"
                                                        style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                                        Filtrar por Estado</h6>
                                                    <div class="px-3 py-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input status-filter" type="checkbox"
                                                                value="" id="status-all" checked>
                                                            <label class="form-check-label" for="status-all">Todos</label>
                                                        </div>
                                                        @foreach ($statusLabels as $key => $label)
                                                            <div class="form-check">
                                                                <input class="form-check-input status-filter"
                                                                    type="checkbox" value="{{ $key }}"
                                                                    id="status-{{ $key }}">
                                                                <label class="form-check-label d-flex align-items-center"
                                                                    for="status-{{ $key }}">
                                                                    <span class="badge badge-pill mr-2"
                                                                        style="background-color: {{ $statusColors[$key] ?? '#6c757d' }}; color: white; width: 18px; height: 18px; display: inline-block; text-align: center; line-height: 18px; font-size: 0.8em; border-radius: 50%;">
                                                                        &nbsp;
                                                                    </span>
                                                                    {{ $label }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="border-0" style="position: relative;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sortable" data-sort="analistas" style="cursor: pointer;">
                                                <i class="fas fa-users text-primary"></i> Analistas
                                                <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                                            </div>
                                            <div class="custom-dropdown">
                                                <button class="btn btn-sm btn-outline-secondary filter-toggle"
                                                    type="button" data-filter="analistas" style="padding: 2px 6px;">
                                                    <i class="fas fa-filter"></i>
                                                </button>
                                                <div class="custom-dropdown-menu" id="analistas-filter-menu"
                                                    style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                                                    <h6 class="dropdown-header"
                                                        style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                                        Filtrar por Analista</h6>
                                                    <div class="px-3 py-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input analista-filter"
                                                                type="checkbox" value="" id="analista-all" checked>
                                                            <label class="form-check-label"
                                                                for="analista-all">Todos</label>
                                                        </div>
                                                        @foreach ($analistas as $analista)
                                                            <div class="form-check">
                                                                <input class="form-check-input analista-filter"
                                                                    type="checkbox" value="{{ $analista->id }}"
                                                                    id="analista-{{ $analista->id }}">
                                                                <label class="form-check-label"
                                                                    for="analista-{{ $analista->id }}">{{ $analista->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    {{-- <th class="border-0">
                                <i class="fas fa-comments text-primary"></i> Comentarios
                            </th> --}}
                                    <th class="border-0">
                                        <i class="fas fa-clipboard-list text-primary"></i> Requerimientos
                                    </th>
                                    {{-- <th class="border-0">
                                <i class="fas fa-envelope text-primary"></i> Correos
                            </th> --}}
                                    <th class="border-0" style="position: relative;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sortable" data-sort="fecha_recepcion" style="cursor: pointer;">
                                                <i class="fas fa-calendar text-primary"></i> Fecha
                                                <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                                            </div>
                                            <div class="custom-dropdown">
                                                <button class="btn btn-sm btn-outline-secondary filter-toggle"
                                                    type="button" data-filter="fecha" style="padding: 2px 6px;">
                                                    <i class="fas fa-filter"></i>
                                                </button>
                                                <div class="custom-dropdown-menu" id="fecha-filter-menu"
                                                    style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 250px;">
                                                    <h6 class="dropdown-header"
                                                        style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                                        Filtrar por Fecha</h6>
                                                    <div class="px-3 py-2">
                                                        <div class="form-group mb-2">
                                                            <label class="small">Desde:</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                id="fecha-desde-filter">
                                                        </div>
                                                        <div class="form-group mb-2">
                                                            <label class="small">Hasta:</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                id="fecha-hasta-filter">
                                                        </div>
                                                        <button class="btn btn-sm btn-primary btn-block"
                                                            id="apply-date-filter">Aplicar</button>
                                                        <button class="btn btn-sm btn-outline-secondary btn-block"
                                                            id="clear-date-filter">Limpiar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th class="border-0 text-center">
                                        <i class="fas fa-cogs text-primary"></i> Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr class="parent-activity activity-row" data-activity-id="{{ $activity->id }}">
                                        <td class="align-middle">
                                            <span class="badge badge-outline-primary font-weight-bold">
                                                {{ $activity->caso }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                @if ($activity->subactivities->count() > 0)
                                                    <span class="toggle-subactivities mr-2" style="cursor: pointer;"
                                                        data-activity-id="{{ $activity->id }}">
                                                        <i class="fas fa-chevron-right text-primary"
                                                            id="icon-{{ $activity->id }}"></i>
                                                    </span>
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold text-dark small">
                                                        {{ Str::limit($activity->name, 40) }}
                                                        @if (strlen($activity->name) > 40)
                                                            <span class="text-primary" style="cursor: pointer;"
                                                                title="{{ $activity->name }}" data-toggle="tooltip">
                                                                <i class="fas fa-info-circle"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($activity->subactivities->count() > 0)
                                                        <small class="text-muted">
                                                            <i class="fas fa-sitemap"></i>
                                                            {{ $activity->subactivities->count() }} subactividad(es)
                                                        </small>
                                                    @endif
                                                </div>

                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="description-cell">
                                                {{ Str::limit($activity->description, 30) }}
                                                @if (strlen($activity->description) > 30)
                                                    <span class="text-primary" style="cursor: pointer;"
                                                        title="{{ $activity->description }}" data-toggle="tooltip">
                                                        <i class="fas fa-info-circle"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="status-cell" data-activity-id="{{ $activity->id }}">
                                                <div class="status-display">
                                                    @if ($activity->statuses->count() > 0)
                                                        @foreach ($activity->statuses as $status)
                                                            <span class="badge badge-pill mr-1 mb-1"
                                                                style="background-color: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                                                <i class="{{ $status->icon ?? 'fas fa-circle' }}"></i>
                                                                {{ $status->label }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        {{-- Fallback al sistema anterior --}}
                                                        @php
                                                            $statusClass = match ($activity->status) {
                                                                'culminada' => 'success',
                                                                'en_ejecucion' => 'primary',
                                                                'en_espera_de_insumos' => 'warning',
                                                                default => 'secondary',
                                                            };
                                                            $statusIcon = match ($activity->status) {
                                                                'culminada' => 'check-circle',
                                                                'en_ejecucion' => 'play-circle',
                                                                'en_espera_de_insumos' => 'pause-circle',
                                                                default => 'circle',
                                                            };
                                                        @endphp
                                                        <span class="badge badge-{{ $statusClass }} badge-pill">
                                                            <i class="fas fa-{{ $statusIcon }}"></i>
                                                            {{ $activity->status_label }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="status-edit-btn">
                                                    <button class="btn btn-sm btn-outline-secondary edit-status-btn"
                                                        data-activity-id="{{ $activity->id }}" title="Editar estados">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if ($activity->analistas->isEmpty())
                                                <span class="text-muted">
                                                    <i class="fas fa-user-slash"></i> Sin asignar
                                                </span>
                                            @else
                                                <div class="analysts-list">
                                                    @foreach ($activity->analistas as $analista)
                                                        <span class="badge badge-light mr-1 mb-1">
                                                            <i class="fas fa-user"></i> {{ $analista->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        {{-- <td class="align-middle">
                                    @if ($activity->comments->count() > 0)
                                        <div class="comments-info">
                                            <a href="{{ route('activities.comments', $activity) }}" class="text-decoration-none">
                                                <span class="badge badge-info badge-pill">
                                                    <i class="fas fa-comments"></i> {{ $activity->comments->count() }}
                                                </span>
                                            </a>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $activity->comments->last()->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-comment-slash"></i> Sin comentarios
                                        </span>
                                    @endif
                                </td> --}}
                                        <td class="align-middle">
                                            @if ($activity->requirements->count() > 0)
                                                <div class="requirements-info">
                                                    <a href="{{ route('requirements.index', ['activity_id' => $activity->id]) }}"
                                                        class="text-decoration-none">
                                                        <span class="badge badge-warning badge-pill">
                                                            <i class="fas fa-clipboard-list"></i>
                                                            {{ $activity->requirements->count() }}
                                                        </span>
                                                    </a>
                                                    <div class="mt-1">
                                                        @php
                                                            $pendientes = $activity->requirements
                                                                ->where('status', 'pendiente')
                                                                ->count();
                                                            $recibidos = $activity->requirements
                                                                ->where('status', 'recibido')
                                                                ->count();
                                                        @endphp
                                                        <small class="text-muted d-block">
                                                            <span class="badge badge-sm badge-warning">{{ $pendientes }}
                                                                pendientes</span>
                                                            <span class="badge badge-sm badge-success">{{ $recibidos }}
                                                                recibidos</span>
                                                        </small>
                                                        @if ($activity->requirements->count() > 0)
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock"></i>
                                                                {{ $activity->requirements->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-clipboard"></i> Sin requerimientos
                                                </span>
                                            @endif
                                        </td>
                                        {{-- <td class="align-middle">
                                    @if ($activity->emails->count() > 0)
                                        <div class="emails-info">
                                            <a href="{{ route('activities.emails', $activity) }}" class="text-decoration-none">
                                                <span class="badge badge-success badge-pill">
                                                    <i class="fas fa-envelope"></i> {{ $activity->emails->count() }}
                                                </span>
                                            </a>
                                            <div class="mt-1">
                                                @php
                                                    $lastEmail = $activity->emails->sortByDesc('created_at')->first();
                                                    $sentCount = $activity->emails->where('type', 'sent')->count();
                                                    $receivedCount = $activity->emails->where('type', 'received')->count();
                                                @endphp
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <span class="badge badge-outline-primary badge-sm mr-1">
                                                        <i class="fas fa-paper-plane"></i> {{ $sentCount }}
                                                    </span>
                                                    <span class="badge badge-outline-success badge-sm">
                                                        <i class="fas fa-inbox"></i> {{ $receivedCount }}
                                                    </span>
                                                </div>
                                                @if ($lastEmail)
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> 
                                                        {{ $lastEmail->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-envelope-open"></i> Sin correos
                                        </span>
                                    @endif
                                </td> --}}
                                        <td class="align-middle">
                                            @if ($activity->fecha_recepcion)
                                                <div class="date-info">
                                                    <span class="badge badge-outline-info">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        {{ $activity->fecha_recepcion->format('d/m/Y') }}
                                                    </span>
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            {{ $activity->fecha_recepcion->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-calendar-times"></i> No asignada
                                                </span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="action-buttons">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('activities.edit', $activity) }}"
                                                        class="btn btn-warning btn-sm action-btn"
                                                        data-tooltip="Ver/Editar" title="Ver/Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}"
                                                        class="btn btn-secondary btn-sm action-btn"
                                                        data-tooltip="Crear Subactividad" title="Crear Subactividad">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                    <form action="{{ route('activities.destroy', $activity) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm action-btn"
                                                            data-tooltip="Eliminar" title="Eliminar"
                                                            onclick="return confirm('¿Estás seguro de eliminar esta actividad y todas sus subactividades?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- Mostrar subactividades (inicialmente ocultas) --}}
                                    @if ($activity->subactivities->count() > 0)
                                        @include('activities.partials.subactivities', [
                                            'subactivities' => $activity->subactivities,
                                            'parentId' => $activity->id,
                                            'level' => 1,
                                        ])
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No hay actividades registradas</h5>
                                                <p class="text-muted">Comienza creando tu primera actividad</p>
                                                <a href="{{ route('activities.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Crear Primera Actividad
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Estados -->
        <div class="modal fade" id="statusEditModal" tabindex="-1" role="dialog"
            aria-labelledby="statusEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="statusEditModalLabel">
                            <i class="fas fa-edit"></i> Editar Estados de Actividad
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">
                                    <i class="fas fa-info-circle text-primary"></i> Información de la Actividad
                                </h6>
                                <div class="activity-info">
                                    <p><strong>Caso:</strong> <span id="modalActivityCaso"></span></p>
                                    <p><strong>Nombre:</strong> <span id="modalActivityNombre"></span></p>
                                    <p><strong>Estados Actuales:</strong></p>
                                    <div id="modalCurrentStatuses" class="mb-3"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">
                                    <i class="fas fa-tasks text-primary"></i> Seleccionar Estados
                                </h6>
                                <div class="status-checkboxes" style="max-height: 400px; overflow-y: auto;">
                                    @foreach ($statuses as $status)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input status-checkbox" type="checkbox"
                                                value="{{ $status->id }}" data-status-name="{{ $status->name }}"
                                                id="status_{{ $status->id }}">
                                            <label class="form-check-label d-flex align-items-center"
                                                for="status_{{ $status->id }}">
                                                <span class="badge badge-pill mr-2"
                                                    style="background-color: {{ $status->color }}; color: white;">
                                                    <i class="fas fa-{{ $status->icon }}"></i> {{ $status->label }}
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="saveStatusChanges">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<style>
    /* ===== ESTILOS ESPECÍFICOS PARA LA VISTA DE ACTIVIDADES ===== */

    /* Header y estadísticas */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .text-gradient {
        background: linear-gradient(135deg, #007bff, #0056b3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Tarjetas de estadísticas */
    .stats-card {
        background: linear-gradient(135deg, var(--primary-color), #0056b3);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stats-card.bg-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }

    .stats-card.bg-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }

    .stats-card.bg-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }

    .stats-icon {
        font-size: 2.5rem;
        margin-right: 1rem;
        opacity: 0.8;
    }

    .stats-content h3 {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }

    .stats-content p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }

    /* Tabla moderna */
    .modern-table {
        font-size: 0.9rem;
    }

    /* Contenedor de tabla */
    #tableContainer {
        min-height: 400px;
        /* Altura mínima para evitar colapso al filtrar */
        transition: min-height 0.3s ease;
    }

    .modern-table thead th {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid #dee2e6;
    }

    .activity-row {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .activity-row:hover {
        background-color: #f8f9fa;
        border-left-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .activity-row td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #f1f3f4;
    }

    /* Badges mejorados */
    .badge-outline-primary {
        color: #007bff;
        border: 1px solid #007bff;
        background: rgba(0, 123, 255, 0.1);
    }

    .badge-outline-success {
        color: #28a745;
        border: 1px solid #28a745;
        background: rgba(40, 167, 69, 0.1);
    }

    .badge-outline-info {
        color: #17a2b8;
        border: 1px solid #17a2b8;
        background: rgba(23, 162, 184, 0.1);
    }

    .badge-pill {
        border-radius: 50px;
        padding: 0.5rem 1rem;
        font-weight: 500;
    }

    .badge-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    /* Información de analistas */
    .analysts-list .badge {
        margin: 0.1rem;
        font-size: 0.75rem;
    }

    /* Información de comentarios y correos */
    .comments-info,
    .emails-info,
    .date-info {
        text-align: center;
    }

    /* Botones de acción */
    .action-buttons .btn.btn-sm {
        margin: 0.1rem 0;
        border-radius: 6px;
        font-size: 0.8rem;
        padding: 0.2rem 0.4rem;
        transition: all 0.2s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Subactividades */
    .subactivity-row {
        display: none;
        background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
        border-left: 3px solid #007bff;
    }

    .subactivity-row.level-1 td:first-child {
        padding-left: 2rem;
    }

    .subactivity-row.level-2 td:first-child {
        padding-left: 3rem;
    }

    .subactivity-row.level-3 td:first-child {
        padding-left: 4rem;
    }

    .toggle-subactivities {
        transition: transform 0.3s ease;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.1);
    }

    .toggle-subactivities:hover {
        background: rgba(0, 123, 255, 0.2);
    }

    .toggle-subactivities.expanded {
        transform: rotate(90deg);
    }

    /* Estado vacío */
    .empty-state {
        padding: 3rem;
    }

    .empty-state i {
        opacity: 0.5;
    }

    /* Descripción con tooltip */
    .description-cell {
        max-width: 200px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 1rem;
        }

        .page-header {
            padding: 1rem;
        }

        .page-header .d-flex {
            flex-direction: column;
            text-align: center;
        }

        .action-buttons {
            margin-top: 1rem;
        }

        .modern-table {
            font-size: 0.8rem;
        }

        .activity-row td {
            padding: 0.5rem;
        }
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .activity-row {
        animation: fadeIn 0.5s ease-out;
    }

    /* Tooltips mejorados */
    [data-toggle="tooltip"] {
        cursor: help;
    }

    /* ===== ESTILOS PARA BÚSQUEDA Y FILTROS ===== */

    /* Contenedor de búsqueda */
    .search-container {
        position: relative;
    }

    .search-container .input-group {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .search-container .form-control {
        border: none;
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    .search-container .form-control:focus {
        box-shadow: none;
        border-color: transparent;
    }

    .search-container .input-group-text {
        background: #f8f9fa;
        border: none;
        color: #6c757d;
    }

    .search-container .btn {
        border: none;
        background: #f8f9fa;
    }

    .search-container .btn:hover {
        background: #e9ecef;
    }

    /* Sugerencias de búsqueda */
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }

    .search-suggestion-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f3f4;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-suggestion-item:hover {
        background-color: #f8f9fa;
    }

    .search-suggestion-item:last-child {
        border-bottom: none;
    }

    .search-suggestion-type {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 500;
    }

    .search-suggestion-content {
        font-weight: 500;
        color: #343a40;
    }

    .search-suggestion-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Filtros avanzados */
    .advanced-filters {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .advanced-filters .form-group label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .advanced-filters .form-control {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .advanced-filters .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Estadísticas de búsqueda */
    .search-stats {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        height: 100%;
    }

    /* Resultados de búsqueda */
    .search-result-highlight {
        background-color: rgba(255, 193, 7, 0.3);
        padding: 0.1rem 0.2rem;
        border-radius: 3px;
        font-weight: 500;
    }

    .search-result-row {
        background: linear-gradient(90deg, rgba(0, 123, 255, 0.05) 0%, rgba(255, 255, 255, 0.05) 100%);
        border-left: 3px solid #007bff;
    }

    .search-result-subactivity {
        background: linear-gradient(90deg, rgba(40, 167, 69, 0.05) 0%, rgba(255, 255, 255, 0.05) 100%);
        border-left: 3px solid #28a745;
    }

    /* Animaciones para filtros */
    .advanced-filters {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Estados de carga para búsqueda */
    .search-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .search-loading .table {
        position: relative;
    }

    .search-loading .table::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 10;
    }

    /* Responsive para búsqueda */
    @media (max-width: 768px) {
        .search-container .input-group {
            margin-bottom: 1rem;
        }

        .search-stats {
            justify-content: center;
        }

        .advanced-filters .row {
            margin: 0;
        }

        .advanced-filters .col-md-3,
        .advanced-filters .col-md-6 {
            padding: 0.5rem;
        }
    }

    /* Botones de filtro */
    #toggleFilters {
        transition: all 0.2s ease;
    }

    #toggleFilters:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Indicador de filtros activos */
    .filter-active {
        position: relative;
    }

    .filter-active::after {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
        border: 2px solid white;
    }

    /* Estilos para botones de filtro activos */
    .filter-toggle.active {
        background-color: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }

    .filter-toggle.active i {
        color: white !important;
    }

    /* Estilos para menús de filtro */
    .custom-dropdown-menu {
        padding: 0;
        overflow: hidden;
    }

    .custom-dropdown-menu .dropdown-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        margin: 0;
        padding: 0.75rem 1rem;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
    }

    .custom-dropdown-menu .px-3 {
        padding: 1rem;
        max-height: 300px;
        overflow-y: auto;
    }

    /* Estilos para checkboxes de filtro */
    .form-check {
        margin-bottom: 0.5rem;
    }

    .form-check-input:checked+.form-check-label {
        color: #007bff;
        font-weight: 500;
    }

    /* Estilos para los atajos de teclado */
    kbd {
        display: inline-block;
        padding: 2px 6px;
        font-size: 11px;
        line-height: 1.4;
        color: #555;
        background-color: #fcfcfc;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2), inset 0 0 0 2px #fff;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        font-weight: bold;
        margin: 0 2px;
    }

    kbd:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }
</style>



<!-- Script optimizado para modal de estados -->
<script src="{{ asset('js/status-modal-optimized.js') }}?v={{ time() }}&fix=button"></script>

<!-- Script para ordenamiento y filtros -->
<script src="{{ asset('js/activities-filters-sort.js') }}?v={{ time() }}"></script>

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
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="searchInput" 
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

            <!-- Advanced Filters (Initially Hidden) -->
            <div class="advanced-filters" id="advancedFilters" style="display: none;">
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterStatus" class="font-weight-bold">
                                <i class="fas fa-flag text-primary"></i> Estado
                            </label>
                            <select class="form-control" id="filterStatus" multiple>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->name }}" data-color="{{ $status->color }}">
                                        {{ $status->label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Mantén Ctrl para seleccionar múltiples estados</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterAnalista" class="font-weight-bold">
                                <i class="fas fa-user text-primary"></i> Analista
                            </label>
                            <select class="form-control" id="filterAnalista">
                                <option value="">Todos los analistas</option>
                                @foreach($analistas as $analista)
                                    <option value="{{ $analista->id }}">{{ $analista->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterCaso" class="font-weight-bold">
                                <i class="fas fa-hashtag text-primary"></i> Caso
                            </label>
                            <input type="text" class="form-control" id="filterCaso" placeholder="Buscar por caso...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-calendar text-primary"></i> Acciones
                            </label>
                            <div class="btn-group-vertical btn-group-sm w-100">
                                <button type="button" class="btn btn-outline-primary" id="clearAllFilters">
                                    <i class="fas fa-eraser"></i> Limpiar Filtros
                                </button>
                                <button type="button" class="btn btn-outline-info" id="exportResults">
                                    <i class="fas fa-download"></i> Exportar Resultados
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="filterFechaDesde" class="font-weight-bold">
                                <i class="fas fa-calendar-alt text-primary"></i> Fecha Desde
                            </label>
                            <input type="date" class="form-control" id="filterFechaDesde">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="filterFechaHasta" class="font-weight-bold">
                                <i class="fas fa-calendar-alt text-primary"></i> Fecha Hasta
                            </label>
                            <input type="date" class="form-control" id="filterFechaHasta">
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('no_iniciada'); })->count() }}</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('en_ejecucion'); })->count() }}</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('culminada'); })->count() }}</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('en_espera_de_insumos'); })->count() }}</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('en_certificacion_por_cliente'); })->count() }}</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('pases_enviados'); })->count() }}</h3>
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
                    <h3>{{ number_format(($activities->filter(function($activity) { return $activity->hasStatus('culminada'); })->count() / max($activities->count(), 1)) * 100, 1) }}%</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('pausada'); })->count() }}</h3>
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
                    <h3>{{ $activities->filter(function($activity) { return $activity->hasStatus('cancelada'); })->count() }}</h3>
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
                                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button" data-filter="status" style="padding: 2px 6px;">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                        <div class="custom-dropdown-menu" id="status-filter-menu" style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                                            <h6 class="dropdown-header" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">Filtrar por Estado</h6>
                                            <div class="px-3 py-2">
                                                <div class="form-check">
                                                    <input class="form-check-input status-filter" type="checkbox" value="" id="status-all" checked>
                                                    <label class="form-check-label" for="status-all">Todos</label>
                                                </div>
                                                @foreach($statuses as $key => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input status-filter" type="checkbox" value="{{ $key }}" id="status-{{ $key }}">
                                                    <label class="form-check-label" for="status-{{ $key }}">{{ $label }}</label>
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
                                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button" data-filter="analistas" style="padding: 2px 6px;">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                        <div class="custom-dropdown-menu" id="analistas-filter-menu" style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                                            <h6 class="dropdown-header" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">Filtrar por Analista</h6>
                                            <div class="px-3 py-2">
                                                <div class="form-check">
                                                    <input class="form-check-input analista-filter" type="checkbox" value="" id="analista-all" checked>
                                                    <label class="form-check-label" for="analista-all">Todos</label>
                                                </div>
                                                @foreach($analistas as $analista)
                                                <div class="form-check">
                                                    <input class="form-check-input analista-filter" type="checkbox" value="{{ $analista->id }}" id="analista-{{ $analista->id }}">
                                                    <label class="form-check-label" for="analista-{{ $analista->id }}">{{ $analista->name }}</label>
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
                                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button" data-filter="fecha" style="padding: 2px 6px;">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                        <div class="custom-dropdown-menu" id="fecha-filter-menu" style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 250px;">
                                            <h6 class="dropdown-header" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">Filtrar por Fecha</h6>
                                            <div class="px-3 py-2">
                                                <div class="form-group mb-2">
                                                    <label class="small">Desde:</label>
                                                    <input type="date" class="form-control form-control-sm" id="fecha-desde-filter">
                                                </div>
                                                <div class="form-group mb-2">
                                                    <label class="small">Hasta:</label>
                                                    <input type="date" class="form-control form-control-sm" id="fecha-hasta-filter">
                                                </div>
                                                <button class="btn btn-sm btn-primary btn-block" id="apply-date-filter">Aplicar</button>
                                                <button class="btn btn-sm btn-outline-secondary btn-block" id="clear-date-filter">Limpiar</button>
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
                                            <span class="toggle-subactivities mr-2" style="cursor: pointer;">
                                                <i class="fas fa-chevron-right text-primary" id="icon-{{ $activity->id }}"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $activity->name }}</div>
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
                                        {{ Str::limit($activity->description, 80) }}
                                        @if(strlen($activity->description) > 80)
                                            <span class="text-primary" style="cursor: pointer;" 
                                                  title="{{ $activity->description }}" 
                                                  data-toggle="tooltip">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="status-cell" data-activity-id="{{ $activity->id }}">
                                        <div class="status-display">
                                            @if($activity->statuses->count() > 0)
                                                @foreach($activity->statuses as $status)
                                                    <span class="badge badge-pill mr-1 mb-1" 
                                                          style="background-color: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                                        <i class="{{ $status->icon ?? 'fas fa-circle' }}"></i> {{ $status->label }}
                                                    </span>
                                                @endforeach
                                            @else
                                                {{-- Fallback al sistema anterior --}}
                                                @php
                                                    $statusClass = match($activity->status) {
                                                        'culminada' => 'success',
                                                        'en_ejecucion' => 'primary',
                                                        'en_espera_de_insumos' => 'warning',
                                                        default => 'secondary'
                                                    };
                                                    $statusIcon = match($activity->status) {
                                                        'culminada' => 'check-circle',
                                                        'en_ejecucion' => 'play-circle',
                                                        'en_espera_de_insumos' => 'pause-circle',
                                                        default => 'circle'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $statusClass }} badge-pill">
                                                    <i class="fas fa-{{ $statusIcon }}"></i> {{ $activity->status_label }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="status-edit-btn">
                                            <button class="btn btn-sm btn-outline-secondary edit-status-btn" 
                                                    data-activity-id="{{ $activity->id }}" 
                                                    title="Editar estados">
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
                                            <a href="{{ route('requirements.index', ['activity_id' => $activity->id]) }}" class="text-decoration-none">
                                                <span class="badge badge-warning badge-pill">
                                                    <i class="fas fa-clipboard-list"></i> {{ $activity->requirements->count() }}
                                                </span>
                                            </a>
                                            <div class="mt-1">
                                                @php
                                                    $pendientes = $activity->requirements->where('status', 'pendiente')->count();
                                                    $recibidos = $activity->requirements->where('status', 'recibido')->count();
                                                @endphp
                                                <small class="text-muted d-block">
                                                    <span class="badge badge-sm badge-warning">{{ $pendientes }} pendientes</span>
                                                    <span class="badge badge-sm badge-success">{{ $recibidos }} recibidos</span>
                                                </small>
                                                @if($activity->requirements->count() > 0)
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
                                                @if($lastEmail)
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
                                    @if($activity->fecha_recepcion)
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
                                               class="btn btn-warning btn-xs action-btn" 
                                               data-tooltip="Ver/Editar"
                                               title="Ver/Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" 
                                               class="btn btn-secondary btn-xs action-btn"
                                               data-tooltip="Crear Subactividad"
                                               title="Crear Subactividad">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <form action="{{ route('activities.destroy', $activity) }}" 
                                                  method="POST" 
                                                  style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-xs action-btn" 
                                                        data-tooltip="Eliminar"
                                                        title="Eliminar"
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
                                @include('activities.partials.subactivities', ['subactivities' => $activity->subactivities, 'parentId' => $activity->id, 'level' => 1])
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
    <div class="modal fade" id="statusEditModal" tabindex="-1" role="dialog" aria-labelledby="statusEditModalLabel" aria-hidden="true">
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
                                @foreach($statuses as $status)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input status-checkbox" 
                                               type="checkbox" 
                                               value="{{ $status->id }}" 
                                               data-status-name="{{ $status->name }}"
                                               id="status_{{ $status->id }}">
                                        <label class="form-check-label d-flex align-items-center" for="status_{{ $status->id }}">
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

<style>
/* ===== ESTILOS ESPECÍFICOS PARA LA VISTA DE ACTIVIDADES ===== */

/* Header y estadísticas */
.page-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
.comments-info, .emails-info, .date-info {
    text-align: center;
}

/* Botones de acción */
.action-buttons .btn {
    margin: 0.1rem 0;
    border-radius: 6px;
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    transition: all 0.2s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
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
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
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
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let searchTimeout;
    let originalTableContent;
    let isSearchActive = false;
    let currentSearchQuery = '';
    let currentFilters = {};

    // Inicializar tooltips de Bootstrap
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Guardar contenido original de la tabla
    originalTableContent = document.getElementById('tableContainer').innerHTML;
    


    // ===== FUNCIONALIDAD DE BÚSQUEDA ===== //

    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const searchResultsCount = document.getElementById('searchResultsCount');
    const resultsNumber = document.getElementById('resultsNumber');
    const searchResultsAlert = document.getElementById('searchResultsAlert');
    const searchResultsText = document.getElementById('searchResultsText');
    const tableTitle = document.getElementById('tableTitle');

    // Búsqueda en tiempo real
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        currentSearchQuery = query;
        
        // Limpiar timeout anterior
        clearTimeout(searchTimeout);
        
        if (query.length === 0) {
            clearSearch();
            return;
        }

        // Mostrar spinner
        searchSpinner.style.display = 'inline-block';
        searchResultsCount.style.display = 'none';

        // Debounce la búsqueda
        searchTimeout = setTimeout(function() {
            performSearch(query, currentFilters);
        }, 300);
    });

    // Limpiar búsqueda
    document.getElementById('clearSearch').addEventListener('click', clearSearch);
    document.getElementById('clearSearchResults').addEventListener('click', clearSearch);
    
    // Cancelar búsqueda con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isSearchActive) {
            clearSearch();
            searchInput.blur(); // Quitar foco del campo de búsqueda
        }
    });

    // Atajo de teclado para enfocar el campo de búsqueda (Ctrl+K)
    document.addEventListener('keydown', function(e) {
        // Ctrl+K para enfocar el campo de búsqueda
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault(); // Prevenir comportamiento por defecto del navegador
            searchInput.focus();
            searchInput.select(); // Seleccionar todo el texto si hay alguno
            
            // Mostrar una pequeña animación visual para indicar que se activó el atajo
            searchInput.style.boxShadow = '0 0 0 3px rgba(0, 123, 255, 0.25)';
            setTimeout(function() {
                searchInput.style.boxShadow = '';
            }, 300);
        }
        
        // También agregar Ctrl+F como alternativa (más familiar para algunos usuarios)
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            // Solo interceptar si no hay texto seleccionado (para no interferir con la búsqueda nativa del navegador)
            const selection = window.getSelection();
            if (!selection.toString()) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
                
                // Animación visual
                searchInput.style.boxShadow = '0 0 0 3px rgba(0, 123, 255, 0.25)';
                setTimeout(function() {
                    searchInput.style.boxShadow = '';
                }, 300);
            }
        }
    });

    function clearSearch() {
        searchInput.value = '';
        currentSearchQuery = '';
        searchSpinner.style.display = 'none';
        searchResultsCount.style.display = 'none';
        searchResultsAlert.style.display = 'none';
        document.getElementById('tableContainer').innerHTML = originalTableContent;
        tableTitle.textContent = 'Lista de Actividades';
        isSearchActive = false;
        setupToggleHandlers();
        updateStatistics();
    }

    // Realizar búsqueda AJAX con jQuery
    function performSearch(query, filters = {}) {
        // Preparar datos
        const data = { query: query };
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                data[key] = filters[key];
            }
        });
        
        $.ajax({
            url: '{{ route("activities.search") }}',
            method: 'GET',
            data: data,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data, textStatus, xhr) {
                try {
                    displaySearchResults(data, query);
                    searchSpinner.style.display = 'none';
                } catch (error) {
                    console.error('Error al mostrar resultados:', error);
                    searchSpinner.style.display = 'none';
                    showErrorMessage('Error al mostrar los resultados: ' + error.message);
                }
            },
            error: function(xhr, status, error) {
                searchSpinner.style.display = 'none';
                
                let errorMessage = 'Error al realizar la búsqueda. Inténtalo de nuevo.';
                if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMessage += ' Error: ' + (errorData.message || errorData.error || xhr.responseText);
                    } catch (e) {
                        errorMessage += ' Error: ' + xhr.responseText;
                    }
                } else {
                    errorMessage += ' Error: ' + error;
                }
                
                showErrorMessage(errorMessage);
            }
        });
    }

    // Mostrar resultados de búsqueda
    function displaySearchResults(data, query) {
        const { activities, subactivities, total_results } = data;
        
        // Actualizar contador
        resultsNumber.textContent = total_results;
        searchResultsCount.style.display = 'inline-block';
        
        // Mostrar alerta de resultados
        if (total_results > 0) {
            searchResultsText.textContent = `Se encontraron ${total_results} resultado(s) para "${query}"`;
            searchResultsAlert.style.display = 'block';
            tableTitle.textContent = `Resultados de búsqueda (${total_results})`;
        } else {
            searchResultsText.textContent = `No se encontraron resultados para "${query}"`;
            searchResultsAlert.style.display = 'block';
            tableTitle.textContent = 'Sin resultados';
        }

        // Generar HTML de resultados
        let resultsHTML = generateSearchResultsHTML(activities, subactivities, query);
        document.getElementById('tableContainer').innerHTML = resultsHTML;
        
        isSearchActive = true;
        setupToggleHandlers();
        highlightSearchTerms(query);
    }

    // Generar HTML para resultados de búsqueda
    function generateSearchResultsHTML(activities, subactivities, query) {
        if (activities.length === 0 && subactivities.length === 0) {
            return `
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron resultados</h5>
                    <p class="text-muted">Intenta con otros términos de búsqueda o ajusta los filtros</p>
                </div>
            `;
        }

        let html = `
            <table class="table table-hover mb-0 modern-table">
                <thead class="thead-light">
                    <tr>
                        <th class="border-0 sortable" data-sort="caso" style="cursor: pointer;">
                            <i class="fas fa-hashtag text-primary"></i> Caso
                            <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="nombre" style="cursor: pointer;">
                            <i class="fas fa-file-alt text-primary"></i> Nombre
                            <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                        </th>
                        <th class="border-0 sortable" data-sort="descripcion" style="cursor: pointer;">
                            <i class="fas fa-align-left text-primary"></i> Descripción
                            <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                        </th>
                        <th class="border-0" style="position: relative;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="sortable" data-sort="status" style="cursor: pointer;">
                                    <i class="fas fa-flag text-primary"></i> Estado
                                    <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" style="padding: 2px 6px;">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 200px;">
                                        <h6 class="dropdown-header">Filtrar por Estado</h6>
                                        <div class="px-3">
                                            <div class="form-check">
                                                <input class="form-check-input status-filter" type="checkbox" value="" id="status-all-search" checked>
                                                <label class="form-check-label" for="status-all-search">Todos</label>
                                            </div>
                                            @foreach($statuses as $key => $label)
                                            <div class="form-check">
                                                <input class="form-check-input status-filter" type="checkbox" value="{{ $key }}" id="status-search-{{ $key }}">
                                                <label class="form-check-label" for="status-search-{{ $key }}">{{ $label }}</label>
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" style="padding: 2px 6px;">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 200px;">
                                        <h6 class="dropdown-header">Filtrar por Analista</h6>
                                        <div class="px-3">
                                            <div class="form-check">
                                                <input class="form-check-input analista-filter" type="checkbox" value="" id="analista-all-search" checked>
                                                <label class="form-check-label" for="analista-all-search">Todos</label>
                                            </div>
                                            @foreach($analistas as $analista)
                                            <div class="form-check">
                                                <input class="form-check-input analista-filter" type="checkbox" value="{{ $analista->id }}" id="analista-search-{{ $analista->id }}">
                                                <label class="form-check-label" for="analista-search-{{ $analista->id }}">{{ $analista->name }}</label>
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" style="padding: 2px 6px;">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 250px;">
                                        <h6 class="dropdown-header">Filtrar por Fecha</h6>
                                        <div class="px-3">
                                            <div class="form-group mb-2">
                                                <label class="small">Desde:</label>
                                                <input type="date" class="form-control form-control-sm" id="fecha-desde-filter-search">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label class="small">Hasta:</label>
                                                <input type="date" class="form-control form-control-sm" id="fecha-hasta-filter-search">
                                            </div>
                                            <button class="btn btn-sm btn-primary btn-block" id="apply-date-filter-search">Aplicar</button>
                                            <button class="btn btn-sm btn-outline-secondary btn-block" id="clear-date-filter-search">Limpiar</button>
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
        `;

        // Agregar actividades principales
        activities.forEach(activity => {
            html += generateActivityRowHTML(activity, true);
        });

        // Agregar subactividades encontradas
        subactivities.forEach(subactivity => {
            html += generateSubactivityRowHTML(subactivity);
        });

        html += `
                </tbody>
            </table>
        `;

        return html;
    }

    // Generar HTML para fila de actividad
    function generateActivityRowHTML(activity, isSearchResult = false) {
        const statusClass = getStatusClass(activity.status);
        const statusIcon = getStatusIcon(activity.status);
        const rowClass = isSearchResult ? 'search-result-row' : '';
        
        return `
            <tr class="parent-activity activity-row ${rowClass}" data-activity-id="${activity.id}">
                <td class="align-middle">
                    <span class="badge badge-outline-primary font-weight-bold">${activity.caso}</span>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        ${activity.subactivities && activity.subactivities.length > 0 ? `
                            <span class="toggle-subactivities mr-2" style="cursor: pointer;">
                                <i class="fas fa-chevron-right text-primary" id="icon-${activity.id}"></i>
                            </span>
                        ` : ''}
                        <div>
                            <div class="font-weight-bold text-dark">${activity.name}</div>
                            ${activity.subactivities && activity.subactivities.length > 0 ? `
                                <small class="text-muted">
                                    <i class="fas fa-sitemap"></i> ${activity.subactivities.length} subactividad(es)
                                </small>
                            ` : ''}
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="description-cell">
                        ${activity.description ? (activity.description.length > 80 ? activity.description.substring(0, 80) + '...' : activity.description) : ''}
                    </div>
                </td>
                <td class="align-middle">
                    <span class="badge badge-${statusClass} badge-pill">
                        <i class="fas fa-${statusIcon}"></i> ${getStatusLabel(activity.status)}
                    </span>
                </td>
                <td class="align-middle">
                    ${generateAnalistasHTML(activity.analistas)}
                </td>
                {{-- <td class="align-middle">
                    ${generateCommentsHTML(activity)}
                </td> --}}
                <td class="align-middle">
                    ${generateRequirementsHTML(activity)}
                </td>
                {{-- <td class="align-middle">
                    ${generateEmailsHTML(activity)}
                </td> --}}
                <td class="align-middle">
                    ${generateDateHTML(activity.fecha_recepcion)}
                </td>
                <td class="align-middle text-center">
                    ${generateActionsHTML(activity)}
                </td>
            </tr>
        `;
    }

    // Generar HTML para subactividad en resultados
    function generateSubactivityRowHTML(subactivity) {
        const statusClass = getStatusClass(subactivity.status);
        const statusIcon = getStatusIcon(subactivity.status);
        
        return `
            <tr class="activity-row search-result-subactivity" data-activity-id="${subactivity.id}">
                <td class="align-middle">
                    <span class="badge badge-outline-success font-weight-bold">${subactivity.caso}</span>
                    <div><small class="text-muted">Subactividad de: ${subactivity.parent ? subactivity.parent.name : 'N/A'}</small></div>
                </td>
                <td class="align-middle">
                    <div class="font-weight-bold text-dark">${subactivity.name}</div>
                    <small class="text-success"><i class="fas fa-level-down-alt"></i> Subactividad</small>
                </td>
                <td class="align-middle">
                    <div class="description-cell">
                        ${subactivity.description ? (subactivity.description.length > 80 ? subactivity.description.substring(0, 80) + '...' : subactivity.description) : ''}
                    </div>
                </td>
                <td class="align-middle">
                    <span class="badge badge-${statusClass} badge-pill">
                        <i class="fas fa-${statusIcon}"></i> ${getStatusLabel(subactivity.status)}
                    </span>
                </td>
                <td class="align-middle">
                    ${generateAnalistasHTML(subactivity.analistas)}
                </td>
                {{-- <td class="align-middle">
                    ${generateCommentsHTML(subactivity)}
                </td> --}}
                <td class="align-middle">
                    ${generateRequirementsHTML(subactivity)}
                </td>
                {{-- <td class="align-middle">
                    ${generateEmailsHTML(subactivity)}
                </td> --}}
                <td class="align-middle">
                    ${generateDateHTML(subactivity.fecha_recepcion)}
                </td>
                <td class="align-middle text-center">
                    ${generateActionsHTML(subactivity)}
                </td>
            </tr>
        `;
    }

    // Funciones auxiliares para generar HTML
    function getStatusClass(status) {
        const statusClasses = {
            'no_iniciada': 'secondary',
            'en_ejecucion': 'primary',
            'en_espera_de_insumos': 'warning',
            'pausada': 'dark',
            'en_certificacion_por_cliente': 'warning',
            'pases_enviados': 'info',
            'culminada': 'success',
            'cancelada': 'danger',
            'en_revision': 'warning'
        };
        return statusClasses[status] || 'secondary';
    }

    function getStatusIcon(status) {
        const statusIcons = {
            'no_iniciada': 'clock',
            'en_ejecucion': 'play-circle',
            'en_espera_de_insumos': 'pause-circle',
            'pausada': 'pause',
            'en_certificacion_por_cliente': 'certificate',
            'pases_enviados': 'paper-plane',
            'culminada': 'check-circle',
            'cancelada': 'times-circle',
            'en_revision': 'eye'
        };
        return statusIcons[status] || 'circle';
    }

    function getStatusLabel(status) {
        const statusLabels = {
            'no_iniciada': 'No Iniciada',
            'en_ejecucion': 'En Ejecución',
            'en_espera_de_insumos': 'En Espera de Insumos',
            'pausada': 'Pausada',
            'en_certificacion_por_cliente': 'En Certificación por Cliente',
            'pases_enviados': 'Pases Enviados',
            'culminada': 'Culminada',
            'cancelada': 'Cancelada',
            'en_revision': 'En Revisión'
        };
        return statusLabels[status] || status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function generateAnalistasHTML(analistas) {
        if (!analistas || analistas.length === 0) {
            return '<span class="text-muted"><i class="fas fa-user-slash"></i> Sin asignar</span>';
        }
        
        let html = '<div class="analysts-list">';
        analistas.forEach(analista => {
            html += `<span class="badge badge-light mr-1 mb-1"><i class="fas fa-user"></i> ${analista.name}</span>`;
        });
        html += '</div>';
        return html;
    }

    function generateCommentsHTML(activity) {
        if (!activity.comments || activity.comments.length === 0) {
            return '<span class="text-muted"><i class="fas fa-comment-slash"></i> Sin comentarios</span>';
        }
        
        return `
            <div class="comments-info">
                <a href="/activities/${activity.id}/comments" class="text-decoration-none">
                    <span class="badge badge-info badge-pill">
                        <i class="fas fa-comments"></i> ${activity.comments.length}
                    </span>
                </a>
            </div>
        `;
    }

    function generateRequirementsHTML(activity) {
        if (!activity.requirements || activity.requirements.length === 0) {
            return '<span class="text-muted"><i class="fas fa-clipboard"></i> Sin requerimientos</span>';
        }
        
        const pendientes = activity.requirements.filter(req => req.status === 'pendiente').length;
        const recibidos = activity.requirements.filter(req => req.status === 'recibido').length;
        
        return `
            <div class="requirements-info">
                <a href="/requirements?activity_id=${activity.id}" class="text-decoration-none">
                    <span class="badge badge-warning badge-pill">
                        <i class="fas fa-clipboard-list"></i> ${activity.requirements.length}
                    </span>
                </a>
                <div class="mt-1">
                    <small class="text-muted d-block">
                        <span class="badge badge-sm badge-warning">${pendientes} pendientes</span>
                        <span class="badge badge-sm badge-success">${recibidos} recibidos</span>
                    </small>
                </div>
            </div>
        `;
    }

    function generateEmailsHTML(activity) {
        if (!activity.emails || activity.emails.length === 0) {
            return '<span class="text-muted"><i class="fas fa-envelope-open"></i> Sin correos</span>';
        }
        
        return `
            <div class="emails-info">
                <a href="/activities/${activity.id}/emails" class="text-decoration-none">
                    <span class="badge badge-success badge-pill">
                        <i class="fas fa-envelope"></i> ${activity.emails.length}
                    </span>
                </a>
            </div>
        `;
    }

    function generateDateHTML(fecha) {
        if (!fecha) {
            return '<span class="text-muted"><i class="fas fa-calendar-times"></i> No asignada</span>';
        }
        
        const date = new Date(fecha);
        // Formatear fecha como DD/MM/YYYY para consistencia con el formato del backend
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const formattedDate = `${day}/${month}/${year}`;
        
        return `
            <div class="date-info">
                <span class="badge badge-outline-info">
                    <i class="fas fa-calendar-alt"></i> ${formattedDate}
                </span>
            </div>
        `;
    }

    function generateActionsHTML(activity) {
        return `
            <div class="action-buttons">
                <div class="btn-group-vertical btn-group-sm" role="group">
                    <a href="/activities/${activity.id}/edit" class="btn btn-warning btn-sm" title="Editar actividad">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="/activities/${activity.id}/emails" class="btn btn-info btn-sm" title="Ver correos">
                        <i class="fas fa-envelope"></i> Correos
                    </a>
                    <a href="/activities/create?parentId=${activity.id}" class="btn btn-secondary btn-sm" title="Crear subactividad">
                        <i class="fas fa-plus"></i> Subactividad
                    </a>
                </div>
                <form action="/activities/${activity.id}" method="POST" style="display:inline;" class="mt-2">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar actividad"
                            onclick="return confirm('¿Estás seguro de eliminar esta actividad?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        `;
    }

    // Resaltar términos de búsqueda
    function highlightSearchTerms(query) {
        if (!query) return;
        
        const terms = query.toLowerCase().split(' ').filter(term => term.length > 0);
        const tableContainer = document.getElementById('tableContainer');
        
        terms.forEach(term => {
            highlightTerm(tableContainer, term);
        });
    }

    function highlightTerm(container, term) {
        const walker = document.createTreeWalker(
            container,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );

        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            textNodes.push(node);
        }

        textNodes.forEach(textNode => {
            const text = textNode.textContent;
            const regex = new RegExp(`(${term})`, 'gi');
            if (regex.test(text)) {
                const highlightedText = text.replace(regex, '<span class="search-result-highlight">$1</span>');
                const wrapper = document.createElement('span');
                wrapper.innerHTML = highlightedText;
                textNode.parentNode.replaceChild(wrapper, textNode);
            }
        });
    }

    // ===== FUNCIONALIDAD DE FILTROS ===== //

    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const advancedFilters = document.getElementById('advancedFilters');
    const filterToggleText = document.getElementById('filterToggleText');

    // Toggle filtros avanzados
    toggleFiltersBtn.addEventListener('click', function() {
        if (advancedFilters.style.display === 'none' || advancedFilters.style.display === '') {
            advancedFilters.style.display = 'block';
            filterToggleText.textContent = 'Ocultar Filtros';
            this.classList.add('filter-active');
        } else {
            advancedFilters.style.display = 'none';
            filterToggleText.textContent = 'Mostrar Filtros';
            this.classList.remove('filter-active');
        }
    });

    // Event listeners para filtros
    const filterElements = [
        'filterStatus',
        'filterAnalista', 
        'filterCaso',
        'filterFechaDesde',
        'filterFechaHasta'
    ];

    filterElements.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', applyFilters);
            element.addEventListener('input', applyFilters);
        }
    });

    // Aplicar filtros
    function applyFilters() {
        currentFilters = {
            status: document.getElementById('filterStatus').value,
            analista_id: document.getElementById('filterAnalista').value,
            caso: document.getElementById('filterCaso').value,
            fecha_desde: document.getElementById('filterFechaDesde').value,
            fecha_hasta: document.getElementById('filterFechaHasta').value
        };

        // Remover filtros vacíos
        Object.keys(currentFilters).forEach(key => {
            if (!currentFilters[key]) {
                delete currentFilters[key];
            }
        });

        // Si hay búsqueda activa, aplicar filtros a la búsqueda
        if (currentSearchQuery) {
            performSearch(currentSearchQuery, currentFilters);
        } else if (Object.keys(currentFilters).length > 0) {
            // Si no hay búsqueda pero sí filtros, realizar búsqueda solo con filtros
            performSearch('', currentFilters);
        } else {
            // Si no hay filtros ni búsqueda, mostrar todo
            clearSearch();
        }

        // Actualizar indicador de filtros activos
        updateFilterIndicator();
    }

    // Actualizar indicador de filtros activos
    function updateFilterIndicator() {
        const hasActiveFilters = Object.keys(currentFilters).length > 0;
        if (hasActiveFilters) {
            toggleFiltersBtn.classList.add('filter-active');
        } else {
            toggleFiltersBtn.classList.remove('filter-active');
        }
    }

    // Limpiar todos los filtros
    document.getElementById('clearAllFilters').addEventListener('click', function() {
        filterElements.forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.value = '';
            }
        });
        currentFilters = {};
        updateFilterIndicator();
        
        if (currentSearchQuery) {
            performSearch(currentSearchQuery, {});
        } else {
            clearSearch();
        }
    });

    // ===== FUNCIONALIDAD ORIGINAL DE SUBACTIVIDADES ===== //

    function setupToggleHandlers() {
        // Manejar el clic en las actividades padre para mostrar/ocultar subactividades
        document.querySelectorAll('.parent-activity').forEach(function(row) {
            const toggleIcon = row.querySelector('.toggle-subactivities');
            if (toggleIcon && !toggleIcon.hasAttribute('data-handler-attached')) {
                toggleIcon.setAttribute('data-handler-attached', 'true');
                toggleIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const activityId = row.getAttribute('data-activity-id');
                    const subactivities = document.querySelectorAll('.subactivity-row[data-parent-id="' + activityId + '"]');
                    const icon = document.getElementById('icon-' + activityId);
                    
                    if (subactivities.length > 0) {
                        const isVisible = subactivities[0].style.display !== 'none';
                        
                        subactivities.forEach(function(subRow, index) {
                            setTimeout(function() {
                                if (isVisible) {
                                    subRow.style.display = 'none';
                                    icon.className = 'fas fa-chevron-right text-primary';
                                    toggleIcon.classList.remove('expanded');
                                } else {
                                    subRow.style.display = 'table-row';
                                    icon.className = 'fas fa-chevron-down text-primary';
                                    toggleIcon.classList.add('expanded');
                                    
                                    // Inicializar tooltips para las subactividades mostradas
                                    setTimeout(function() {
                                        initializeSimpleTooltips();
                                    }, 200);
                                }
                            }, index * 50);
                        });
                    }
                });
            }
        });

        // Manejar subactividades anidadas
        document.querySelectorAll('.toggle-subactivities[data-subactivity-id]').forEach(function(toggle) {
            if (!toggle.hasAttribute('data-handler-attached')) {
                toggle.setAttribute('data-handler-attached', 'true');
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const subactivityId = this.getAttribute('data-subactivity-id');
                    const subactivities = document.querySelectorAll('.subactivity-row[data-parent-id="' + subactivityId + '"]');
                    const icon = document.getElementById('icon-sub-' + subactivityId);
                    
                    if (subactivities.length > 0) {
                        const isVisible = subactivities[0].style.display !== 'none';
                        
                        subactivities.forEach(function(subRow, index) {
                            setTimeout(function() {
                                if (isVisible) {
                                    subRow.style.display = 'none';
                                    icon.className = 'fas fa-chevron-right text-primary';
                                    toggle.classList.remove('expanded');
                                } else {
                                    subRow.style.display = 'table-row';
                                    icon.className = 'fas fa-chevron-down text-primary';
                                    toggle.classList.add('expanded');
                                    
                                    // Inicializar tooltips para las subactividades anidadas mostradas
                                    setTimeout(function() {
                                        initializeSimpleTooltips();
                                    }, 200);
                                }
                            }, index * 50);
                        });
                    }
                });
            }
        });
        
        // También configurar handlers de ordenamiento y filtros
        setupSortHandlers();
        setupColumnFilters();
    }

    // ===== FUNCIONES AUXILIARES ===== //

    function showErrorMessage(message) {
        // Crear y mostrar mensaje de error
        const errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger alert-dismissible fade show';
        errorAlert.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i> ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(errorAlert, container.firstChild);
        
        // Auto-dismiss después de 5 segundos
        setTimeout(() => {
            if (errorAlert.parentNode) {
                errorAlert.remove();
            }
        }, 5000);
    }

    function updateStatistics() {
        // Actualizar estadísticas si es necesario
        // Esta función se puede expandir para recalcular estadísticas
    }

    // ===== INICIALIZACIÓN ===== //

    // Configurar handlers inicialmente
    setupToggleHandlers();
    
    // Observer para cambios dinámicos en el DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                setupToggleHandlers();
                // Reinicializar tooltips después de cambios en el DOM
                setTimeout(function() {
                    if (typeof initializeSimpleTooltips === 'function') {
                        initializeSimpleTooltips();
                    }
                }, 300);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Animación de entrada para las tarjetas de estadísticas
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(function(card, index) {
        setTimeout(function() {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(function() {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });

    // Mejorar la experiencia de hover en las filas
    document.addEventListener('mouseenter', function(e) {
        if (e.target && typeof e.target.closest === 'function') {
            const row = e.target.closest('.activity-row');
            if (row) {
                row.style.transform = 'translateX(5px)';
            }
        }
    }, true);
    
    document.addEventListener('mouseleave', function(e) {
        if (e.target && typeof e.target.closest === 'function') {
            const row = e.target.closest('.activity-row');
            if (row) {
                row.style.transform = 'translateX(0)';
            }
        }
    }, true);

    // Auto-dismiss para alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.classList.contains('show')) {
                alert.classList.remove('show');
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 150);
            }
        }, 5000);
    });

    // ===== FUNCIONALIDAD DE ORDENAMIENTO Y FILTROS ===== //
    let currentSort = { column: null, direction: 'asc' };
    let activeFilters = {
        status: [],
        analistas: [],
        fechaDesde: null,
        fechaHasta: null
    };
    
    function setupSortHandlers() {
        const sortableHeaders = document.querySelectorAll('.sortable');
        sortableHeaders.forEach(header => {
            // Remover event listeners existentes para evitar duplicados
            header.removeEventListener('click', handleSort);
            header.addEventListener('click', handleSort);
        });
    }
    
    function handleSort(event) {
        event.stopPropagation(); // Evitar que se propague al dropdown
        const column = this.getAttribute('data-sort');
        sortTable(column);
    }
    
    function setupColumnFilters() {
        console.log('=== CONFIGURANDO FILTROS LIMPIOS ===');
        
        // Función global para toggle de dropdowns
        window.simpleToggle = function(filterType) {
            console.log('Simple toggle:', filterType);
            const menu = document.getElementById(`${filterType}-filter-menu`);
            
            if (menu) {
                // Cerrar otros dropdowns
                document.querySelectorAll('.custom-dropdown-menu').forEach(otherMenu => {
                    if (otherMenu.id !== `${filterType}-filter-menu`) {
                        otherMenu.style.display = 'none';
                    }
                });
                
                // Toggle actual
                const isVisible = menu.style.display === 'block';
                menu.style.display = isVisible ? 'none' : 'block';
                console.log(`Menu ${filterType}: ${isVisible ? 'cerrado' : 'abierto'}`);
            }
        }
        
        // Configurar botones con onclick directo
        setTimeout(() => {
            const statusBtn = document.querySelector('[data-filter="status"]');
            const analistasBtn = document.querySelector('[data-filter="analistas"]');
            const fechaBtn = document.querySelector('[data-filter="fecha"]');
            
            if (statusBtn) {
                statusBtn.onclick = function(e) {
                    e.stopPropagation();
                    console.log('STATUS CLICK');
                    simpleToggle('status');
                };
                console.log('Status button configurado');
            }
            
            if (analistasBtn) {
                analistasBtn.onclick = function(e) {
                    e.stopPropagation();
                    console.log('ANALISTAS CLICK');
                    simpleToggle('analistas');
                };
                console.log('Analistas button configurado');
            }
            
            if (fechaBtn) {
                fechaBtn.onclick = function(e) {
                    e.stopPropagation();
                    console.log('FECHA CLICK');
                    simpleToggle('fecha');
                };
                console.log('Fecha button configurado');
            }
        }, 100);
        
        // Cerrar dropdowns al hacer clic fuera (pero no al hacer clic en checkboxes)
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown') && !e.target.closest('.custom-dropdown-menu')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });
        
        // Configurar checkboxes con event delegation
        document.addEventListener('change', function(e) {
            if (e.target.matches('.status-filter')) {
                handleStatusChange(e.target);
            } else if (e.target.matches('.analista-filter')) {
                handleAnalistaChange(e.target);
            }
        });
        
        // Configurar botón limpiar filtros
        const clearButton = document.getElementById('clearAllColumnFilters');
        if (clearButton) {
            clearButton.onclick = function() {
                console.log('Limpiando todos los filtros...');
                clearAllFilters();
            };
        }
        
        // Configurar filtros de fecha
        setupDateFilters();
    }
    
    function setupDateFilters() {
        console.log('Configurando filtros de fecha...');
        
        // Configurar filtrado automático para campos de fecha del dropdown
        const fechaDesdeFilter = document.getElementById('fecha-desde-filter');
        const fechaHastaFilter = document.getElementById('fecha-hasta-filter');
        
        if (fechaDesdeFilter) {
            fechaDesdeFilter.addEventListener('change', function() {
                console.log('Fecha desde (dropdown) cambiada:', this.value);
                
                // Auto-completar fecha hasta si está vacía
                if (this.value && !fechaHastaFilter.value) {
                    fechaHastaFilter.value = this.value;
                    console.log('Auto-completando fecha hasta:', this.value);
                }
                
                // Aplicar filtro automáticamente SIN cerrar el dropdown
                applyDateFilterFromDropdownSilent();
            });
        }
        
        if (fechaHastaFilter) {
            fechaHastaFilter.addEventListener('change', function() {
                console.log('Fecha hasta (dropdown) cambiada:', this.value);
                
                // Aplicar filtro automáticamente SIN cerrar el dropdown
                applyDateFilterFromDropdownSilent();
            });
        }
        
        // Configurar botones de aplicar filtro de fecha (mantener para compatibilidad)
        const applyDateFilterBtn = document.getElementById('apply-date-filter');
        const clearDateFilterBtn = document.getElementById('clear-date-filter');
        
        if (applyDateFilterBtn) {
            applyDateFilterBtn.onclick = function(e) {
                e.preventDefault();
                applyDateFilterFromDropdown();
            };
        }
        
        if (clearDateFilterBtn) {
            clearDateFilterBtn.onclick = function(e) {
                e.preventDefault();
                console.log('Limpiando filtro de fecha...');
                
                // Limpiar campos
                document.getElementById('fecha-desde-filter').value = '';
                document.getElementById('fecha-hasta-filter').value = '';
                
                // Limpiar filtros activos
                activeFilters.fechaDesde = null;
                activeFilters.fechaHasta = null;
                
                // Aplicar filtros
                throttledApplyFilters();
                updateFilterIndicators();
                
                // Cerrar dropdown
                document.getElementById('fecha-filter-menu').style.display = 'none';
            };
        }
        
        // También configurar los filtros de fecha de la sección de búsqueda
        const fechaDesdeSearchFilter = document.getElementById('fecha-desde-filter-search');
        const fechaHastaSearchFilter = document.getElementById('fecha-hasta-filter-search');
        
        if (fechaDesdeSearchFilter) {
            fechaDesdeSearchFilter.addEventListener('change', function() {
                console.log('Fecha desde (búsqueda) cambiada:', this.value);
                
                // Auto-completar fecha hasta si está vacía
                if (this.value && fechaHastaSearchFilter && !fechaHastaSearchFilter.value) {
                    fechaHastaSearchFilter.value = this.value;
                    console.log('Auto-completando fecha hasta (búsqueda):', this.value);
                }
                
                // Sincronizar y aplicar filtro automáticamente SIN cerrar dropdown
                applyDateFilterFromSearchSilent();
            });
        }
        
        if (fechaHastaSearchFilter) {
            fechaHastaSearchFilter.addEventListener('change', function() {
                console.log('Fecha hasta (búsqueda) cambiada:', this.value);
                
                // Aplicar filtro automáticamente SIN cerrar dropdown
                applyDateFilterFromSearchSilent();
            });
        }
        
        const applyDateFilterSearchBtn = document.getElementById('apply-date-filter-search');
        const clearDateFilterSearchBtn = document.getElementById('clear-date-filter-search');
        
        if (applyDateFilterSearchBtn) {
            applyDateFilterSearchBtn.onclick = function(e) {
                e.preventDefault();
                applyDateFilterFromSearch();
            };
        }
        
        if (clearDateFilterSearchBtn) {
            clearDateFilterSearchBtn.onclick = function(e) {
                e.preventDefault();
                console.log('Limpiando filtro de fecha desde búsqueda...');
                
                // Limpiar campos de búsqueda
                document.getElementById('fecha-desde-filter-search').value = '';
                document.getElementById('fecha-hasta-filter-search').value = '';
                
                // Sincronizar con los filtros principales
                if (document.getElementById('fecha-desde-filter')) {
                    document.getElementById('fecha-desde-filter').value = '';
                }
                if (document.getElementById('fecha-hasta-filter')) {
                    document.getElementById('fecha-hasta-filter').value = '';
                }
                
                // Limpiar filtros activos
                activeFilters.fechaDesde = null;
                activeFilters.fechaHasta = null;
                
                // Aplicar filtros
                throttledApplyFilters();
                updateFilterIndicators();
            };
        }
        
        // Configurar filtros de fecha de la sección avanzada
        const filterFechaDesde = document.getElementById('filterFechaDesde');
        const filterFechaHasta = document.getElementById('filterFechaHasta');
        
        if (filterFechaDesde) {
            filterFechaDesde.addEventListener('change', function() {
                console.log('Filtro fecha desde (avanzado) cambiado:', this.value);
                
                // Auto-completar fecha hasta si está vacía
                const filterFechaHasta = document.getElementById('filterFechaHasta');
                if (this.value && filterFechaHasta && !filterFechaHasta.value) {
                    filterFechaHasta.value = this.value;
                    console.log('Auto-completando fecha hasta (avanzado):', this.value);
                }
                
                activeFilters.fechaDesde = this.value || null;
                
                // Sincronizar con filtros de dropdown
                if (document.getElementById('fecha-desde-filter')) {
                    document.getElementById('fecha-desde-filter').value = this.value;
                }
                if (document.getElementById('fecha-desde-filter-search')) {
                    document.getElementById('fecha-desde-filter-search').value = this.value;
                }
                
                // Actualizar también el filtro hasta si se cambió
                if (filterFechaHasta && filterFechaHasta.value) {
                    activeFilters.fechaHasta = filterFechaHasta.value;
                }
                
                throttledApplyFilters();
                updateFilterIndicators();
            });
        }
        
        if (filterFechaHasta) {
            filterFechaHasta.addEventListener('change', function() {
                console.log('Filtro fecha hasta (avanzado) cambiado:', this.value);
                activeFilters.fechaHasta = this.value || null;
                
                // Sincronizar con filtros de dropdown
                if (document.getElementById('fecha-hasta-filter')) {
                    document.getElementById('fecha-hasta-filter').value = this.value;
                }
                if (document.getElementById('fecha-hasta-filter-search')) {
                    document.getElementById('fecha-hasta-filter-search').value = this.value;
                }
                
                throttledApplyFilters();
                updateFilterIndicators();
            });
        }
        
        console.log('Filtros de fecha configurados correctamente');
    }
    
    function applyDateFilterFromDropdown() {
        const fechaDesde = document.getElementById('fecha-desde-filter').value;
        const fechaHasta = document.getElementById('fecha-hasta-filter').value;
        
        console.log('Aplicando filtro de fecha desde dropdown:', { fechaDesde, fechaHasta });
        
        // Actualizar filtros activos
        activeFilters.fechaDesde = fechaDesde || null;
        activeFilters.fechaHasta = fechaHasta || null;
        
        // Sincronizar con filtros avanzados
        const filterFechaDesde = document.getElementById('filterFechaDesde');
        const filterFechaHasta = document.getElementById('filterFechaHasta');
        if (filterFechaDesde) filterFechaDesde.value = fechaDesde;
        if (filterFechaHasta) filterFechaHasta.value = fechaHasta;
        
        // Sincronizar con filtros de búsqueda
        const fechaDesdeSearch = document.getElementById('fecha-desde-filter-search');
        const fechaHastaSearch = document.getElementById('fecha-hasta-filter-search');
        if (fechaDesdeSearch) fechaDesdeSearch.value = fechaDesde;
        if (fechaHastaSearch) fechaHastaSearch.value = fechaHasta;
        
        // Aplicar filtros
        throttledApplyFilters();
        updateFilterIndicators();
        
        // Cerrar dropdown de fecha
        const fechaFilterMenu = document.getElementById('fecha-filter-menu');
        if (fechaFilterMenu) {
            fechaFilterMenu.style.display = 'none';
        }
    }
    
    function applyDateFilterFromDropdownSilent() {
        const fechaDesde = document.getElementById('fecha-desde-filter').value;
        const fechaHasta = document.getElementById('fecha-hasta-filter').value;
        
        console.log('Aplicando filtro de fecha desde dropdown (silencioso):', { fechaDesde, fechaHasta });
        
        // Actualizar filtros activos
        activeFilters.fechaDesde = fechaDesde || null;
        activeFilters.fechaHasta = fechaHasta || null;
        
        // Sincronizar con filtros avanzados
        const filterFechaDesde = document.getElementById('filterFechaDesde');
        const filterFechaHasta = document.getElementById('filterFechaHasta');
        if (filterFechaDesde) filterFechaDesde.value = fechaDesde;
        if (filterFechaHasta) filterFechaHasta.value = fechaHasta;
        
        // Sincronizar con filtros de búsqueda
        const fechaDesdeSearch = document.getElementById('fecha-desde-filter-search');
        const fechaHastaSearch = document.getElementById('fecha-hasta-filter-search');
        if (fechaDesdeSearch) fechaDesdeSearch.value = fechaDesde;
        if (fechaHastaSearch) fechaHastaSearch.value = fechaHasta;
        
        // Aplicar filtros SIN cerrar el dropdown
        throttledApplyFilters();
        updateFilterIndicators();
    }
    
    function applyDateFilterFromSearch() {
        const fechaDesde = document.getElementById('fecha-desde-filter-search').value;
        const fechaHasta = document.getElementById('fecha-hasta-filter-search').value;
        
        console.log('Aplicando filtro de fecha desde búsqueda:', { fechaDesde, fechaHasta });
        
        // Actualizar filtros activos
        activeFilters.fechaDesde = fechaDesde || null;
        activeFilters.fechaHasta = fechaHasta || null;
        
        // Sincronizar con otros filtros
        const fechaDesdeFilter = document.getElementById('fecha-desde-filter');
        const fechaHastaFilter = document.getElementById('fecha-hasta-filter');
        if (fechaDesdeFilter) fechaDesdeFilter.value = fechaDesde;
        if (fechaHastaFilter) fechaHastaFilter.value = fechaHasta;
        
        const filterFechaDesde = document.getElementById('filterFechaDesde');
        const filterFechaHasta = document.getElementById('filterFechaHasta');
        if (filterFechaDesde) filterFechaDesde.value = fechaDesde;
        if (filterFechaHasta) filterFechaHasta.value = fechaHasta;
        
        // Aplicar filtros
        throttledApplyFilters();
        updateFilterIndicators();
        
        // Cerrar dropdown de fecha de búsqueda si existe
        const fechaSearchFilterMenu = document.getElementById('fecha-search-filter-menu');
        if (fechaSearchFilterMenu) {
            fechaSearchFilterMenu.style.display = 'none';
        }
    }
    
    function applyDateFilterFromSearchSilent() {
        const fechaDesde = document.getElementById('fecha-desde-filter-search').value;
        const fechaHasta = document.getElementById('fecha-hasta-filter-search').value;
        
        console.log('Aplicando filtro de fecha desde búsqueda (silencioso):', { fechaDesde, fechaHasta });
        
        // Actualizar filtros activos
        activeFilters.fechaDesde = fechaDesde || null;
        activeFilters.fechaHasta = fechaHasta || null;
        
        // Sincronizar con otros filtros
        const fechaDesdeFilter = document.getElementById('fecha-desde-filter');
        const fechaHastaFilter = document.getElementById('fecha-hasta-filter');
        if (fechaDesdeFilter) fechaDesdeFilter.value = fechaDesde;
        if (fechaHastaFilter) fechaHastaFilter.value = fechaHasta;
        
        const filterFechaDesde = document.getElementById('filterFechaDesde');
        const filterFechaHasta = document.getElementById('filterFechaHasta');
        if (filterFechaDesde) filterFechaDesde.value = fechaDesde;
        if (filterFechaHasta) filterFechaHasta.value = fechaHasta;
        
        // Aplicar filtros SIN cerrar el dropdown
        throttledApplyFilters();
        updateFilterIndicators();
    }
    
    // Throttle para evitar filtrados excesivos
    let filterTimeout = null;
    
    function throttledApplyFilters() {
        if (filterTimeout) {
            clearTimeout(filterTimeout);
        }
        filterTimeout = setTimeout(() => {
            applyFilters();
        }, 150); // Esperar 150ms antes de aplicar filtros
    }
    
    function handleStatusChange(checkbox) {
        console.log('Status filter changed:', checkbox.value, checkbox.checked);
        
        if (checkbox.value === '') {
            // Checkbox "Todos"
            if (checkbox.checked) {
                activeFilters.status = [];
                document.querySelectorAll('.status-filter').forEach(cb => {
                    if (cb.value !== '') cb.checked = false;
                });
            }
        } else {
            // Checkbox específico
            const allCheckbox = document.getElementById('status-all');
            if (allCheckbox) allCheckbox.checked = false;
            
            if (checkbox.checked) {
                if (!activeFilters.status.includes(checkbox.value)) {
                    activeFilters.status.push(checkbox.value);
                }
            } else {
                activeFilters.status = activeFilters.status.filter(s => s !== checkbox.value);
            }
        }
        
        console.log('Active status filters:', activeFilters.status);
        throttledApplyFilters();
        updateFilterIndicators();
    }
    
    function handleAnalistaChange(checkbox) {
        console.log('Analista filter changed:', checkbox.value, checkbox.checked);
        
        if (checkbox.value === '') {
            // Checkbox "Todos"
            if (checkbox.checked) {
                activeFilters.analistas = [];
                document.querySelectorAll('.analista-filter').forEach(cb => {
                    if (cb.value !== '') cb.checked = false;
                });
            }
        } else {
            // Checkbox específico
            const allCheckbox = document.getElementById('analista-all');
            if (allCheckbox) allCheckbox.checked = false;
            
            if (checkbox.checked) {
                if (!activeFilters.analistas.includes(checkbox.value)) {
                    activeFilters.analistas.push(checkbox.value);
                }
            } else {
                activeFilters.analistas = activeFilters.analistas.filter(a => a !== checkbox.value);
            }
        }
        
        console.log('Active analista filters:', activeFilters.analistas);
        throttledApplyFilters();
        updateFilterIndicators();
    }
    
    function clearAllFilters() {
        console.log('Limpiando todos los filtros...');
        
        // Limpiar filtros de estado
        document.querySelectorAll('.status-filter').forEach(cb => {
            if (cb.value === '') {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
        });
        
        // Limpiar filtros de analistas
        document.querySelectorAll('.analista-filter').forEach(cb => {
            if (cb.value === '') {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
        });
        
        // Limpiar filtros de fecha
        const fechaDesdeInput = document.getElementById('fecha-desde-filter');
        const fechaHastaInput = document.getElementById('fecha-hasta-filter');
        if (fechaDesdeInput) fechaDesdeInput.value = '';
        if (fechaHastaInput) fechaHastaInput.value = '';
        
        // Limpiar filtros de fecha de la sección avanzada
        const filterFechaDesde = document.getElementById('filterFechaDesde');
        const filterFechaHasta = document.getElementById('filterFechaHasta');
        if (filterFechaDesde) filterFechaDesde.value = '';
        if (filterFechaHasta) filterFechaHasta.value = '';
        
        // Limpiar filtros de fecha de búsqueda
        const fechaDesdeSearchInput = document.getElementById('fecha-desde-filter-search');
        const fechaHastaSearchInput = document.getElementById('fecha-hasta-filter-search');
        if (fechaDesdeSearchInput) fechaDesdeSearchInput.value = '';
        if (fechaHastaSearchInput) fechaHastaSearchInput.value = '';
        
        // Resetear filtros activos
        activeFilters = {
            status: [],
            analistas: [],
            fechaDesde: null,
            fechaHasta: null
        };
        
        // Limpiar caches para mejorar rendimiento
        clearFilterCaches();
        
        // Aplicar filtros para mostrar todas las filas
        throttledApplyFilters();
        
        // Cerrar todos los dropdowns
        document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
        
        // Mostrar todas las filas directamente
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = '';
        });
        
        // Limpiar indicadores visuales
        document.querySelectorAll('.filter-toggle').forEach(button => {
            button.classList.remove('active');
        });
        
        // Actualizar contador
        updateResultsCount();
        
        console.log('Filtros limpiados correctamente');
    }
    
    function updateResultsCount() {
        const visibleRows = document.querySelectorAll('tbody tr[style=""], tbody tr:not([style*="display: none"])');
        const totalRows = document.querySelectorAll('tbody tr').length;
        
        console.log(`Mostrando ${visibleRows.length} de ${totalRows} actividades`);
        
        // Actualizar título de la tabla si existe
        const tableTitle = document.getElementById('tableTitle');
        if (tableTitle) {
            if (visibleRows.length === totalRows) {
                tableTitle.textContent = 'Lista de Actividades';
            } else {
                tableTitle.textContent = `Actividades filtradas (${visibleRows.length} de ${totalRows})`;
            }
        }
    }
    
    function sortTable(column) {
        // Determinar dirección de ordenamiento
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.direction = 'asc';
        }
        currentSort.column = column;
        
        // Actualizar iconos de ordenamiento
        updateSortIcons(column, currentSort.direction);
        
        // Obtener filas de la tabla
        const tableBody = document.querySelector('#tableContainer tbody');
        if (!tableBody) return;
        
        const rows = Array.from(tableBody.querySelectorAll('tr.parent-activity'));
        
        // Ordenar filas
        rows.sort((a, b) => {
            let aValue = getSortValue(a, column);
            let bValue = getSortValue(b, column);
            
            // Convertir a números si es posible
            const aNum = parseFloat(aValue);
            const bNum = parseFloat(bValue);
            if (!isNaN(aNum) && !isNaN(bNum)) {
                aValue = aNum;
                bValue = bNum;
            }
            
            // Manejar fechas
            if (column === 'fecha_recepcion') {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            }
            
            if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
            if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });
        
        // Reordenar filas en el DOM
        rows.forEach(row => {
            tableBody.appendChild(row);
            // También mover las subactividades si existen
            const activityId = row.getAttribute('data-activity-id');
            const subRows = tableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${activityId}"]`);
            subRows.forEach(subRow => {
                tableBody.appendChild(subRow);
            });
        });
    }
    
    function getSortValue(row, column) {
        const cells = row.querySelectorAll('td');
        let value = '';
        
        switch(column) {
            case 'caso':
                value = cells[0]?.textContent?.trim() || '';
                break;
            case 'nombre':
                value = cells[1]?.textContent?.trim() || '';
                break;
            case 'descripcion':
                value = cells[2]?.textContent?.trim() || '';
                break;
            case 'status':
                value = cells[3]?.textContent?.trim() || '';
                break;
            case 'analistas':
                value = cells[4]?.textContent?.trim() || '';
                break;
            case 'fecha_recepcion':
                value = cells[7]?.textContent?.trim() || '';
                break;
            default:
                value = '';
        }
        
        return value.toLowerCase();
    }
    
    // Cache para elementos DOM
    let tableBodyCache = null;
    let rowsCache = null;
    let lastTableHTML = '';
    
    function applyFilters() {
        console.log('Aplicando filtros optimizados...', activeFilters);
        
        // Usar cache del tbody si está disponible
        if (!tableBodyCache) {
            tableBodyCache = document.querySelector('#tableContainer tbody');
        }
        
        if (!tableBodyCache) {
            console.error('No se encontró tbody');
            return;
        }
        
        // Verificar si la tabla cambió (para invalidar cache)
        const currentTableHTML = tableBodyCache.innerHTML;
        if (currentTableHTML !== lastTableHTML) {
            rowsCache = null;
            lastTableHTML = currentTableHTML;
        }
        
        // Usar cache de filas si está disponible
        if (!rowsCache) {
            rowsCache = Array.from(tableBodyCache.querySelectorAll('tr'));
        }
        
        console.log('Filas en cache:', rowsCache.length);
        let visibleCount = 0;
        
        // Usar requestAnimationFrame para no bloquear la UI
        const processRows = (startIndex = 0) => {
            const batchSize = 50; // Procesar 50 filas por lote
            const endIndex = Math.min(startIndex + batchSize, rowsCache.length);
            
            for (let i = startIndex; i < endIndex; i++) {
                const row = rowsCache[i];
                let shouldShow = true;
                const cells = row.querySelectorAll('td');
                
                // Filtro por estado (optimizado)
                if (activeFilters.status.length > 0 && cells[3]) {
                    const statusText = cells[3].textContent.trim().toLowerCase();
                    const statusMatch = activeFilters.status.some(status => {
                        switch(status) {
                            case 'en_espera_de_insumos':
                                return statusText.includes('espera') || statusText.includes('insumos');
                            case 'en_ejecucion':
                                return statusText.includes('ejecución') || statusText.includes('ejecutando');
                            default:
                                return statusText.includes(status.toLowerCase());
                        }
                    });
                }
            }
        }
    }

    </script>

    <!-- Script optimizado para modal de estados -->
    <script src="{{ asset('js/status-modal-optimized.js') }}?v={{ time() }}&fix=button"></script>
    
    <!-- Script para cargar datos de actividades -->
    <script>
        // Cargar datos de actividades desde PHP
        document.addEventListener('DOMContentLoaded', function() {
            const activitiesData = @json($activities);
            
            // Procesar datos usando la función del archivo externo
            if (typeof processActivitiesData === 'function') {
                processActivitiesData(activitiesData);
            } else {
                console.error('Función processActivitiesData no encontrada');
            }
        });
    </script>
</div>
@endsection
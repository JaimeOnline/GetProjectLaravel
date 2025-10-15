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
                <div class="header-action-buttons">
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
                    <button class="btn btn-primary btn-sm" id="toggleFilters">
                        <i class="fas fa-filter"></i> <span id="filterToggleText">Mostrar Filtros</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Botón de exportar -->
                <div class="mb-3 d-flex justify-content-end">
                    <form id="exportForm" method="GET" action="{{ route('activities.export') }}" target="_blank">
                        <input type="hidden" name="status" id="exportStatus">
                        <input type="hidden" name="analista_id" id="exportAnalista">
                        <input type="hidden" name="fecha_desde" id="exportFechaDesde">
                        <input type="hidden" name="fecha_hasta" id="exportFechaHasta">
                        <input type="hidden" name="query" id="exportQuery">
                        <!-- INPUTS PARA FILTROS DE COLUMNA -->
                        <input type="hidden" name="status_column" id="exportStatusColumn">
                        <input type="hidden" name="analista_column" id="exportAnalistaColumn">
                        <input type="hidden" name="fecha_desde_column" id="exportFechaDesdeColumn">
                        <input type="hidden" name="fecha_hasta_column" id="exportFechaHastaColumn">
                        <button type="submit" class="btn btn-outline-success">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                        <button type="button" id="exportWordBtn" class="btn btn-outline-primary ml-2">
                            <i class="fas fa-file-word"></i> Exportar Word
                        </button>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const exportForm = document.getElementById('exportForm');
                            exportForm.addEventListener('submit', function(e) {
                                // Filtros avanzados
                                document.getElementById('exportStatus').value = document.getElementById('filterStatus')
                                    .value;
                                document.getElementById('exportAnalista').value = document.getElementById('filterAnalista')
                                    .value;
                                document.getElementById('exportFechaDesde').value = document.getElementById(
                                    'filterFechaDesde').value;
                                document.getElementById('exportFechaHasta').value = document.getElementById(
                                    'filterFechaHasta').value;
                                document.getElementById('exportQuery').value = document.getElementById('searchInput').value;

                                // Filtros de columna (checkboxes)
                                // Estado columna
                                let statusChecked = Array.from(document.querySelectorAll('.status-filter:checked'))
                                    .filter(el => el.value !== "")
                                    .map(el => el.value);
                                document.getElementById('exportStatusColumn').value = statusChecked.join(',');

                                // Analista columna
                                let analistaChecked = Array.from(document.querySelectorAll('.analista-filter:checked'))
                                    .filter(el => el.value !== "")
                                    .map(el => el.value);
                                document.getElementById('exportAnalistaColumn').value = analistaChecked.join(',');

                                // Fechas columna
                                document.getElementById('exportFechaDesdeColumn').value = document.getElementById(
                                    'fecha-desde-filter').value;
                                document.getElementById('exportFechaHastaColumn').value = document.getElementById(
                                    'fecha-hasta-filter').value;
                            });
                        });
                    </script>
                </div>
                </script>
                <script>
                    document.getElementById('exportWordBtn').addEventListener('click', function() {
                        // Toma los mismos valores que el submit de Excel
                        const params = new URLSearchParams();
                        params.set('status', document.getElementById('filterStatus').value);
                        params.set('analista_id', document.getElementById('filterAnalista').value);
                        params.set('fecha_desde', document.getElementById('filterFechaDesde').value);
                        params.set('fecha_hasta', document.getElementById('filterFechaHasta').value);
                        params.set('query', document.getElementById('searchInput').value);

                        // Filtros de columna
                        let statusChecked = Array.from(document.querySelectorAll('.status-filter:checked'))
                            .filter(el => el.value !== "")
                            .map(el => el.value);
                        params.set('status_column', statusChecked.join(','));

                        let analistaChecked = Array.from(document.querySelectorAll('.analista-filter:checked'))
                            .filter(el => el.value !== "")
                            .map(el => el.value);
                        params.set('analista_column', analistaChecked.join(','));

                        params.set('fecha_desde_column', document.getElementById('fecha-desde-filter').value);
                        params.set('fecha_hasta_column', document.getElementById('fecha-hasta-filter').value);

                        // Redirige a la ruta de exportación Word con los filtros
                        window.open("{{ route('activities.exportWord') }}?" + params.toString(), "_blank");
                    });
                </script>
            </div>
            <!-- Search Bar -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filterProyecto">Proyecto:</label>
                    <select class="form-control" id="filterProyecto" name="filterProyecto"
                        onchange="window.location='?proyecto_id='+this.value">
                        <option value="">Todos</option>
                        @foreach ($proyectos as $proyecto)
                            <option value="{{ $proyecto->id }}"
                                {{ request('proyecto_id') == $proyecto->id ? 'selected' : '' }}>
                                {{ $proyecto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
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
        <!-- Filtros avanzados (inicialmente ocultos) -->
        <div id="filtersSection" style="display: none; margin-bottom: 1rem;">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="filterStatus">Estado:</label>
                        <select class="form-control" id="filterStatus">
                            <option value="">Todos</option>
                            @foreach ($statusLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterAnalista">Analista:</label>
                        <select class="form-control" id="filterAnalista">
                            <option value="">Todos</option>
                            @foreach ($analistas as $analista)
                                <option value="{{ $analista->id }}">{{ $analista->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterFechaDesde">Fecha Desde:</label>
                        <input type="date" class="form-control" id="filterFechaDesde">
                        <label for="filterFechaHasta" class="mt-2">Fecha Hasta:</label>
                        <input type="date" class="form-control" id="filterFechaHasta">
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
                        <p>En Espera de Insumos</p>
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

    {{-- Script para editar Prioridad y Orden en tabla --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Al hacer click en el valor, mostrar input
            document.querySelectorAll('.editable-cell .editable-value').forEach(function(span) {
                span.addEventListener('click', function() {
                    const cell = span.closest('.editable-cell');
                    const input = cell.querySelector('.editable-input');
                    span.style.display = 'none';
                    input.style.display = 'inline-block';
                    input.focus();
                    input.select();
                });
            });

            // Al perder foco o presionar Enter, enviar AJAX
            document.querySelectorAll('.editable-cell .editable-input').forEach(function(input) {
                input.addEventListener('blur', saveInlineEdit);
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        saveInlineEdit.call(input, e);
                    }
                });
            });

            function saveInlineEdit(e) {
                const input = this;
                const cell = input.closest('.editable-cell');
                const span = cell.querySelector('.editable-value');
                const activityId = cell.getAttribute('data-activity-id');
                const field = cell.getAttribute('data-field');
                const value = input.value;

                fetch(`/activities/${activityId}/inline-update`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            field,
                            value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            span.textContent = value;
                        } else {
                            alert('Error al actualizar');
                        }
                        input.style.display = 'none';
                        span.style.display = 'inline-block';
                    })
                    .catch(() => {
                        alert('Error al actualizar');
                        input.style.display = 'none';
                        span.style.display = 'inline-block';
                    });
            }
        });
    </script>

    <!-- Search Results Alert -->
    <div class="alert alert-info" id="searchResultsAlert" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-search mr-2"></i>
            <div>
                <strong>Resultados de búsqueda:</strong>
                <span id="searchResultsText"></span>
                {{-- <button class="btn btn-sm btn-outline-info ml-2" id="showAllResults">
                        <i class="fas fa-eye"></i> Ver todos los resultados
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ml-1" id="clearSearchResults">
                        <i class="fas fa-times"></i> Limpiar búsqueda
                    </button> --}}
            </div>
        </div>
    </div>

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
            <!-- Scroll horizontal superior opcional -->
            <div id="top-scroll" style="overflow-x: auto; width: 100%; height: 20px; background: #f8f9fa;">
                <div id="top-scroll-inner" style="height: 1px; width: 2000px;"></div>
            </div>
            <div id="main-table-scroll"
                style="overflow-x: auto; overflow-y: auto; max-height: 60vh; width: 100%; border-bottom: 1px solid #ccc;">
                <div id="tableContainer">
                    @include('activities.partials.activity_table', [
                        'activities' => $activities,
                        'statusLabels' => $statusLabels,
                        'statusColors' => $statusColors,
                        'analistas' => $analistas,
                        // agrega aquí cualquier otra variable que uses en la tabla
                    ])
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const topScroll = document.getElementById('top-scroll');
            const tableScroll = document.getElementById('main-table-scroll');
            if (topScroll && tableScroll) {
                topScroll.addEventListener('scroll', function() {
                    tableScroll.scrollLeft = topScroll.scrollLeft;
                });
                tableScroll.addEventListener('scroll', function() {
                    topScroll.scrollLeft = tableScroll.scrollLeft;
                });
            }
        });
    </script>
    <!-- Modal para Editar Estados -->
    <div class="modal fade" id="statusEditModal" tabindex="-1" role="dialog" aria-labelledby="statusEditModalLabel"
        aria-hidden="true">
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
                                            name="status_ids[]" value="{{ $status->id }}"
                                            data-status-name="{{ $status->name }}" id="status_{{ $status->id }}">

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

    <!-- Modal para Editar Analistas -->
    <div class="modal fade" id="analystsEditModal" tabindex="-1" role="dialog"
        aria-labelledby="analystsEditModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="analystsEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="analystsEditModalLabel">
                            <i class="fas fa-users"></i> Editar Analistas de Actividad
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="activity_id" id="modalAnalystsActivityId">
                        <div class="form-group">
                            <label for="modalAnalystsSelect">Selecciona analistas:</label>
                            <select name="analista_id[]" id="modalAnalystsSelect" class="form-control" multiple>
                                @foreach ($analistas as $analista)
                                    <option value="{{ $analista->id }}">{{ $analista->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="saveAnalystsAjaxBtn">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
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

    /* Oculta los botones de acción por defecto */
    /* .action-buttons {
        display: none;
    } */

    /* Muestra los botones de acción al hacer hover sobre la fila */
    .activity-row:hover .action-buttons,
    .subactivity-row:hover .action-buttons {
        display: block !important;
    }

    /* Oculta el botón de editar analistas por defecto */
    .analysts-edit-btn-group {
        display: none;
    }

    /* Muestra el botón al hacer hover sobre la fila */
    .activity-row:hover .analysts-edit-btn-group,
    .subactivity-row:hover .analysts-edit-btn-group {
        display: inline-block !important;
    }

    /* Sticky header para la tabla de actividades */
    .sticky-thead th {
        position: sticky;
        top: 0;
        z-index: 102;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }

    #main-table-scroll {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 60vh;
        width: 100%;
        border-bottom: 1px solid #ccc;
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón para abrir el modal de analistas
        document.querySelectorAll('.edit-analysts-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var activityId = btn.getAttribute('data-activity-id');
                var analysts = [];
                // Busca los badges de analistas en la misma fila
                btn.closest('td').querySelectorAll('.badge').forEach(function(badge) {
                    analysts.push(badge.textContent.trim());
                });

                // Selecciona la opción correspondiente en el select del modal
                var select = document.getElementById('modalAnalystsSelect');
                for (var i = 0; i < select.options.length; i++) {
                    select.options[i].selected = false;
                    if (analysts.includes(select.options[i].text.trim())) {
                        select.options[i].selected = true;
                    }
                }

                // Cambia la acción del formulario
                var form = document.getElementById('analystsEditForm');
                form.action = '/activities/' + activityId;
                document.getElementById('modalAnalystsActivityId').value = activityId;

                // Muestra el modal de analistas
                $('#analystsEditModal').modal('show');
            });
        });

        // AJAX para guardar analistas sin refrescar la página
        document.getElementById('analystsEditForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var form = this;
            var activityId = document.getElementById('modalAnalystsActivityId').value;
            var select = document.getElementById('modalAnalystsSelect');
            var selected = Array.from(select.selectedOptions).map(opt => opt.value);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        analista_id: selected
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualiza la celda de analistas en la tabla
                        var row = document.querySelector('tr[data-activity-id="' + activityId +
                            '"]');
                        if (row) {
                            var cell = row.querySelector('td .analysts-list');
                            if (cell) {
                                // Renderiza los nuevos analistas
                                cell.innerHTML = '';
                                data.analistas.forEach(function(analista) {
                                    var span = document.createElement('span');
                                    span.className = 'badge badge-light mr-1 mb-1';
                                    span.innerHTML = '<i class="fas fa-user"></i> ' +
                                        analista.name;
                                    cell.appendChild(span);
                                });
                            }
                            // Resalta la fila editada
                            row.classList.add('table-success');
                            setTimeout(function() {
                                row.classList.remove('table-success');
                            }, 2000);
                        }
                        $('#analystsEditModal').modal('hide');
                    } else {
                        alert('Error al actualizar analistas');
                    }
                })
                .catch(() => {
                    alert('Error al actualizar analistas');
                });
        });
    });

    // Delegación de eventos para el modal de estados (funciona siempre, incluso tras recarga de tabla)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-status-btn')) {
            e.preventDefault();
            var btn = e.target.closest('.edit-status-btn');
            var data = btn.getAttribute('data-current-statuses');
            var currentStatuses = [];
            if (data) {
                currentStatuses = data.split(',').map(function(id) {
                    return id.trim();
                });
            }
            $('#statusEditModal').off('shown.bs.modal').on('shown.bs.modal', function() {
                $('#statusEditModal input[type="checkbox"][name="status_ids[]"]').prop('checked',
                false);
                currentStatuses.forEach(function(id) {
                    $('#statusEditModal input[type="checkbox"][name="status_ids[]"][value="' +
                        id + '"]').prop('checked', true);
                });
            });
            $('#statusEditModal').modal('show');
        }
    });
</script>

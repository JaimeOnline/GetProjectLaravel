@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-gradient mb-2">
                        <i class="fas fa-chart-bar text-info"></i> Reporte de Requerimientos
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle"></i>
                        Análisis detallado del estado de los requerimientos del sistema
                    </p>
                </div>
                <div class="action-buttons">
                    <a href="{{ route('requirements.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                        <i class="fas fa-arrow-left"></i> Volver a Lista
                    </a>
                    <a href="{{ route('requirements.create') }}" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-plus"></i> Nuevo Requerimiento
                    </a>
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">
                        <i class="fas fa-download"></i> Exportar Reporte
                    </h5>
                    <p class="mb-0 opacity-75">
                        Descarga los datos del reporte actual
                    </p>
                </div>
                <div class="btn-group">
                    <form id="export-excel-form" action="{{ route('requirements.report.export_excel_selected') }}"
                        method="POST" class="mr-2">
                        @csrf
                        {{-- Filtros actuales para que el backend los conozca si hace falta --}}
                        <input type="hidden" name="status" value="{{ request('status', 'pendiente') }}">
                        <input type="hidden" name="cliente_id" value="{{ request('cliente_id') }}">
                        <input type="hidden" name="activity_id" value="{{ request('activity_id') }}">
                        <input type="hidden" name="fecha_desde" value="{{ request('fecha_desde') }}">
                        <input type="hidden" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                        <input type="hidden" name="fecha_recepcion_desde" value="{{ request('fecha_recepcion_desde') }}">
                        <input type="hidden" name="fecha_recepcion_hasta" value="{{ request('fecha_recepcion_hasta') }}">
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                        {{-- IDs seleccionados se llenan en JS --}}
                        <div id="selected-ids-container"></div>

                        <button type="button" id="export-excel-btn" class="btn btn-success btn-lg">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </form>

                    <button type="button" id="copy-report-btn" class="btn btn-primary btn-lg">
                        <i class="fas fa-copy"></i> Copiar texto
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filter-section card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter text-primary"></i> Filtros del Reporte
                </h5>
                <button type="button" id="toggle-filters-btn" class="btn btn-sm btn-light text-dark">
                    <i class="fas fa-chevron-down"></i>
                    Mostrar filtros
                </button>
            </div>
            <div class="card-body" id="filters-body" style="display: none;">
                <form method="GET" action="{{ route('requirements.report') }}">
                    {{-- Fila 1: Estado, Cliente, Actividad --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="font-weight-bold">
                                    <i class="fas fa-flag text-primary"></i> Estado
                                </label>
                                <select class="form-control" id="status" name="status">
                                    <option value=""
                                        {{ request()->has('status') && request('status') === '' ? 'selected' : '' }}>
                                        Todos los estados
                                    </option>
                                    <option value="pendiente"
                                        {{ request('status', 'pendiente') === 'pendiente' ? 'selected' : '' }}>
                                        Pendiente
                                    </option>
                                    <option value="recibido" {{ request('status') === 'recibido' ? 'selected' : '' }}>
                                        Recibido
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente_id" class="font-weight-bold">
                                    <i class="fas fa-user-tie text-primary"></i> Cliente
                                </label>
                                <select class="form-control" id="cliente_id" name="cliente_id">
                                    <option value="">Todos los clientes</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                            {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="activity_id" class="font-weight-bold">
                                    <i class="fas fa-tasks text-primary"></i> Actividad
                                </label>
                                <select class="form-control" id="activity_id" name="activity_id">
                                    <option value="">Todas las actividades</option>
                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}"
                                            {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                                            {{ $activity->caso ? '[' . $activity->caso . '] ' : '' }}{{ $activity->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Fila 2: Fecha Desde, Fecha Hasta, Recepción Desde --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_desde" class="font-weight-bold">
                                    <i class="fas fa-calendar text-primary"></i> Fecha Desde
                                </label>
                                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                                    value="{{ request('fecha_desde') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_hasta" class="font-weight-bold">
                                    <i class="fas fa-calendar text-primary"></i> Fecha Hasta
                                </label>
                                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                                    value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_recepcion_desde" class="font-weight-bold">
                                    <i class="fas fa-calendar-check text-primary"></i> Recepción Desde
                                </label>
                                <input type="date" class="form-control" id="fecha_recepcion_desde"
                                    name="fecha_recepcion_desde" value="{{ request('fecha_recepcion_desde') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Fila 3: Recepción Hasta, Ordenar Por, Orden --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_recepcion_hasta" class="font-weight-bold">
                                    <i class="fas fa-calendar-check text-primary"></i> Recepción Hasta
                                </label>
                                <input type="date" class="form-control" id="fecha_recepcion_hasta"
                                    name="fecha_recepcion_hasta" value="{{ request('fecha_recepcion_hasta') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_by" class="font-weight-bold">
                                    <i class="fas fa-sort text-primary"></i> Ordenar Por
                                </label>
                                <select class="form-control" id="sort_by" name="sort_by">
                                    <option value="created_at"
                                        {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>
                                        Fecha Creación
                                    </option>
                                    <option value="fecha_recepcion"
                                        {{ request('sort_by') === 'fecha_recepcion' ? 'selected' : '' }}>
                                        Fecha Recepción
                                    </option>
                                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>
                                        Estado
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sort_order" class="font-weight-bold">
                                    <i class="fas fa-sort-amount-down text-primary"></i> Orden
                                </label>
                                <select class="form-control" id="sort_order" name="sort_order">
                                    <option value="desc"
                                        {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>
                                        Descendente
                                    </option>
                                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>
                                        Ascendente
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg mr-2">
                            <i class="fas fa-search"></i> Generar Reporte
                        </button>
                        <a href="{{ route('requirements.report') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-eraser"></i> Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-label text-muted">Total Requerimientos</div>
                <div class="stat-number text-primary">{{ $stats['total'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label text-muted">Pendientes</div>
                <div class="stat-number text-warning">{{ $stats['pendientes'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label text-muted">Recibidos</div>
                <div class="stat-number text-success">{{ $stats['recibidos'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label text-muted">Días Promedio Respuesta</div>
                <div class="stat-number text-info">{{ $stats['tiempo_promedio_respuesta'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label text-muted">Vencidos (+7 días)</div>
                <div class="stat-number text-danger">{{ $stats['requerimientos_vencidos'] }}</div>
            </div>
        </div>

        <!-- Requirements by Activity -->
        @if ($requirementsByActivity->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks text-primary"></i> Requerimientos por Actividad
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($requirementsByActivity as $activityName => $activityRequirements)
                        <div class="activity-group">
                            <div class="activity-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="mr-2 select-activity"
                                            data-activity-id="{{ $activityRequirements->first()->activity->id }}">
                                        @if ($activityRequirements->first()->activity->caso)
                                            <span class="badge badge-light ml-2">
                                                {{ $activityRequirements->first()->activity->caso }}
                                            </span>
                                        @endif
                                        <strong class="ml-2">
                                            <a href="{{ route('activities.edit', $activityRequirements->first()->activity->id) }}"
                                                style="color: #222; text-decoration: underline;" title="Editar actividad">
                                                {{ $activityName }}
                                            </a>
                                        </strong>
                                    </div>
                                    <div>
                                        <span class="badge badge-light">
                                            {{ $activityRequirements->count() }} requerimientos
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @foreach ($activityRequirements as $requirement)
                                <div class="requirement-item" data-activity-id="{{ $requirement->activity_id }}"
                                    data-requirement-id="{{ $requirement->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="mr-2">
                                            <input type="checkbox" class="select-requirement"
                                                data-requirement-id="{{ $requirement->id }}">
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="mb-2">
                                                <strong>{!! nl2br(e($requirement->description)) !!}</strong>
                                            </div>
                                            <div class="d-flex align-items-center text-muted">
                                                <small class="mr-3">
                                                    <i class="fas fa-calendar"></i>
                                                    Creado: {{ $requirement->created_at->format('d/m/Y H:i') }}
                                                </small>
                                                @if ($requirement->fecha_recepcion)
                                                    <small class="mr-3">
                                                        <i class="fas fa-calendar-check text-success"></i>
                                                        Recibido: {{ $requirement->fecha_recepcion->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
                                                <small>
                                                    <i class="fas fa-clock"></i>
                                                    {{ $requirement->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            @if ($requirement->notas)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-sticky-note"></i>
                                                        {{ Str::limit($requirement->notas, 1000) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            @if ($requirement->status === 'pendiente')
                                                <span class="badge badge-warning badge-lg">
                                                    <i class="fas fa-clock"></i> Pendiente
                                                </span>
                                                @if ($requirement->created_at->diffInDays(now()) > 7)
                                                    <br><span class="badge badge-danger badge-sm mt-1">Vencido</span>
                                                @endif
                                            @else
                                                <span class="badge badge-success badge-lg">
                                                    <i class="fas fa-check-circle"></i> Recibido
                                                </span>
                                                <br><small class="text-muted">
                                                    {{ $requirement->created_at->diffInDays($requirement->fecha_recepcion) }}
                                                    días
                                                </small>
                                            @endif

                                            <!-- Botones de acción -->
                                            <div class="btn-group btn-group-sm mt-2" role="group">
                                                <a href="{{ route('requirements.show', $requirement) }}"
                                                    class="btn btn-info btn-xs action-btn" data-tooltip="Ver Detalles"
                                                    title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('requirements.edit', $requirement) }}"
                                                    class="btn btn-warning btn-xs action-btn" data-tooltip="Editar"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if ($requirement->status === 'pendiente')
                                                    <form action="{{ route('requirements.mark-received', $requirement) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-xs action-btn"
                                                            data-tooltip="Marcar como Recibido"
                                                            title="Marcar como Recibido">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('requirements.mark-pending', $requirement) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-secondary btn-xs action-btn"
                                                            data-tooltip="Marcar como Pendiente"
                                                            title="Marcar como Pendiente">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('requirements.destroy', $requirement) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs action-btn"
                                                        data-tooltip="Eliminar" title="Eliminar"
                                                        onclick="return confirm('¿Estás seguro de eliminar este requerimiento?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron requerimientos</h5>
                    <p class="text-muted">
                        No hay requerimientos que coincidan con los filtros aplicados.
                    </p>
                    <a href="{{ route('requirements.report') }}" class="btn btn-primary">
                        <i class="fas fa-refresh"></i> Ver Todos los Requerimientos
                    </a>
                </div>
            </div>
        @endif

        <!-- Monthly Trend -->
        @if ($requirementsByMonth->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i> Tendencia Mensual
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mes</th>
                                    <th>Total</th>
                                    <th>Pendientes</th>
                                    <th>Recibidos</th>
                                    <th>% Completado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requirementsByMonth->sortKeysDesc() as $month => $monthRequirements)
                                    @php
                                        $pendientes = $monthRequirements->where('status', 'pendiente')->count();
                                        $recibidos = $monthRequirements->where('status', 'recibido')->count();
                                        $total = $monthRequirements->count();
                                        $porcentaje = $total > 0 ? round(($recibidos / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</strong>
                                        </td>
                                        <td>{{ $total }}</td>
                                        <td>
                                            <span class="badge badge-warning">{{ $pendientes }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ $recibidos }}</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $porcentaje }}%"
                                                    aria-valuenow="{{ $porcentaje }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $porcentaje }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filtros
            var toggleBtn = document.getElementById('toggle-filters-btn');
            var filtersBody = document.getElementById('filters-body');
            if (toggleBtn && filtersBody) {
                toggleBtn.addEventListener('click', function() {
                    var isVisible = filtersBody.style.display !== 'none';
                    // Si está visible, lo ocultamos
                    filtersBody.style.display = isVisible ? 'none' : 'block';

                    var icon = toggleBtn.querySelector('i.fas');
                    if (icon) {
                        icon.classList.remove('fa-chevron-up', 'fa-chevron-down');
                        icon.classList.add(isVisible ? 'fa-chevron-down' : 'fa-chevron-up');
                    }

                    // Actualizar texto del botón
                    var label = isVisible ? ' Mostrar filtros' : ' Ocultar filtros';
                    // Limpiar texto actual (dejando el ícono)
                    toggleBtn.childNodes.forEach(function(node, idx) {
                        if (idx > 0 && node.nodeType === Node.TEXT_NODE) {
                            node.textContent = '';
                        }
                    });
                    toggleBtn.appendChild(document.createTextNode(label));
                });
            }

            // Manejar selección de actividades y requerimientos
            var activityCheckboxes = document.querySelectorAll('.select-activity');
            var requirementCheckboxes = document.querySelectorAll('.select-requirement');

            // Al marcar/desmarcar una actividad, marcar todos sus requerimientos
            activityCheckboxes.forEach(function(chk) {
                chk.addEventListener('change', function() {
                    var activityId = chk.getAttribute('data-activity-id');
                    var reqs = document.querySelectorAll('.requirement-item[data-activity-id="' +
                        activityId + '"] .select-requirement');
                    reqs.forEach(function(r) {
                        r.checked = chk.checked;
                    });
                });
            });

            // Al cambiar un requerimiento, actualizar checkbox de actividad
            requirementCheckboxes.forEach(function(chk) {
                chk.addEventListener('change', function() {
                    var requirementItem = chk.closest('.requirement-item');
                    if (!requirementItem) return;
                    var activityId = requirementItem.getAttribute('data-activity-id');
                    var allReqs = document.querySelectorAll('.requirement-item[data-activity-id="' +
                        activityId + '"] .select-requirement');
                    var allChecked = true;
                    allReqs.forEach(function(r) {
                        if (!r.checked) {
                            allChecked = false;
                        }
                    });
                    var activityChk = document.querySelector('.select-activity[data-activity-id="' +
                        activityId + '"]');
                    if (activityChk) {
                        activityChk.checked = allChecked;
                    }
                });
            });

            // Botón Excel (exportar seleccionados o todos)
            var exportBtn = document.getElementById('export-excel-btn');
            var exportForm = document.getElementById('export-excel-form');
            var selectedContainer = document.getElementById('selected-ids-container');

            if (exportBtn && exportForm && selectedContainer) {
                exportBtn.addEventListener('click', function() {
                    // Limpiar inputs anteriores
                    selectedContainer.innerHTML = '';

                    // Obtener todos los requerimientos seleccionados
                    var selectedReqs = document.querySelectorAll('.select-requirement:checked');

                    selectedReqs.forEach(function(chk) {
                        var reqId = chk.getAttribute('data-requirement-id');
                        if (!reqId) return;
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = reqId;
                        selectedContainer.appendChild(input);
                    });

                    // Si no hay seleccionados, el backend tomará todos según filtros
                    exportForm.submit();
                });
            }

            var copyBtn = document.getElementById('copy-report-btn');
            if (!copyBtn) return;

            copyBtn.addEventListener('click', function() {
                // Construir texto plano con los requerimientos visibles/seleccionados
                var blocks = [];
                var activityGroups = document.querySelectorAll('.activity-group');

                // ¿Hay requerimientos seleccionados en todo el reporte?
                var anySelected = document.querySelectorAll('.select-requirement:checked').length > 0;

                activityGroups.forEach(function(group) {
                    var header = group.querySelector('.activity-header strong');
                    if (!header) return;

                    var activityName = header.textContent.trim();

                    // Caso (texto del badge junto al nombre de la actividad, en el primer <div>)
                    var casoBadge = group.querySelector(
                        '.activity-header .d-flex > div:first-child .badge');
                    var caso = '';
                    if (casoBadge) {
                        caso = casoBadge.textContent.replace(/\s+/g, ' ').trim();
                    }

                    // Cantidad de requerimientos (badge del lado derecho, en el último <div>)
                    var countBadge = group.querySelector(
                        '.activity-header .d-flex > div:last-child .badge');
                    var countText = '';
                    if (countBadge) {
                        countText = countBadge.textContent
                            .replace(/\s+/g, ' ') // colapsar espacios
                            .replace('requerimientos', '')
                            .trim();
                    }

                    // Requerimientos a copiar en esta actividad
                    var items;
                    if (anySelected) {
                        // Solo los seleccionados en esta actividad
                        var selectedReqs = group.querySelectorAll(
                            '.requirement-item .select-requirement:checked');
                        if (selectedReqs.length === 0) {
                            items = []; // nada en esta actividad
                        } else {
                            items = Array.from(selectedReqs).map(function(chk) {
                                return chk.closest('.requirement-item');
                            });
                        }
                    } else {
                        // Si no hay ningún seleccionado globalmente, tomar todos
                        items = group.querySelectorAll('.requirement-item');
                    }

                    // Si hay selección global y esta actividad no tiene items, la saltamos completa
                    if (anySelected && items.length === 0) {
                        return;
                    }

                    var headerLine = '';

                    // Si hay caso (ej: GLPI123), anteponerlo
                    if (caso) {
                        headerLine += caso + ' ';
                    }

                    // Nombre de la actividad
                    headerLine += activityName;

                    // Sufijo con cantidad: "(1 requerimiento)" o "(N requerimientos)"
                    if (countText) {
                        var num = parseInt(countText, 10);
                        if (isNaN(num)) {
                            headerLine += ' (' + countText + ' requerimientos)';
                        } else {
                            headerLine += ' (' + num + ' ' + (num === 1 ? 'requerimiento' :
                                'requerimientos') + ')';
                        }
                    }

                    blocks.push('==============================');
                    blocks.push(headerLine);
                    blocks.push('==============================');

                    items.forEach(function(item, index) {
                        var desc = item.querySelector('strong');
                        var meta = item.querySelector(
                            '.d-flex.align-items-center.text-muted');
                        var statusBadge = item.querySelector(
                            '.badge-warning, .badge-success');
                        var notas = item.querySelector('.fas.fa-sticky-note');

                        var lineas = [];

                        // Separador entre requerimientos (no antes del primero)
                        lineas.push('------------------------------');
                        lineas.push((index + 1) + '. ' + (desc ? desc.textContent.trim() :
                            ''));

                        if (statusBadge) {
                            lineas.push('   Estado: ' + statusBadge.textContent.trim());
                        }
                        if (meta) {
                            lineas.push('   ' + meta.textContent.replace(/\s+/g, ' ')
                                .trim());
                        }
                        if (notas && notas.parentElement) {
                            lineas.push('   Notas: ' + notas.parentElement.textContent
                                .replace('Notas', '').trim());
                        }

                        blocks.push(lineas.join('\n'));
                    });

                    blocks.push(''); // espacio entre actividades
                });

                var text = blocks.join('\n');

                if (!text.trim()) {
                    alert('No hay requerimientos para copiar.');
                    return;
                }

                navigator.clipboard.writeText(text).then(function() {
                    alert('Requerimientos copiados al portapapeles.');
                }).catch(function() {
                    // Fallback si clipboard API falla
                    var textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.left = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        alert('Requerimientos copiados al portapapeles.');
                    } catch (e) {
                        alert('No se pudo copiar el texto automáticamente.');
                    }
                    document.body.removeChild(textarea);
                });
            });
        });
    </script>
@endsection

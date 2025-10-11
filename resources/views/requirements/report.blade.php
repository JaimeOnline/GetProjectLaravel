@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activity-group {
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .activity-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
        }

        .requirement-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s ease;
        }

        .requirement-item:hover {
            background-color: #f8f9fa;
        }

        .requirement-item:last-child {
            border-bottom: none;
        }

        .filter-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .export-section {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
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
                        Descarga los datos del reporte actual en formato CSV
                    </p>
                </div>
                <div>
                    <a href="{{ route('requirements.report.export', request()->query()) }}" class="btn btn-light btn-lg">
                        <i class="fas fa-file-csv"></i> Descargar CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filter-section">
            <h5 class="mb-4">
                <i class="fas fa-filter text-primary"></i> Filtros del Reporte
            </h5>
            <form method="GET" action="{{ route('requirements.report') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="font-weight-bold">
                                <i class="fas fa-flag text-primary"></i> Estado
                            </label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>
                                    Pendiente</option>
                                <option value="recibido" {{ request('status') === 'recibido' ? 'selected' : '' }}>Recibido
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_desde" class="font-weight-bold">
                                <i class="fas fa-calendar text-primary"></i> Fecha Desde
                            </label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                                value="{{ request('fecha_desde') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_hasta" class="font-weight-bold">
                                <i class="fas fa-calendar text-primary"></i> Fecha Hasta
                            </label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                                value="{{ request('fecha_hasta') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_recepcion_desde" class="font-weight-bold">
                                <i class="fas fa-calendar-check text-primary"></i> Recepción Desde
                            </label>
                            <input type="date" class="form-control" id="fecha_recepcion_desde"
                                name="fecha_recepcion_desde" value="{{ request('fecha_recepcion_desde') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_recepcion_hasta" class="font-weight-bold">
                                <i class="fas fa-calendar-check text-primary"></i> Recepción Hasta
                            </label>
                            <input type="date" class="form-control" id="fecha_recepcion_hasta"
                                name="fecha_recepcion_hasta" value="{{ request('fecha_recepcion_hasta') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sort_by" class="font-weight-bold">
                                <i class="fas fa-sort text-primary"></i> Ordenar Por
                            </label>
                            <select class="form-control" id="sort_by" name="sort_by">
                                <option value="created_at"
                                    {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Fecha
                                    Creación</option>
                                <option value="fecha_recepcion"
                                    {{ request('sort_by') === 'fecha_recepcion' ? 'selected' : '' }}>Fecha Recepción
                                </option>
                                <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Estado
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sort_order" class="font-weight-bold">
                                <i class="fas fa-sort-amount-down text-primary"></i> Orden
                            </label>
                            <select class="form-control" id="sort_order" name="sort_order">
                                <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>
                                    Descendente</option>
                                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascendente
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

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number text-primary">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Requerimientos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-warning">{{ $stats['pendientes'] }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-success">{{ $stats['recibidos'] }}</div>
                <div class="stat-label">Recibidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-info">{{ $stats['tiempo_promedio_respuesta'] }}</div>
                <div class="stat-label">Días Promedio Respuesta</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-danger">{{ $stats['requerimientos_vencidos'] }}</div>
                <div class="stat-label">Vencidos (+7 días)</div>
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
                                    <div>
                                        @if ($activityRequirements->first()->activity->caso)
                                            <span
                                                class="badge badge-light ml-2">{{ $activityRequirements->first()->activity->caso }}</span>
                                        @endif
                                        <strong>
                                            <a href="{{ route('activities.edit', $activityRequirements->first()->activity->id) }}"
                                                style="color: #222; text-decoration: underline;" title="Editar actividad">
                                                {{ $activityName }}
                                            </a>
                                        </strong>

                                    </div>
                                    <div>
                                        <span class="badge badge-light">{{ $activityRequirements->count() }}
                                            requerimientos</span>
                                    </div>
                                </div>
                            </div>
                            @foreach ($activityRequirements as $requirement)
                                <div class="requirement-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="mb-2">
                                                <strong>{{ $requirement->description }}</strong>
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
@endsection

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
                        <i class="fas fa-clipboard-list text-primary"></i> Gestión de Requerimientos
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle"></i>
                        Administra todos los requerimientos del sistema y su estado
                    </p>
                </div>
                <div class="action-buttons">
                    <a href="{{ route('requirements.report') }}" class="btn btn-info btn-lg shadow-sm mr-2">
                        <i class="fas fa-chart-bar"></i> Ver Reporte
                    </a>
                    <a href="{{ route('requirements.create') }}" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-plus"></i> Nuevo Requerimiento
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card bg-primary">
                    <div class="stats-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stats-content">
                        <h3>{{ $stats['total'] }}</h3>
                        <p>Total Requerimientos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card bg-warning">
                    <div class="stats-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-content">
                        <h3>{{ $stats['pendientes'] }}</h3>
                        <p>Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card bg-success">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-content">
                        <h3>{{ $stats['recibidos'] }}</h3>
                        <p>Recibidos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-search text-primary"></i> Búsqueda y Filtros
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('requirements.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search" class="font-weight-bold">
                                    <i class="fas fa-search text-primary"></i> Búsqueda
                                </label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Buscar en descripción, notas, actividad...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status" class="font-weight-bold">
                                    <i class="fas fa-flag text-primary"></i> Estado
                                </label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Todos los estados</option>
                                    <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>
                                        Pendiente</option>
                                    <option value="recibido" {{ request('status') === 'recibido' ? 'selected' : '' }}>
                                        Recibido</option>
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-filter text-primary"></i> Acciones
                                </label>
                                <div class="btn-group-vertical btn-group-sm w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <a href="{{ route('requirements.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-eraser"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Requirements Table -->
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Lista de Requerimientos
                    </h5>
                    <small class="text-muted">
                        Mostrando {{ $requirements->count() }} de {{ $requirements->total() }} requerimientos
                    </small>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($requirements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 modern-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">
                                        <i class="fas fa-hashtag text-primary"></i> Caso
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-tasks text-primary"></i> Actividad
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-align-left text-primary"></i> Descripción
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-flag text-primary"></i> Estado
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-calendar text-primary"></i> Fecha Creación
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-calendar-check text-primary"></i> Fecha Recepción
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-cogs text-primary"></i> Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requirements as $requirement)
                                    <tr>
                                        <td class="font-weight-bold">
                                            @if ($requirement->activity && $requirement->activity->caso)
                                                <span class="badge badge-info">{{ $requirement->activity->caso }}</span>
                                            @else
                                                <span class="text-muted">Sin caso</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="activity-info">
                                                @if ($requirement->activity->caso)
                                                    <span
                                                        class="badge badge-info mb-1">{{ $requirement->activity->caso }}</span><br>
                                                @endif
                                                <strong>
                                                    <a href="{{ route('activities.edit', $requirement->activity->id) }}"
                                                        class="text-primary" title="Editar actividad">
                                                        {{ $requirement->activity->name }}
                                                    </a>
                                                </strong>
                                                @if ($requirement->activity->parent)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-level-up-alt fa-rotate-90"></i>
                                                        {{ $requirement->activity->parent->name }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="requirement-description">
                                                {!! nl2br(e(Str::limit($requirement->description, 1000))) !!}
                                                @if ($requirement->notas)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-sticky-note"></i>
                                                        {{ Str::limit($requirement->notas, 50) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            @if ($requirement->status === 'pendiente')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pendiente
                                                </span>
                                            @else
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Recibido
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $requirement->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($requirement->fecha_recepcion)
                                                <small class="text-success">
                                                    {{ $requirement->fecha_recepcion->format('d/m/Y H:i') }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
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
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $requirements->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron requerimientos</h5>
                        <p class="text-muted">
                            @if (request()->hasAny(['search', 'status', 'activity_id']))
                                No hay requerimientos que coincidan con los filtros aplicados.
                            @else
                                Aún no hay requerimientos registrados en el sistema.
                            @endif
                        </p>
                        <a href="{{ route('requirements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Requerimiento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Implementar tooltips simples usando title nativo
            function initializeSimpleTooltips() {
                // Encontrar todos los botones de acción
                const actionButtons = document.querySelectorAll('.action-btn');

                actionButtons.forEach(function(button) {
                    const tooltipText = button.getAttribute('data-tooltip');
                    if (tooltipText && !button.hasAttribute('title')) {
                        button.setAttribute('title', tooltipText);
                    }
                });
            }

            // Inicializar tooltips
            initializeSimpleTooltips();
        });
    </script>
@endsection

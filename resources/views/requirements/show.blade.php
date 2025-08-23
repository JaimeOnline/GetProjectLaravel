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
                    <i class="fas fa-eye text-info"></i> Requerimiento #{{ $requirement->id }}
                </h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Detalles completos del requerimiento
                </p>
            </div>
            <div class="action-buttons">
                <a href="{{ route('requirements.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                    <i class="fas fa-arrow-left"></i> Volver a Lista
                </a>
                <a href="{{ route('requirements.edit', $requirement) }}" class="btn btn-warning btn-lg shadow-sm">
                    <i class="fas fa-edit"></i> Editar
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

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list text-primary"></i> Información del Requerimiento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-hashtag text-primary"></i> ID del Requerimiento
                            </h6>
                            <p class="font-weight-bold">#{{ $requirement->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-flag text-primary"></i> Estado Actual
                            </h6>
                            <p>
                                @if($requirement->status === 'pendiente')
                                    <span class="badge badge-warning badge-lg">
                                        <i class="fas fa-clock"></i> Pendiente
                                    </span>
                                @else
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-check-circle"></i> Recibido
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-align-left text-primary"></i> Descripción
                        </h6>
                        <div class="description-box">
                            {{ $requirement->description }}
                        </div>
                    </div>

                    @if($requirement->notas)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-sticky-note text-primary"></i> Notas Adicionales
                            </h6>
                            <div class="notes-box">
                                {{ $requirement->notas }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks text-primary"></i> Actividad Asociada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-details">
                        @if($requirement->activity->caso)
                            <div class="mb-2">
                                <span class="badge badge-info badge-lg">{{ $requirement->activity->caso }}</span>
                            </div>
                        @endif
                        
                        <h5 class="mb-3">{{ $requirement->activity->name }}</h5>
                        
                        @if($requirement->activity->description)
                            <p class="text-muted mb-3">{{ $requirement->activity->description }}</p>
                        @endif

                        @if($requirement->activity->parent)
                            <div class="parent-activity mb-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-level-up-alt fa-rotate-90"></i> Actividad Padre
                                </h6>
                                <p>
                                    <strong>{{ $requirement->activity->parent->name }}</strong>
                                    @if($requirement->activity->parent->caso)
                                        <span class="badge badge-secondary ml-2">{{ $requirement->activity->parent->caso }}</span>
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Estado de la Actividad</h6>
                                <p>
                                    @if($requirement->activity->status === 'en_ejecucion')
                                        <span class="badge badge-info">
                                            <i class="fas fa-play-circle"></i> En Ejecución
                                        </span>
                                    @elseif($requirement->activity->status === 'culminada')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Culminada
                                        </span>
                                    @elseif($requirement->activity->status === 'en_espera_de_insumos')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-pause-circle"></i> En Espera de Insumos
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Fecha de Recepción de Actividad</h6>
                                <p>
                                    @if($requirement->activity->fecha_recepcion)
                                        {{ $requirement->activity->fecha_recepcion->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">No especificada</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('activities.edit', $requirement->activity) }}" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> Ver Actividad Completa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-primary"></i> Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($requirement->status === 'pendiente')
                            <form action="{{ route('requirements.mark-received', $requirement) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> Marcar como Recibido
                                </button>
                            </form>
                        @else
                            <form action="{{ route('requirements.mark-pending', $requirement) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-undo"></i> Marcar como Pendiente
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('requirements.edit', $requirement) }}" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-edit"></i> Editar Requerimiento
                        </a>
                        
                        <form action="{{ route('requirements.destroy', $requirement) }}" method="POST" class="mb-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-outline-danger btn-block"
                                    onclick="return confirm('¿Estás seguro de eliminar este requerimiento? Esta acción no se puede deshacer.')">
                                <i class="fas fa-trash"></i> Eliminar Requerimiento
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-primary"></i> Cronología
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Requerimiento Creado</h6>
                                <p class="timeline-text">{{ $requirement->created_at->format('d/m/Y H:i:s') }}</p>
                                <small class="text-muted">{{ $requirement->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        
                        @if($requirement->updated_at != $requirement->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Última Modificación</h6>
                                    <p class="timeline-text">{{ $requirement->updated_at->format('d/m/Y H:i:s') }}</p>
                                    <small class="text-muted">{{ $requirement->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($requirement->fecha_recepcion)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Requerimiento Recibido</h6>
                                    <p class="timeline-text">{{ $requirement->fecha_recepcion->format('d/m/Y H:i:s') }}</p>
                                    <small class="text-muted">{{ $requirement->fecha_recepcion->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary"></i> Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tiempo desde creación:</span>
                            <strong>{{ $requirement->created_at->diffForHumans() }}</strong>
                        </div>
                    </div>
                    
                    @if($requirement->fecha_recepcion)
                        <div class="stat-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Tiempo de respuesta:</span>
                                <strong>{{ $requirement->created_at->diffInDays($requirement->fecha_recepcion) }} días</strong>
                            </div>
                        </div>
                    @else
                        <div class="stat-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Días pendiente:</span>
                                <strong class="text-warning">{{ $requirement->created_at->diffInDays(now()) }} días</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.description-box, .notes-box {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.notes-box {
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 0.9em;
    font-weight: 600;
}

.timeline-text {
    margin: 0 0 5px 0;
    font-size: 0.85em;
}

.stat-item {
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    border-bottom: none;
}

.activity-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.parent-activity {
    background: rgba(108, 117, 125, 0.1);
    padding: 15px;
    border-radius: 6px;
    border-left: 3px solid #6c757d;
}
</style>
@endsection
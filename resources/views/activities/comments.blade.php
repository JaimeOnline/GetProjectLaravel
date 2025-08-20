@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Comentarios de la Actividad</h1>
                <a href="{{ route('activities.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
            </div>

            {{-- Información de la actividad --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks"></i> 
                        {{ $activity->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Caso:</strong> {{ $activity->caso }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge badge-{{ $activity->status == 'culminada' ? 'success' : ($activity->status == 'en_ejecucion' ? 'warning' : 'info') }}">
                                    {{ $activity->status_label }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha de Recepción:</strong> 
                                {{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('d/m/Y') : 'No asignada' }}
                            </p>
                            <p><strong>Total de Comentarios:</strong> 
                                <span class="badge badge-info">{{ $activity->comments->count() }}</span>
                            </p>
                        </div>
                    </div>
                    @if($activity->description)
                        <p><strong>Descripción:</strong> {{ $activity->description }}</p>
                    @endif
                </div>
            </div>

            {{-- Lista de comentarios --}}
            @if($activity->comments->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-comments"></i> 
                            Historial de Comentarios
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($activity->comments->sortByDesc('created_at') as $comment)
                            <div class="comment-item border-left border-primary pl-3 mb-3">
                                <div class="comment-content">
                                    <p class="mb-2">{{ $comment->comment }}</p>
                                </div>
                                <div class="comment-meta">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> 
                                        {{ $comment->created_at->format('d/m/Y H:i:s') }}
                                        <span class="ml-2">
                                            ({{ $comment->created_at->diffForHumans() }})
                                        </span>
                                    </small>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Esta actividad no tiene comentarios aún.
                </div>
            @endif

            {{-- Botones de acción --}}
            <div class="mt-4">
                <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar Actividad
                </a>
                <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Crear Subactividad
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.comment-item {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}

.comment-content {
    font-size: 14px;
    line-height: 1.5;
}

.comment-meta {
    border-top: 1px solid #dee2e6;
    padding-top: 8px;
    margin-top: 8px;
}

.border-left {
    border-left: 4px solid #007bff !important;
}
</style>
@endsection
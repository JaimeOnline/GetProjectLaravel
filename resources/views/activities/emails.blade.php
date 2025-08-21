@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Correos de la Actividad: {{ $activity->name }}</h1>
        <div>
            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Editar
            </a>
            <a href="{{ route('activities.index') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Ver Actividades
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle"></i> Información de la Actividad
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Caso:</strong> {{ $activity->caso }}<br>
                    <strong>Estado:</strong> 
                    <span class="badge badge-{{ $activity->status == 'culminada' ? 'success' : ($activity->status == 'en_ejecucion' ? 'primary' : 'warning') }}">
                        {{ $activity->status_label }}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Fecha de Recepción:</strong> 
                    {{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('d/m/Y') : 'No especificada' }}
                </div>
            </div>
            @if($activity->description)
                <div class="mt-2">
                    <strong>Descripción:</strong> {{ $activity->description }}
                </div>
            @endif
        </div>
    </div>

    @if($emails->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-envelope"></i> Correos Relacionados ({{ $emails->count() }})
                </h5>
                <small class="text-muted">
                    Incluye correos de la actividad principal y todas sus subactividades, ordenados por fecha más reciente
                </small>
                <div class="mt-2">
                    @php
                        $sentCount = $emails->where('type', 'sent')->count();
                        $receivedCount = $emails->where('type', 'received')->count();
                    @endphp
                    <span class="badge badge-primary mr-2">
                        <i class="fas fa-paper-plane"></i> {{ $sentCount }} Enviados
                    </span>
                    <span class="badge badge-success">
                        <i class="fas fa-inbox"></i> {{ $receivedCount }} Recibidos
                    </span>
                </div>
            </div>
            <div class="card-body">
                @foreach($emails as $email)
                    <div class="border rounded p-3 mb-3 {{ $email->type == 'sent' ? 'border-primary' : 'border-success' }}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge badge-{{ $email->type == 'sent' ? 'primary' : 'success' }} mr-2">
                                        <i class="fas fa-{{ $email->type == 'sent' ? 'paper-plane' : 'inbox' }}"></i>
                                        {{ $email->type_label }}
                                    </span>
                                    <h6 class="mb-0">{{ $email->subject }}</h6>
                                </div>
                                
                                <div class="mb-2">
                                    <strong>{{ $email->type == 'sent' ? 'Para:' : 'De:' }}</strong> 
                                    {{ $email->sender_recipient ?: 'No especificado' }}
                                </div>
                                
                                <div class="mb-2">
                                    <strong>Actividad:</strong> 
                                    <span class="badge badge-light">{{ $email->activity->name }}</span>
                                </div>
                                
                                <div class="mb-2">
                                    <strong>Contenido:</strong>
                                    <div class="bg-light p-2 rounded mt-1" style="max-height: 150px; overflow-y: auto;">
                                        {!! nl2br(e($email->content)) !!}
                                    </div>
                                </div>
                                
                                @if($email->attachments && count($email->attachments) > 0)
                                    <div class="mb-2">
                                        <strong>Archivos Adjuntos:</strong>
                                        <ul class="list-unstyled mb-0 ml-3">
                                            @foreach($email->attachments as $index => $attachment)
                                                <li class="mb-1">
                                                    <i class="fas fa-paperclip text-primary"></i>
                                                    @if(is_array($attachment))
                                                        <a href="{{ route('emails.download', [$email, $index]) }}" 
                                                           class="text-decoration-none" target="_blank">
                                                            {{ $attachment['original_name'] }}
                                                        </a>
                                                        <small class="text-muted">
                                                            ({{ number_format($attachment['file_size'] / 1024, 1) }} KB)
                                                        </small>
                                                    @else
                                                        {{-- Compatibilidad con archivos antiguos (solo texto) --}}
                                                        <span class="text-muted">{{ $attachment }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-4 text-right">
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> 
                                        {{ $email->created_at->format('d/m/Y H:i:s') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        ({{ $email->created_at->diffForHumans() }})
                                    </small>
                                </div>
                                
                                <form action="{{ route('emails.destroy', $email) }}" method="POST" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar este correo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar correo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            No hay correos registrados para esta actividad y sus subactividades.
            <br>
            <small>Puedes agregar correos desde la página de edición de cada actividad.</small>
        </div>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <!-- Breadcrumbs -->
    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Actividades</a></li>
                <li class="breadcrumb-item"><a href="{{ route('activities.edit', $activity) }}">{{ $activity->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Correos</li>
            </ol>
        </nav>
    </div>

    <!-- Barra de Acciones -->
    <div class="action-bar">
        <div class="action-group">
            <h1 class="text-gradient mb-0">Correos de la Actividad</h1>
        </div>
        <div class="action-group">
            <div class="quick-nav">
                <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Ver Actividades
                </a>
                <a href="{{ route('activities.edit', $activity) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-edit"></i> Volver a Editar
                </a>
                <a href="{{ route('activities.comments', $activity) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-comments"></i> Comentarios
                </a>
            </div>
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
        </div>
    @endif

    <!-- Formulario para agregar nuevo correo -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus"></i> Agregar Nuevo Correo</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('activities.emails.store', $activity) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">
                                <i class="fas fa-exchange-alt text-primary"></i> Tipo de Correo
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="received">Correo Recibido</option>
                                <option value="sent">Correo Enviado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sender_recipient">
                                <i class="fas fa-user text-primary"></i> De/Para
                            </label>
                            <input type="email" class="form-control" id="sender_recipient" name="sender_recipient" 
                                   placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subject">
                        <i class="fas fa-tag text-primary"></i> Asunto
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="subject" name="subject" 
                           placeholder="Asunto del correo" required>
                </div>
                
                <div class="form-group">
                    <label for="content">
                        <i class="fas fa-align-left text-primary"></i> Contenido
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="content" name="content" rows="4" 
                              placeholder="Contenido del correo..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="attachments">
                        <i class="fas fa-paperclip text-primary"></i> Archivos Adjuntos
                    </label>
                    <input type="file" class="form-control-file" id="attachments" name="attachments[]" multiple
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar,.csv,.xml">
                    <small class="form-text text-muted">
                        Máximo 10MB por archivo. Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF, ZIP, RAR, CSV, XML
                    </small>
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-plus"></i> Agregar Correo
                            </button>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                El correo se agregará a la actividad actual
                            </small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
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
                <li class="breadcrumb-item active" aria-current="page">Ver: {{ $activity->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Barra de Acciones -->
    <div class="action-bar">
        <div class="action-group">
            <h1 class="text-gradient mb-0">Ver Actividad</h1>
        </div>
        <div class="action-group">
            <div class="quick-nav">
                <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-plus"></i> Crear Sub Actividad
                </a>
                <a href="{{ route('activities.comments', $activity) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-comments"></i> Comentarios
                </a>
                <a href="{{ route('activities.emails', $activity) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-envelope"></i> Correos
                </a>
            </div>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger fade-in">
            <h6><i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Pestañas de Navegación -->
    <ul class="nav nav-tabs section-tabs" id="activityTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                <i class="fas fa-info-circle"></i> Información Básica
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="requirements-tab" data-toggle="tab" href="#requirements" role="tab">
                <i class="fas fa-list-check"></i> Requerimientos
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments" role="tab">
                <i class="fas fa-comments"></i> Comentarios
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="emails-tab" data-toggle="tab" href="#emails" role="tab">
                <i class="fas fa-envelope"></i> Correos
            </a>
        </li>
    </ul>

    <div class="tab-content" id="activityTabsContent">
        <!-- Pestaña: Información Básica -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel">
            <form action="{{ route('activities.update', $activity) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Básica de la Actividad</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="caso">
                                        <i class="fas fa-hashtag text-primary"></i> Caso
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="caso" name="caso" value="{{ $activity->caso }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="status">
                                        <i class="fas fa-flag text-primary"></i> Estado
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="en_ejecucion" {{ $activity->status == 'en_ejecucion' ? 'selected' : '' }}>En ejecución</option>
                                        <option value="culminada" {{ $activity->status == 'culminada' ? 'selected' : '' }}>Culminada</option>
                                        <option value="en_espera_de_insumos" {{ $activity->status == 'en_espera_de_insumos' ? 'selected' : '' }}>En espera de insumos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="name">
                                <i class="fas fa-tag text-primary"></i> Nombre de la Actividad
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $activity->name }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="description">
                                <i class="fas fa-align-left text-primary"></i> Descripción
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe los detalles de la actividad...">{{ $activity->description }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-users text-primary"></i> Seleccionar Analistas
                                <span class="text-danger">*</span>
                            </label>
                            
                            <div class="analysts-selector" id="analysts-selector">
                                <div class="text-center mb-2">
                                    <i class="fas fa-user-friends fa-2x text-muted"></i>
                                    <p class="mb-1 font-weight-bold">Selecciona los analistas para esta actividad</p>
                                    <p class="text-muted mb-0">Haz clic en las tarjetas para seleccionar/deseleccionar</p>
                                </div>
                                
                                <div class="analysts-grid">
                                    @foreach ($analistas as $analista)
                                        <div class="analyst-card" 
                                             data-analyst-id="{{ $analista->id }}"
                                             data-analyst-name="{{ $analista->name }}">
                                            <div class="analyst-avatar">
                                                {{ strtoupper(substr($analista->name, 0, 2)) }}
                                            </div>
                                            <p class="analyst-name">{{ $analista->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Inputs ocultos para enviar los datos -->
                                <div id="selected-analysts-inputs">
                                    @if($activity->analistas)
                                        @foreach($activity->analistas as $analista)
                                            <input type="hidden" name="analista_id[]" value="{{ $analista->id }}">
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            
                            <div id="selected-analysts-summary" class="mt-2" style="display: none;">
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="selected-count">0</span> analista(s) seleccionado(s):
                                    <span id="selected-names" class="font-weight-bold"></span>
                                </small>
                            </div>
                            
                            @if($activity->analistas && $activity->analistas->count() == 0)
                                <small class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Esta actividad no tiene analistas asignados. Debes seleccionar al menos uno.
                                </small>
                            @endif
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="parent_id">
                                        <i class="fas fa-sitemap text-primary"></i> Actividad Padre
                                    </label>
                                    <select class="form-control" id="parent_id" name="parent_id">
                                        <option value="">Ninguna</option>
                                        @foreach ($activities as $parentActivity)
                                            <option value="{{ $parentActivity->id }}" {{ $activity->parent_id == $parentActivity->id ? 'selected' : '' }}>{{ $parentActivity->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="fecha_recepcion">
                                        <i class="fas fa-calendar text-primary"></i> Fecha de Recepción
                                    </label>
                                    <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" value="{{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de Actualizar para Información Básica -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Actualizar Información Básica
                                    </button>
                                </div>
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Los cambios se guardarán al hacer clic en "Actualizar"
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pestaña: Requerimientos -->
        <div class="tab-pane fade" id="requirements" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list-check"></i> Gestión de Requerimientos</h5>
                </div>
                <div class="card-body">
                    {{-- Mostrar requerimientos existentes --}}
                    @if ($activity->requirements->count() > 0)
                        <div class="form-group">
                            <label>Requerimientos Existentes</label>
                            <div class="card">
                                <div class="card-body">
                                    @foreach ($activity->requirements as $requirement)
                                        <div class="border-bottom pb-2 mb-2 d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <p class="mb-1">{{ $requirement->description }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $requirement->created_at->format('d/m/Y H:i:s') }}
                                                    <span class="ml-2">
                                                        ({{ $requirement->created_at->diffForHumans() }})
                                                    </span>
                                                </small>
                                            </div>
                                            <div class="ml-2">
                                                <form action="{{ route('requirements.destroy', $requirement) }}" method="POST" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este requerimiento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar requerimiento">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('activities.requirements.store', $activity) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="requirements">Agregar Nuevos Requerimientos</label>
                            <div id="requirements-container">
                                <div class="requirement-item mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="requirements[]" placeholder="Agrega nuevos requerimientos (deja vacío si no hay)">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-requirement" title="Eliminar requerimiento">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-requirement">
                                <i class="fas fa-plus"></i> Agregar Requerimiento
                            </button>
                            
                            <!-- Botón de Actualizar para Requerimientos -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Actualizar Requerimientos
                                        </button>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Los cambios se guardarán al hacer clic en "Actualizar"
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pestaña: Comentarios -->
        <div class="tab-pane fade" id="comments" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Gestión de Comentarios</h5>
                </div>
                <div class="card-body">
                    {{-- Mostrar comentarios existentes --}}
                    @if ($activity->comments->count() > 0)
                        <div class="form-group">
                            <label>Comentarios Existentes</label>
                            <div class="card">
                                <div class="card-body">
                                    @foreach ($activity->comments as $comment)
                                        <div class="border-bottom pb-2 mb-2 d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <p class="mb-1">{{ $comment->comment }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $comment->created_at->format('d/m/Y H:i:s') }}
                                                    <span class="ml-2">
                                                        ({{ $comment->created_at->diffForHumans() }})
                                                    </span>
                                                </small>
                                            </div>
                                            <div class="ml-2">
                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este comentario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar comentario">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('activities.comments.tab.store', $activity) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="comments">Agregar Nuevos Comentarios</label>
                            <div id="comments-container">
                                <div class="comment-item mb-2">
                                    <div class="input-group">
                                        <textarea class="form-control" name="comments[]" placeholder="Agrega nuevos comentarios (deja vacío si no hay)"></textarea>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-comment" title="Eliminar comentario">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-comment">
                                <i class="fas fa-plus"></i> Agregar Comentario
                            </button>
                            
                            <!-- Botón de Actualizar para Comentarios -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Actualizar Comentarios
                                        </button>
                                        <a href="{{ route('activities.comments', $activity) }}" class="btn btn-info btn-lg ml-2">
                                            <i class="fas fa-eye"></i> Ver Página de Comentarios
                                        </a>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Los cambios se guardarán al hacer clic en "Actualizar"
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pestaña: Correos -->
        <div class="tab-pane fade" id="emails" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-envelope"></i> Gestión de Correos</h5>
                </div>
                <div class="card-body">
                    <!-- Mostrar correos existentes -->
                    @if ($activity->emails->count() > 0)
                        <div class="mb-4">
                            <h6>Correos Existentes ({{ $activity->emails->count() }} total)</h6>
                            <div class="card">
                                <div class="card-body">
                                    @foreach ($activity->emails->sortByDesc('created_at')->take(5) as $email)
                                        <div class="border rounded p-3 mb-3 {{ $email->type == 'sent' ? 'border-primary' : 'border-success' }}">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge badge-{{ $email->type == 'sent' ? 'primary' : 'success' }} mr-2">
                                                            <i class="fas fa-{{ $email->type == 'sent' ? 'paper-plane' : 'inbox' }}"></i>
                                                            {{ $email->type == 'sent' ? 'Enviado' : 'Recibido' }}
                                                        </span>
                                                        <h6 class="mb-0">{{ $email->subject }}</h6>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <strong>{{ $email->type == 'sent' ? 'Para:' : 'De:' }}</strong> 
                                                        {{ $email->sender_recipient ?: 'No especificado' }}
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
                                    @if ($activity->emails->count() > 5)
                                        <div class="text-center mt-2">
                                            <a href="{{ route('activities.emails', $activity) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Ver todos los correos ({{ $activity->emails->count() }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Formulario para agregar nuevo correo -->
                    <form action="{{ route('activities.emails.store', $activity) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Agregar Nuevo Correo</label>
                            
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
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                <small class="form-text text-muted">
                                    Máximo 10MB por archivo. Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG, GIF, ZIP, RAR
                                </small>
                            </div>
                            
                            <!-- Botón de Agregar Correo -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-plus"></i> Agregar Correo
                                        </button>
                                        <a href="{{ route('activities.emails', $activity) }}" class="btn btn-info btn-lg ml-2">
                                            <i class="fas fa-eye"></i> Ver Todos los Correos
                                        </a>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            El correo se agregará al hacer clic en "Agregar Correo"
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit form JavaScript loaded');
    
    // ===== FUNCIONALIDAD DE PESTAÑAS =====
    
    // Inicializar pestañas de Bootstrap
    const tabLinks = document.querySelectorAll('#activityTabs .nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Manejar clicks en las pestañas
    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover clases activas de todas las pestañas
            tabLinks.forEach(link => link.classList.remove('active'));
            tabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Activar la pestaña clickeada
            this.classList.add('active');
            
            // Mostrar el contenido correspondiente
            const targetId = this.getAttribute('href').substring(1);
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });
    
    // Función para activar una pestaña específica
    function activateTab(tabId) {
        // Remover clases activas
        tabLinks.forEach(link => link.classList.remove('active'));
        tabPanes.forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Activar la pestaña específica
        const tabLink = document.querySelector(`#activityTabs .nav-link[href="#${tabId}"]`);
        const tabPane = document.getElementById(tabId);
        
        if (tabLink && tabPane) {
            tabLink.classList.add('active');
            tabPane.classList.add('show', 'active');
        }
    }
    
    // Verificar si hay una pestaña activa desde el servidor
    @if(session('active_tab'))
        activateTab('{{ session('active_tab') }}');
    @endif
    
    // Verificar si hay un hash en la URL para activar una pestaña específica
    if (window.location.hash) {
        const hashTab = window.location.hash.substring(1);
        if (['basic', 'requirements', 'comments', 'emails'].includes(hashTab)) {
            activateTab(hashTab);
        }
    }
    
    // ===== FUNCIONALIDAD DE REQUERIMIENTOS =====
    
    // Agregar requerimiento
    const addRequirementBtn = document.getElementById('add-requirement');
    if (addRequirementBtn) {
        addRequirementBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = document.getElementById('requirements-container');
            const newRequirement = document.createElement('div');
            newRequirement.classList.add('requirement-item', 'mb-2');
            newRequirement.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="requirements[]" placeholder="Descripción del requerimiento">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-requirement" title="Eliminar requerimiento">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRequirement);
            attachRemoveHandlers();
        });
    }

    // Agregar comentario
    const addCommentBtn = document.getElementById('add-comment');
    if (addCommentBtn) {
        addCommentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const container = document.getElementById('comments-container');
            const newComment = document.createElement('div');
            newComment.classList.add('comment-item', 'mb-2');
            newComment.innerHTML = `
                <div class="input-group">
                    <textarea class="form-control" name="comments[]" placeholder="Descripción del comentario"></textarea>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-comment" title="Eliminar comentario">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newComment);
            attachRemoveHandlers();
        });
    }

    // Función para adjuntar manejadores de eliminación
    function attachRemoveHandlers() {
        // Eliminar requerimientos - SOLO botones dentro de requirements-container
        const requirementsContainer = document.getElementById('requirements-container');
        if (requirementsContainer) {
            requirementsContainer.querySelectorAll('.remove-requirement').forEach(function(button) {
                // Remover listeners existentes para evitar duplicados
                button.removeEventListener('click', removeRequirement);
                button.addEventListener('click', removeRequirement);
            });
        }

        // Eliminar comentarios - SOLO botones dentro de comments-container
        const commentsContainer = document.getElementById('comments-container');
        if (commentsContainer) {
            commentsContainer.querySelectorAll('.remove-comment').forEach(function(button) {
                // Remover listeners existentes para evitar duplicados
                button.removeEventListener('click', removeComment);
                button.addEventListener('click', removeComment);
            });
        }
    }

    function removeRequirement(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const container = document.getElementById('requirements-container');
        if (container.children.length > 1) {
            const item = e.target.closest('.requirement-item');
            if (item) {
                item.remove();
            }
        } else {
            alert('Debe mantener al menos un campo de requerimiento.');
        }
    }

    function removeComment(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const container = document.getElementById('comments-container');
        if (container.children.length > 1) {
            const item = e.target.closest('.comment-item');
            if (item) {
                item.remove();
            }
        } else {
            alert('Debe mantener al menos un campo de comentario.');
        }
    }

    // ===== FUNCIONALIDAD DE SELECCIÓN DE ANALISTAS =====
    let selectedAnalysts = [];
    
    // Inicializar analistas seleccionados desde el servidor
    function initializeSelectedAnalysts() {
        const existingInputs = document.querySelectorAll('#selected-analysts-inputs input[name="analista_id[]"]');
        existingInputs.forEach(input => {
            const analystId = input.value;
            const analystCard = document.querySelector(`[data-analyst-id="${analystId}"]`);
            if (analystCard) {
                const analystName = analystCard.dataset.analystName;
                selectedAnalysts.push({ id: analystId, name: analystName });
                analystCard.classList.add('selected');
            }
        });
        updateAnalystsDisplay();
    }
    
    // Manejar clicks en las tarjetas de analistas
    document.addEventListener('click', function(e) {
        if (e.target.closest('.analyst-card')) {
            const card = e.target.closest('.analyst-card');
            const analystId = card.dataset.analystId;
            const analystName = card.dataset.analystName;
            
            if (card.classList.contains('selected')) {
                // Deseleccionar
                card.classList.remove('selected');
                selectedAnalysts = selectedAnalysts.filter(a => a.id !== analystId);
            } else {
                // Seleccionar
                card.classList.add('selected');
                selectedAnalysts.push({ id: analystId, name: analystName });
            }
            
            updateAnalystsDisplay();
        }
    });
    
    // Actualizar la visualización y los inputs ocultos
    function updateAnalystsDisplay() {
        const container = document.getElementById('selected-analysts-inputs');
        const summary = document.getElementById('selected-analysts-summary');
        const countSpan = document.getElementById('selected-count');
        const namesSpan = document.getElementById('selected-names');
        
        // Limpiar inputs existentes
        container.innerHTML = '';
        
        // Crear nuevos inputs
        selectedAnalysts.forEach(analyst => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'analista_id[]';
            input.value = analyst.id;
            container.appendChild(input);
        });
        
        // Actualizar resumen
        if (selectedAnalysts.length > 0) {
            countSpan.textContent = selectedAnalysts.length;
            namesSpan.textContent = selectedAnalysts.map(a => a.name).join(', ');
            summary.style.display = 'block';
        } else {
            summary.style.display = 'none';
        }
    }

    // Inicializar manejadores para elementos existentes
    attachRemoveHandlers();
    
    // Inicializar analistas seleccionados
    initializeSelectedAnalysts();
});
</script>
@endsection
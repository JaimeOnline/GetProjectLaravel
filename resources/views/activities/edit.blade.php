@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Editar Actividad</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('activities.update', $activity) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="caso">Caso</label>
            <input type="text" class="form-control" id="caso" name="caso" value="{{ $activity->caso }}" required>
        </div>
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $activity->name }}" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea class="form-control" id="description" name="description">{{ $activity->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="status">Estado</label>
            <select class="form-control" id="status" name="status" required>
                <option value="en_ejecucion" {{ $activity->status == 'en_ejecucion' ? 'selected' : '' }}>En ejecución</option>
                <option value="culminada" {{ $activity->status == 'culminada' ? 'selected' : '' }}>Culminada</option>
                <option value="en_espera_de_insumos" {{ $activity->status == 'en_espera_de_insumos' ? 'selected' : '' }}>En espera de insumos</option>
            </select>
        </div>
        <div class="form-group">
            <label for="analista_id">Analistas</label>
            <select class="form-control" id="analista_id" name="analista_id[]" multiple required>
                @foreach ($analistas as $analista)
                    <option value="{{ $analista->id }}" 
                        {{ $activity->analistas && in_array($analista->id, $activity->analistas->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $analista->name }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples analistas.
                @if($activity->analistas && $activity->analistas->count() == 0)
                    <span class="text-warning">⚠️ Esta actividad no tiene analistas asignados. Debes seleccionar al menos uno.</span>
                @endif
            </small>
        </div>
        <div class="form-group">
            <label for="parent_id">Actividad Padre</label>
            <select class="form-control" id="parent_id" name="parent_id">
                <option value="">Ninguna</option>
                @foreach ($activities as $parentActivity)
                    <option value="{{ $parentActivity->id }}" {{ $activity->parent_id == $parentActivity->id ? 'selected' : '' }}>{{ $parentActivity->name }}</option>
                @endforeach
            </select>
        </div>
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
        </div>
        
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
                <div class="mt-2">
                    <a href="{{ route('activities.comments', $activity) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Ver todos los comentarios
                    </a>
                </div>
            </div>
        @endif

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
        </div>
        <div class="form-group">
            <label for="fecha_recepcion">Fecha de Recepción</label>
            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" value="{{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('Y-m-d') : '' }}">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Actividad</button>
    </form>

    {{-- Sección de Correos --}}
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Gestión de Correos</h3>
            <a href="{{ route('activities.emails', $activity) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Todos los Correos
            </a>
        </div>

        {{-- Mostrar correos existentes --}}
        @if ($activity->emails->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Correos Existentes ({{ $activity->emails->count() }})</h5>
                </div>
                <div class="card-body">
                    @foreach ($activity->emails->sortByDesc('created_at')->take(3) as $email)
                        <div class="border-bottom pb-3 mb-3 {{ $loop->last ? 'border-bottom-0 mb-0 pb-0' : '' }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge badge-{{ $email->type == 'sent' ? 'primary' : 'success' }} mr-2">
                                            <i class="fas fa-{{ $email->type == 'sent' ? 'paper-plane' : 'inbox' }}"></i>
                                            {{ $email->type_label }}
                                        </span>
                                        <h6 class="mb-0">{{ $email->subject }}</h6>
                                    </div>
                                    <div class="mb-1">
                                        <strong>{{ $email->type == 'sent' ? 'Para:' : 'De:' }}</strong> {{ $email->sender_recipient ?: 'No especificado' }}
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">{{ Str::limit($email->content, 100) }}</small>
                                    </div>
                                    @if($email->attachments && count($email->attachments) > 0)
                                        <div class="mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-paperclip"></i> {{ count($email->attachments) }} archivo(s) adjunto(s)
                                                @foreach($email->attachments as $index => $attachment)
                                                    @if(is_array($attachment))
                                                        <br>
                                                        <a href="{{ route('emails.download', [$email, $index]) }}" 
                                                           class="text-decoration-none text-primary" target="_blank">
                                                            <i class="fas fa-download"></i> {{ $attachment['original_name'] }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-right">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock"></i> {{ $email->created_at->format('d/m/Y H:i:s') }}
                                    </small>
                                    <small class="text-muted d-block mb-2">
                                        ({{ $email->created_at->diffForHumans() }})
                                    </small>
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
                    @if($activity->emails->count() > 3)
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Mostrando los 3 correos más recientes. 
                                <a href="{{ route('activities.emails', $activity) }}">Ver todos ({{ $activity->emails->count() }})</a>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Formulario para agregar nuevo correo --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Agregar Nuevo Correo</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('activities.emails.store', $activity) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_type">Tipo de Correo</label>
                                <select class="form-control" id="email_type" name="type" required>
                                    <option value="">-- Seleccionar Tipo --</option>
                                    <option value="sent">Enviado</option>
                                    <option value="received">Recibido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_sender_recipient">
                                    <span id="sender_recipient_label">Remitente/Destinatario</span>
                                </label>
                                <input type="text" class="form-control" id="email_sender_recipient" 
                                       name="sender_recipient" placeholder="Dirección de correo (opcional)">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_subject">Asunto</label>
                        <input type="text" class="form-control" id="email_subject" name="subject" 
                               placeholder="Asunto del correo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_content">Contenido</label>
                        <textarea class="form-control" id="email_content" name="content" rows="4" 
                                  placeholder="Contenido del correo" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_attachments">Archivos Adjuntos</label>
                        
                        <!-- Zona de arrastrar y soltar -->
                        <div id="drop-zone" class="border border-dashed border-primary rounded p-4 mb-3 text-center" 
                             style="min-height: 120px; background-color: #f8f9fa; transition: all 0.3s ease;">
                            <div id="drop-zone-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                <p class="mb-1"><strong>Arrastra y suelta archivos aquí</strong></p>
                                <p class="text-muted mb-2">o</p>
                                <button type="button" class="btn btn-primary" id="browse-files">
                                    <i class="fas fa-folder-open"></i> Seleccionar Archivos
                                </button>
                                <input type="file" id="multiple-file-input" name="attachments[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar" 
                                       style="display: none;">
                            </div>
                        </div>
                        
                        <!-- Lista de archivos seleccionados -->
                        <div id="selected-files-list" class="mb-3" style="display: none;">
                            <h6>Archivos Seleccionados:</h6>
                            <div id="files-preview" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                <!-- Los archivos se mostrarán aquí -->
                            </div>
                        </div>
                        
                        <!-- Contenedor tradicional (oculto por defecto) -->
                        <div id="attachments-container" style="display: none;">
                            <div class="attachment-item mb-2">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="attachments[]" 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                        <label class="custom-file-label">Seleccionar archivo...</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-attachment" title="Eliminar archivo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" id="add-attachment">
                                <i class="fas fa-plus"></i> Agregar Archivo Individual
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="clear-all-files" style="display: none;">
                                <i class="fas fa-trash-alt"></i> Limpiar Todo
                            </button>
                        </div>
                        
                        <small class="form-text text-muted">
                            <strong>Formatos permitidos:</strong> PDF, Word, Excel, imágenes, archivos comprimidos. 
                            <strong>Tamaño máximo:</strong> 10MB por archivo.
                            <br><strong>Tip:</strong> Puedes arrastrar múltiples archivos desde tu explorador de archivos.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-envelope"></i> Guardar Correo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit form JavaScript loaded');
    
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

    // === FUNCIONALIDAD DE CORREOS ===
    
    // Cambiar etiqueta según tipo de correo
    const emailTypeSelect = document.getElementById('email_type');
    const senderRecipientLabel = document.getElementById('sender_recipient_label');
    const senderRecipientInput = document.getElementById('email_sender_recipient');
    
    if (emailTypeSelect && senderRecipientLabel && senderRecipientInput) {
        emailTypeSelect.addEventListener('change', function() {
            if (this.value === 'sent') {
                senderRecipientLabel.textContent = 'Destinatario';
                senderRecipientInput.placeholder = 'Correo del destinatario (opcional)';
            } else if (this.value === 'received') {
                senderRecipientLabel.textContent = 'Remitente';
                senderRecipientInput.placeholder = 'Correo del remitente (opcional)';
            } else {
                senderRecipientLabel.textContent = 'Remitente/Destinatario';
                senderRecipientInput.placeholder = 'Dirección de correo (opcional)';
            }
        });
    }

    // Variables globales para manejo de archivos
    let selectedFiles = [];
    let fileCounter = 0;

    // Elementos del DOM
    const dropZone = document.getElementById('drop-zone');
    const multipleFileInput = document.getElementById('multiple-file-input');
    const browseFilesBtn = document.getElementById('browse-files');
    const selectedFilesList = document.getElementById('selected-files-list');
    const filesPreview = document.getElementById('files-preview');
    const clearAllBtn = document.getElementById('clear-all-files');
    const attachmentsContainer = document.getElementById('attachments-container');

    // Configurar drag & drop
    if (dropZone) {
        // Prevenir comportamiento por defecto
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Efectos visuales
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        // Manejar drop
        dropZone.addEventListener('drop', handleDrop, false);
    }

    // Botón para seleccionar archivos
    if (browseFilesBtn) {
        browseFilesBtn.addEventListener('click', function() {
            multipleFileInput.click();
        });
    }

    // Input de archivos múltiples
    if (multipleFileInput) {
        multipleFileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    // Botón limpiar todo
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            clearAllFiles();
        });
    }

    // Funciones auxiliares
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.style.backgroundColor = '#e3f2fd';
        dropZone.style.borderColor = '#2196f3';
        dropZone.style.transform = 'scale(1.02)';
    }

    function unhighlight(e) {
        dropZone.style.backgroundColor = '#f8f9fa';
        dropZone.style.borderColor = '#007bff';
        dropZone.style.transform = 'scale(1)';
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        ([...files]).forEach(addFile);
        updateFilesDisplay();
    }

    function addFile(file) {
        // Validar tipo de archivo
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'application/zip',
            'application/x-rar-compressed'
        ];

        if (!allowedTypes.includes(file.type) && !isValidFileExtension(file.name)) {
            alert(`Tipo de archivo no permitido: ${file.name}`);
            return;
        }

        // Validar tamaño (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert(`Archivo muy grande: ${file.name}. Máximo 10MB.`);
            return;
        }

        // Agregar archivo a la lista
        const fileId = 'file_' + (++fileCounter);
        selectedFiles.push({
            id: fileId,
            file: file,
            name: file.name,
            size: file.size
        });
    }

    function isValidFileExtension(filename) {
        const validExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.txt', '.jpg', '.jpeg', '.png', '.gif', '.zip', '.rar'];
        const extension = filename.toLowerCase().substring(filename.lastIndexOf('.'));
        return validExtensions.includes(extension);
    }

    function updateFilesDisplay() {
        if (selectedFiles.length === 0) {
            selectedFilesList.style.display = 'none';
            clearAllBtn.style.display = 'none';
            return;
        }

        selectedFilesList.style.display = 'block';
        clearAllBtn.style.display = 'inline-block';

        filesPreview.innerHTML = '';
        selectedFiles.forEach((fileObj, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'file-item d-flex justify-content-between align-items-center p-2 mb-1 bg-light rounded';
            fileDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-file text-primary mr-2"></i>
                    <div>
                        <div class="font-weight-bold">${fileObj.name}</div>
                        <small class="text-muted">${formatFileSize(fileObj.size)}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('${fileObj.id}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            filesPreview.appendChild(fileDiv);
        });

        // Crear inputs ocultos para el formulario
        createHiddenInputs();
    }

    function createHiddenInputs() {
        // Limpiar inputs existentes
        const existingInputs = document.querySelectorAll('input[name="attachments[]"][type="file"].hidden-file-input');
        existingInputs.forEach(input => input.remove());

        if (selectedFiles.length === 0) {
            return;
        }

        // Usar el input múltiple existente y asignarle todos los archivos
        if (multipleFileInput && selectedFiles.length > 0) {
            try {
                const dt = new DataTransfer();
                selectedFiles.forEach(fileObj => {
                    dt.items.add(fileObj.file);
                });
                multipleFileInput.files = dt.files;
            } catch (error) {
                console.error('Error asignando archivos al input múltiple:', error);
                // Fallback: crear inputs individuales
                createIndividualInputs();
            }
        }
    }

    function createIndividualInputs() {
        selectedFiles.forEach((fileObj, index) => {
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'attachments[]';
            input.className = 'hidden-file-input';
            input.style.display = 'none';
            
            try {
                const dt = new DataTransfer();
                dt.items.add(fileObj.file);
                input.files = dt.files;
                
                const form = document.querySelector('form[action*="emails"]');
                if (form) {
                    form.appendChild(input);
                }
            } catch (error) {
                console.error('Error creando input individual:', error);
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function removeFile(fileId) {
        selectedFiles = selectedFiles.filter(f => f.id !== fileId);
        updateFilesDisplay();
    }

    function clearAllFiles() {
        selectedFiles = [];
        updateFilesDisplay();
        multipleFileInput.value = '';
    }

    // Hacer removeFile global para que funcione desde el HTML
    window.removeFile = removeFile;

    // Agregar archivo adjunto individual (método tradicional)
    const addAttachmentBtn = document.getElementById('add-attachment');
    if (addAttachmentBtn) {
        addAttachmentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Mostrar el contenedor tradicional
            attachmentsContainer.style.display = 'block';
            
            const newAttachment = document.createElement('div');
            newAttachment.classList.add('attachment-item', 'mb-2');
            newAttachment.innerHTML = `
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="attachments[]" 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                        <label class="custom-file-label">Seleccionar archivo...</label>
                    </div>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-attachment" title="Eliminar archivo">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            attachmentsContainer.appendChild(newAttachment);
            attachEmailHandlers();
        });
    }

    // Función para adjuntar manejadores de correos
    function attachEmailHandlers() {
        const attachmentsContainer = document.getElementById('attachments-container');
        if (attachmentsContainer) {
            attachmentsContainer.querySelectorAll('.remove-attachment').forEach(function(button) {
                button.removeEventListener('click', removeAttachment);
                button.addEventListener('click', removeAttachment);
            });
            
            // Manejar cambio de archivos para mostrar el nombre
            attachmentsContainer.querySelectorAll('.custom-file-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    const fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
                    const label = this.nextElementSibling;
                    label.textContent = fileName;
                });
            });
        }
    }

    function removeAttachment(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const container = document.getElementById('attachments-container');
        if (container.children.length > 1) {
            const item = e.target.closest('.attachment-item');
            if (item) {
                item.remove();
            }
        } else {
            // Limpiar el campo en lugar de eliminarlo si es el último
            const input = container.querySelector('input[name="attachments[]"]');
            if (input) {
                input.value = '';
            }
        }
    }

    // Inicializar manejadores para elementos existentes
    attachRemoveHandlers();
    attachEmailHandlers();
    
    // Inicializar el primer campo de archivo
    const firstFileInput = document.querySelector('.custom-file-input');
    if (firstFileInput) {
        firstFileInput.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
            const label = this.nextElementSibling;
            label.textContent = fileName;
        });
    }


    
    // Asegurar que el botón de submit funcione correctamente
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"].btn-primary');
    
    if (submitBtn && form) {
        submitBtn.addEventListener('click', function(e) {
            // Forzar el envío del formulario para asegurar que funcione
            form.submit();
        });
    }
});
</script>
@endsection
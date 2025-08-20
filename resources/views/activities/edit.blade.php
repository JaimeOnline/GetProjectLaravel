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
            <label for="user_id">Usuario Asignado</label>
            <select class="form-control" id="user_id" name="user_id[]" multiple required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" 
                        {{ $activity->users && in_array($user->id, $activity->users->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">
                Mantén presionado Ctrl (o Cmd en Mac) para seleccionar múltiples usuarios.
                @if($activity->users && $activity->users->count() == 0)
                    <span class="text-warning">⚠️ Esta actividad no tiene usuarios asignados. Debes seleccionar al menos uno.</span>
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

    // Inicializar manejadores para elementos existentes
    attachRemoveHandlers();
    
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
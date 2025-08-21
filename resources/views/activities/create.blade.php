@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>{{ isset($parentActivity) ? 'Crear Subactividad para: ' . $parentActivity->name : 'Crear Nueva Actividad' }}
        </h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('activities.store') }}" method="POST">
            @csrf
            @if (isset($parentActivity))
                <input type="hidden" name="parent_id" value="{{ $parentActivity->id }}">
                <div class="alert alert-info">
                    <strong>Actividad Padre:</strong> {{ $parentActivity->name }}
                </div>
            @endif
            <div class="form-group">
                <label for="caso">Caso</label>
                <input type="text" class="form-control" id="caso" name="caso" required>
            </div>
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="status">Estado</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="en_ejecucion">En ejecución</option>
                    <option value="culminada">Culminada</option>
                    <option value="en_espera_de_insumos">En espera de insumos</option>
                </select>
            </div>
            <div class="form-group">
                <label for="analista_id">Analistas</label>
                <select class="form-control" id="analista_id" name="analista_id[]" multiple required>
                    @foreach ($analistas as $analista)
                        <option value="{{ $analista->id }}">{{ $analista->name }}</option>
                    @endforeach
                </select>
            </div>
            @if (!isset($parentActivity))
                <div class="form-group">
                    <label for="parent_id">Actividad Padre</label>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option value="">-- Seleccionar Actividad Padre (Opcional) --</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label for="requirements">Requerimientos</label>
                <div id="requirements-container">
                    <div class="requirement-item mb-2">
                        <div class="input-group">
                            <textarea class="form-control" name="requirements[]" placeholder="Agrega los requerimientos (deja vacío si no hay)"></textarea>
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
            <div class="form-group">
                <label for="comments">Comentarios</label>
                <div id="comments-container">
                    <div class="comment-item mb-2">
                        <div class="input-group">
                            <textarea class="form-control" name="comments[]" placeholder="Agrega comentarios (deja vacío si no hay)"></textarea>
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
                <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" value="{{ date('Y-m-d') }}">
            </div>
            <button type="submit" class="btn btn-primary">Crear Actividad</button>
        </form>
    </div>
    <script>
        // Agregar requerimiento
        document.getElementById('add-requirement').addEventListener('click', function() {
            var container = document.getElementById('requirements-container');
            var newRequirement = document.createElement('div');
            newRequirement.classList.add('requirement-item', 'mb-2');
            newRequirement.innerHTML = `
                <div class="input-group">
                    <textarea class="form-control" name="requirements[]" placeholder="Descripción del requerimiento"></textarea>
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

        // Agregar comentario
        document.getElementById('add-comment').addEventListener('click', function() {
            var container = document.getElementById('comments-container');
            var newComment = document.createElement('div');
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

        // Función para adjuntar manejadores de eliminación
        function attachRemoveHandlers() {
            // Eliminar requerimientos
            document.querySelectorAll('.remove-requirement').forEach(function(button) {
                button.removeEventListener('click', removeRequirement);
                button.addEventListener('click', removeRequirement);
            });

            // Eliminar comentarios
            document.querySelectorAll('.remove-comment').forEach(function(button) {
                button.removeEventListener('click', removeComment);
                button.addEventListener('click', removeComment);
            });
        }

        function removeRequirement(e) {
            e.preventDefault();
            e.stopPropagation();
            var container = document.getElementById('requirements-container');
            if (container.children.length > 1) {
                e.target.closest('.requirement-item').remove();
            } else {
                alert('Debe mantener al menos un campo de requerimiento.');
            }
        }

        function removeComment(e) {
            e.preventDefault();
            e.stopPropagation();
            var container = document.getElementById('comments-container');
            if (container.children.length > 1) {
                e.target.closest('.comment-item').remove();
            } else {
                alert('Debe mantener al menos un campo de comentario.');
            }
        }

        // Inicializar manejadores
        attachRemoveHandlers();
    </script>
@endsection

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
                    <option value="{{ $user->id }}" {{ in_array($user->id, $activity->users->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
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
        <div class="form-group">
            <label for="requirements">Requerimientos</label>
            <div id="requirements-container">
                @foreach ($activity->requirements as $requirement)
                    <input type="text" class="form-control" name="requirements[]" value="{{ $requirement->description }}">
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary" id="add-requirement">Agregar Requerimiento</button>
        </div>
        
        {{-- Mostrar comentarios existentes --}}
        @if ($activity->comments->count() > 0)
            <div class="form-group">
                <label>Comentarios Existentes</label>
                <div class="card">
                    <div class="card-body">
                        @foreach ($activity->comments as $comment)
                            <div class="border-bottom pb-2 mb-2">
                                <p class="mb-1">{{ $comment->comment }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    {{ $comment->created_at->format('d/m/Y H:i:s') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="form-group">
            <label for="comments">Agregar Nuevos Comentarios</label>
            <div id="comments-container">
                <textarea class="form-control" name="comments[]" placeholder="Agrega nuevos comentarios (deja vacío si no hay)"></textarea>
            </div>
            <button type="button" class="btn btn-secondary" id="add-comment">Agregar Comentario</button>
        </div>
        <div class="form-group">
            <label for="fecha_recepcion">Fecha de Recepción</label>
            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" value="{{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('Y-m-d') : '' }}">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Actividad</button>
    </form>
</div>
<script>
    document.getElementById('add-requirement').addEventListener('click', function() {
        var container = document.getElementById('requirements-container');
        var newRequirement = document.createElement('div');
        newRequirement.classList.add('requirement');
        newRequirement.innerHTML =
            '<input type="text" class="form-control" name="requirements[]" placeholder="Descripción del requerimiento" required>';
        container.appendChild(newRequirement);
    });

    document.getElementById('add-comment').addEventListener('click', function() {
        var container = document.getElementById('comments-container');
        var newComment = document.createElement('div');
        newComment.classList.add('comment');
        newComment.innerHTML =
            '<textarea class="form-control" name="comments[]" placeholder="Descripción del comentario" required></textarea>';
        container.appendChild(newComment);
    });
</script>
@endsection
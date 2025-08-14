@extends('layouts.app')
@section('content')
    <div class="container">
        <h1>Crear Nueva Actividad</h1>
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
                <label for="user_id">Usuario Asignado</label>
                <select class="form-control" id="user_id" name="user_id[]" multiple required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            blade
            <div class="form-group">
                <label for="parent_id">Actividad Padre</label>
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="">Ninguna</option>
                    @foreach ($activities as $parentActivity)
                        <option value="{{ $parentActivity->id }}"
                            {{ $activity->parent_id == $parentActivity->id ? 'selected' : '' }}>{{ $parentActivity->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="requirements">Requerimientos</label>
                <div id="requirements-container">
                    <textarea class="form-control" name="requirements[]" placeholder="Agrega los requerimientos (deja vacío si no hay)"></textarea>
                </div>
                <button type="button" class="btn btn-secondary" id="add-requirement">Agregar Requerimiento</button>
            </div>
            <div class="form-group">
                <label for="fecha_recepcion">Fecha de Recepción</label>
                <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion">
            </div>
            <button type="submit" class="btn btn-primary">Crear Actividad</button>
        </form>
    </div>
    <script>
        document.getElementById('add-requirement').addEventListener('click', function() {
            var container = document.getElementById('requirements-container');
            var newRequirement = document.createElement('div');
            newRequirement.classList.add('requirement');
            newRequirement.innerHTML =
                '<textarea class="form-control" name="requirements[]" placeholder="Descripción del requerimiento" required></textarea>';
            container.appendChild(newRequirement);
        });
    </script>
@endsection

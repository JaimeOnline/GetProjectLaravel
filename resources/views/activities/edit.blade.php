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
                    <option value="En ejecución" {{ $activity->status == 'En ejecución' ? 'selected' : '' }}>En ejecución
                    </option>
                    <option value="Culminada" {{ $activity->status == 'Culminada' ? 'selected' : '' }}>Culminada</option>
                    <option value="En espera de insumos"
                        {{ $activity->status == 'En espera de insumos' ? 'selected' : '' }}>En espera de insumos</option>
                </select>
            </div>
            <div class="form-group">
                <label for="user_id">Usuario Asignado</label>
                <select class="form-control" id="user_id" name="user_id[]" multiple required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ in_array($user->id, $activity->users->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Actividad</button>
        </form>
    </div>
@endsection

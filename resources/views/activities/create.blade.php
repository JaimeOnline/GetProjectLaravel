@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Crear Nueva Actividad</h1>
    @if($errors->any())
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
        <button type="submit" class="btn btn-primary">Crear Actividad</button>
    </form>
</div>
@endsection
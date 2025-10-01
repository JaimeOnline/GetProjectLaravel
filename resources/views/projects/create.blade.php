@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Crear Proyecto</h1>
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre del Proyecto</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <!-- Puedes agregar más campos aquí -->
            <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
        </form>
    </div>
@endsection

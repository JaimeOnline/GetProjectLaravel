@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Actividades</h1>
    <a href="{{ route('activities.create') }}" class="btn btn-primary">Crear Nueva Actividad</a>
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Usuarios Asignados</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
            <tr>
                <td>{{ $activity->id }}</td>
                <td>{{ $activity->name }}</td>
                <td>{{ $activity->description }}</td>
                <td>{{ $activity->status }}</td>
                <td>
                    @if($activity->users->isEmpty())
                        Sin usuarios asignados
                    @else
                        @foreach ($activity->users as $user)
                            <span>{{ $user->name }}</span>@if (!$loop->last), @endif <!-- Muestra los nombres de los usuarios asignados, separados por comas -->
                        @endforeach
                    @endif
                </td>
                <td>
                    <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
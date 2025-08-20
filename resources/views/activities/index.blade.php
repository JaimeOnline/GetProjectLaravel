blade
{{-- resources/views/activities/index.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Actividades</h1>
    <a href="{{ route('activities.create') }}" class="btn btn-primary">Crear Nueva Actividad</a>
    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Caso</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Usuarios Asignados</th>
                <th>Fecha de Recepción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
                <tr>
                    <td>{{ $activity->caso }}</td>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->description }}</td>
                    <td>{{ $activity->status_label }}</td>
                    <td>
                        @if ($activity->users->isEmpty())
                            Sin usuarios asignados
                        @else
                            @foreach ($activity->users as $user)
                                <span>{{ $user->name }}</span>
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('d-m-Y') : 'No asignada' }}</td>
                    <td>
                        <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                        <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" class="btn btn-secondary">Crear Subactividad</a>
                    </td>
                </tr>
                {{-- Mostrar subactividades --}}
                @foreach ($activity->subactivities as $subactivity)
                    <tr>
                        <td></td> {{-- Espacio vacío para alinear con las columnas de la tabla --}}
                        <td><strong>{{ $subactivity->name }}</strong></td>
                        <td>{{ $subactivity->description }}</td>
                        <td>{{ $subactivity->status }}</td>
                        <td>
                            @if ($subactivity->users->isEmpty())
                                Sin usuarios asignados
                            @else
                                @foreach ($subactivity->users as $user)
                                    <span>{{ $user->name }}</span>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>{{ $subactivity->fecha_recepcion ? $subactivity->fecha_recepcion->format('d-m-Y') : 'No asignada' }}</td>
                        <td>
                            <a href="{{ route('activities.create', ['parentId' => $subactivity->id]) }}" class="btn btn-secondary">Crear Subactividad</a>
                            <a href="{{ route('activities.edit', $subactivity) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('activities.destroy', $subactivity) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection
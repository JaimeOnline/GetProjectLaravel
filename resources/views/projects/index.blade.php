@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <h1 class="mb-4">Proyectos</h1>
        @if ($proyectos->isEmpty())
            <p class="no-proyectos">
                No hay proyectos aún.
                <a href="{{ route('projects.create') }}">Comienza creando uno</a>
            </p>
        @else
            <ul class="listado-proyectos">
                @foreach ($proyectos as $proyecto)
                    <li class="proyecto">
                        <a href="{{ route('activities.index', ['proyecto_id' => $proyecto->id]) }}">
                            {{ $proyecto->nombre }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection

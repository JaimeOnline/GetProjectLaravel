@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Proyectos</li>
                </ol>
            </nav>
        </div>

        <!-- Barra de Acciones -->
        <div class="action-bar">
            <div class="action-group">
                <h1 class="text-gradient mb-0">Proyectos</h1>
            </div>
            <div class="action-group">
                <div class="quick-nav">
                    <a href="{{ route('projects.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Crear Proyecto
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success fade-in">
                {{ session('success') }}
            </div>
        @endif

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

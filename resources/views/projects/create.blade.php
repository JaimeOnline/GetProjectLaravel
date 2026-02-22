@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Crear Proyecto</h1>
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <div class="form-group">
            @section('content')
                <div class="container">
                    <!-- Breadcrumbs -->
                    <div class="breadcrumb-container">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Actividades</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Crear Proyecto</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Barra de Acciones -->
                    <div class="action-bar">
                        <div class="action-group">
                            <h1 class="text-gradient mb-0">Crear Proyecto</h1>
                        </div>
                        <div class="action-group">
                            <div class="quick-nav">
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-gradient-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-project-diagram"></i> Información del Proyecto
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label" for="nombre">
                                        <i class="fas fa-tag text-primary"></i> Nombre del Proyecto <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nombre" id="nombre" class="form-control"
                                        value="{{ old('nombre') }}" required>
                                </div>
                                <!-- Puedes agregar más campos aquí -->

                                <div class="mt-4 pt-3 border-top d-flex justify-content-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-save"></i> Guardar Proyecto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endsection <label for="nombre">Nombre del Proyecto</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        <!-- Puedes agregar más campos aquí -->
        <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
    </form>
</div>
@endsection

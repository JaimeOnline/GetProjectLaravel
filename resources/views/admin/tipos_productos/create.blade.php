@extends('layouts.app')

@section('content')
    <div class="card fade-in">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-boxes mr-2"></i> Nuevo Tipo de Producto
            </h5>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <p><strong>Revisa los siguientes errores:</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tipos-productos.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre del Tipo de Producto</label>
                    <input type="text" name="nombre" id="nombre"
                        class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <a href="{{ route('admin.tipos-productos.index') }}" class="btn btn-secondary">
                        Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

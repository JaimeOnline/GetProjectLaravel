@extends('layouts.app')

@section('content')
    <div class="card fade-in">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-user-plus mr-2"></i> Nuevo Analista
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

            <form action="{{ route('admin.analistas.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nombre del Analista</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <a href="{{ route('admin.analistas.index') }}" class="btn btn-secondary">
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

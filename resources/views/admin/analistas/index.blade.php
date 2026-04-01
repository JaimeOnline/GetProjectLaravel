@extends('layouts.app')

@section('content')
    <div class="card fade-in">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-user-tie mr-2"></i> Analistas
            </h5>
            <a href="{{ route('admin.analistas.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Nuevo Analista
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if ($analistas->count())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nombre</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analistas as $analista)
                                <tr>
                                    <td>{{ $analista->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.analistas.edit', $analista) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.analistas.destroy', $analista) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este analista?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $analistas->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    No hay analistas registrados aún.
                </div>
            @endif
        </div>
    </div>
@endsection

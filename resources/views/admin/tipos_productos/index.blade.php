@extends('layouts.app')

@section('content')
    <div class="card fade-in">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-boxes mr-2"></i> Tipos de Producto
            </h5>
            <a href="{{ route('admin.tipos-productos.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Nuevo Tipo de Producto
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if ($tipos->count())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Nombre</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tipos as $tipo)
                                <tr>
                                    <td>{{ $tipo->nombre }}</td>
                                    <td>
                                        <a href="{{ route('admin.tipos-productos.edit', $tipo) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.tipos-productos.destroy', $tipo) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este tipo de producto?');">
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
                    {{ $tipos->links() }}
                </div>
            @else
                <div class="alert alert-info mb-0">
                    No hay tipos de producto registrados aún.
                </div>
            @endif
        </div>
    </div>
@endsection

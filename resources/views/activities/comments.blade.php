@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activities.edit', $activity) }}">{{ $activity->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Comentarios</li>
                </ol>
            </nav>
        </div>

        <!-- Barra de Acciones -->
        <div class="action-bar">
            <div class="action-group">
                <h1 class="text-gradient mb-0">Comentarios de la Actividad</h1>
            </div>
            <div class="action-group">
                <div class="quick-nav">
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> Ver Actividades
                    </a>
                    <a href="{{ route('activities.edit', $activity) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-edit"></i> Volver a Editar
                    </a>
                    <a href="{{ route('activities.emails', $activity) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-envelope"></i> Correos
                    </a>
                </div>
            </div>
        </div>
        {{-- Mensajes de éxito --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Información de la actividad --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-tasks"></i>
                    {{ $activity->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Caso:</strong> {{ $activity->caso }}</p>
                        <p><strong>Estado:</strong>
                            <span
                                class="badge badge-{{ $activity->status == 'culminada' ? 'success' : ($activity->status == 'en_ejecucion' ? 'warning' : 'info') }}">
                                {{ $activity->status_label }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha de Recepción:</strong>
                            {{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('d/m/Y') : 'No asignada' }}
                        </p>
                        <p><strong>Total de Comentarios:</strong>
                            <span class="badge badge-info">{{ $activity->comments->count() }}</span>
                        </p>
                    </div>
                </div>
                @if ($activity->description)
                    <p><strong>Descripción:</strong> {{ $activity->description }}</p>
                @endif
            </div>
        </div>

        {{-- Formulario para agregar nuevo comentario --}}
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle"></i>
                    Agregar Nuevo Comentario
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('activities.comments.store', $activity) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="comment">Comentario</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Escribe tu comentario aquí..."
                            required>{{ old('comment') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Agregar Comentario
                    </button>
                </form>
            </div>
        </div>

        {{-- Lista de comentarios --}}
        @if ($activity->comments->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i>
                        Historial de Comentarios ({{ $activity->comments->count() }})
                    </h5>
                    <button type="button" id="toggle-order-btn" class="btn btn-sm btn-light text-dark mr-2"
                        data-order="desc">
                        Ordenar por fecha: Más recientes primero
                    </button>
                </div>
                <div class="card-body" id="comments-list">
                    @foreach ($activity->comments as $comment)
                        <div class="comment-item border-left border-primary pl-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="comment-content flex-grow-1">
                                    <p class="mb-2">{{ $comment->comment }}</p>
                                </div>
                                <div class="comment-actions ml-2">
                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este comentario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar comentario">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="comment-meta">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i>
                                    {{ $comment->created_at->format('d/m/Y H:i:s') }}
                                    <span class="ml-2">
                                        ({{ $comment->created_at->diffForHumans() }})
                                    </span>
                                </small>
                            </div>
                        </div>
                        @if (!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Esta actividad no tiene comentarios aún. ¡Agrega el primero!
            </div>
        @endif

        {{-- Botones de acción --}}
        <div class="mt-4">
            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar Actividad
            </a>
            <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" class="btn btn-secondary">
                <i class="fas fa-plus"></i> Crear Subactividad
            </a>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('toggle-order-btn');
            var list = document.getElementById('comments-list');
            if (!btn || !list) return;

            btn.addEventListener('click', function() {
                var currentOrder = btn.getAttribute('data-order') || 'desc';
                var newOrder = currentOrder === 'desc' ? 'asc' : 'desc';

                var items = Array.from(list.querySelectorAll('.comment-item'));
                if (items.length === 0) return;

                // Ordenar por fecha de creación: usamos el texto del small con formato d/m/Y H:i:s
                items.sort(function(a, b) {
                    var textA = a.querySelector('.comment-meta small').textContent;
                    var textB = b.querySelector('.comment-meta small').textContent;

                    var dateA = extractDate(textA);
                    var dateB = extractDate(textB);

                    if (!dateA || !dateB) return 0;

                    return newOrder === 'desc' ? dateB - dateA : dateA - dateB;
                });

                // Quitar todos los items y volver a agregarlos en el nuevo orden
                items.forEach(function(item) {
                    list.appendChild(item.parentNode && item.nextElementSibling && item
                        .nextElementSibling.tagName === 'HR' ?
                        item.nextElementSibling : document.createTextNode(''));
                    list.appendChild(item);
                });

                btn.setAttribute('data-order', newOrder);
                btn.textContent = 'Ordenar por fecha: ' + (newOrder === 'desc' ? 'Más recientes primero' :
                    'Más antiguos primero');
            });

            function extractDate(text) {
                // Busca algo como 31/01/2026 14:30:00
                var match = text.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2}):(\d{2})/);
                if (!match) return null;
                var d = parseInt(match[1], 10);
                var m = parseInt(match[2], 10) - 1;
                var y = parseInt(match[3], 10);
                var hh = parseInt(match[4], 10);
                var mm = parseInt(match[5], 10);
                var ss = parseInt(match[6], 10);
                return new Date(y, m, d, hh, mm, ss);
            }
        });
    </script>

@endsection

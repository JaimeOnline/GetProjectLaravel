@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container" id="edit-activity-page">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Actividades</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ver: {{ $activity->caso }} - {{ $activity->name }}
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Barra de Acciones -->
        <div class="action-bar">
            <div class="action-group">
                <h1 class="text-gradient mb-0">Ver Actividad</h1>
            </div>
            <div class="action-group">
                <div class="quick-nav">
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}"
                        class="btn btn-warning btn-sm">
                        <i class="fas fa-plus"></i> Crear Sub Actividad
                    </a>
                    <a href="{{ route('activities.comments', $activity) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-comments"></i> Comentarios
                    </a>
                    <a href="{{ route('activities.emails', $activity) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-envelope"></i> Correos
                    </a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger fade-in">
                <h6><i class="fas fa-exclamation-triangle"></i> Por favor corrige los siguientes errores:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Pestañas de Navegación -->
        <ul class="nav nav-tabs section-tabs" id="activityTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                    <i class="fas fa-info-circle"></i> Información Básica
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="requirements-tab" data-toggle="tab" href="#requirements" role="tab">
                    <i class="fas fa-list-check"></i> Requerimientos
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="comments-tab" data-toggle="tab" href="#comments" role="tab">
                    <i class="fas fa-comments"></i> Comentarios
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="emails-tab" data-toggle="tab" href="#emails" role="tab">
                    <i class="fas fa-envelope"></i> Correos
                </a>
            </li>
        </ul>

        <div class="tab-content" id="activityTabsContent">
            <!-- Pestaña: Información Básica -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <form action="{{ route('activities.update', $activity) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Básica de la Actividad</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="caso">
                                        <i class="fas fa-hashtag text-primary"></i> Caso <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="caso" name="caso"
                                        value="{{ $activity->caso }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">
                                        <i class="fas fa-tag text-primary"></i> Nombre de la Actividad <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        value="{{ old('name', $activity->name) }}">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label" for="description">
                                    <i class="fas fa-align-left text-primary"></i> Descripción
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="Describe los detalles de la actividad...">{{ old('description', $activity->description) }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-flag text-primary"></i> Estados <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="status-management-container">
                                        <div class="current-statuses" id="currentStatuses">
                                            @if ($activity->statuses && $activity->statuses->count() > 0)
                                                @foreach ($activity->statuses as $status)
                                                    <span class="badge badge-pill mr-1 mb-1"
                                                        style="background-color: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                                        <i class="{{ $status->icon ?? 'fas fa-circle' }}"></i>
                                                        {{ $status->label }}
                                                    </span>
                                                @endforeach
                                            @else
                                                @if ($activity->status)
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-circle"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-exclamation-triangle"></i> Sin estados asignados
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                            id="editStatusesBtn" data-activity-id="{{ $activity->id }}">
                                            <i class="fas fa-edit"></i> Editar Estados
                                        </button>
                                    </div>
                                    <input type="hidden" name="status" value="{{ $activity->status }}"
                                        id="hiddenStatusField">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="proyecto_id">
                                        <i class="fas fa-project-diagram text-primary"></i> Proyecto
                                    </label>
                                    <select class="form-control" id="proyecto_id" name="proyecto_id">
                                        <option value="">-- Sin proyecto --</option>
                                        @foreach ($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}"
                                                {{ old('proyecto_id', $activity->proyecto_id) == $proyecto->id ? 'selected' : '' }}>
                                                {{ $proyecto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="prioridad">
                                        <i class="fas fa-arrow-up text-primary"></i> Prioridad (número) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="prioridad" name="prioridad"
                                        value="{{ old('prioridad', $activity->prioridad) }}" min="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="orden_analista">
                                        <i class="fas fa-sort-numeric-up text-primary"></i> Orden Analista (número) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="orden_analista" name="orden_analista"
                                        value="{{ old('orden_analista', $activity->orden_analista) }}" min="1"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="cliente_id">
                                        <i class="fas fa-user-tie text-primary"></i> Cliente <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="cliente_id" name="cliente_id" required>
                                        <option value="">-- Selecciona un cliente --</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}"
                                                {{ old('cliente_id', $activity->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="tipo_producto_id">
                                        <i class="fas fa-box text-primary"></i> Tipo de Producto
                                    </label>
                                    <select class="form-control" id="tipo_producto_id" name="tipo_producto_id">
                                        <option value="">-- Sin tipo de producto --</option>
                                        @foreach ($tipos_productos as $tipo)
                                            <option value="{{ $tipo->id }}"
                                                {{ old('tipo_producto_id', $activity->tipo_producto_id ?? '') == $tipo->id ? 'selected' : '' }}>
                                                {{ $tipo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="categoria">
                                        <i class="fas fa-layer-group text-primary"></i> Categoría
                                    </label>
                                    @php
                                        $selectedCategorias = old(
                                            'categoria',
                                            \DB::table('activity_categoria')
                                                ->where('activity_id', $activity->id)
                                                ->pluck('categoria')
                                                ->toArray(),
                                        );
                                    @endphp
                                    <select class="form-control" id="categoria" name="categoria[]" multiple>
                                        <option value="proyecto"
                                            {{ in_array('proyecto', $selectedCategorias) ? 'selected' : '' }}>Proyecto
                                        </option>
                                        <option value="incidencia"
                                            {{ in_array('incidencia', $selectedCategorias) ? 'selected' : '' }}>Incidencia
                                        </option>
                                        <option value="mejora_continua"
                                            {{ in_array('mejora_continua', $selectedCategorias) ? 'selected' : '' }}>Mejora
                                            Continua</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="estatus_operacional">
                                        <i class="fas fa-cogs text-primary"></i> Estatus Operacional
                                    </label>
                                    <textarea class="form-control" id="estatus_operacional" name="estatus_operacional" rows="3"
                                        placeholder="Ingrese el estatus operacional de la actividad...">{{ $activity->estatus_operacional }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="porcentaje_avance">
                                        <i class="fas fa-percentage text-primary"></i> Porcentaje de Avance (%)
                                    </label>
                                    <input type="number" class="form-control" id="porcentaje_avance"
                                        name="porcentaje_avance" min="0" max="100"
                                        value="{{ old('porcentaje_avance', $activity->porcentaje_avance ?? 0) }}">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label" for="basic_comment">
                                    <i class="fas fa-comment-dots text-primary"></i> Comentario
                                </label>
                                <textarea class="form-control" id="basic_comment" name="basic_comment" rows="3"
                                    placeholder="Agrega un comentario sobre la actividad..."></textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">
                                    <i class="fas fa-users text-primary"></i> Seleccionar Analistas <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="analysts-selector" id="analysts-selector">
                                    <div class="text-center mb-2">
                                        <i class="fas fa-user-friends fa-2x text-muted"></i>
                                        <p class="mb-1 font-weight-bold">Selecciona los analistas para esta actividad</p>
                                        <p class="text-muted mb-0">Haz clic en las tarjetas para seleccionar/deseleccionar
                                        </p>
                                    </div>
                                    <div class="analysts-grid">
                                        @foreach ($analistas as $analista)
                                            <div class="analyst-card" data-analyst-id="{{ $analista->id }}"
                                                data-analyst-name="{{ $analista->name }}">
                                                <div class="analyst-avatar">
                                                    {{ strtoupper(substr($analista->name, 0, 2)) }}
                                                </div>
                                                <p class="analyst-name">{{ $analista->name }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="selected-analysts-inputs">
                                        @if ($activity->analistas)
                                            @foreach ($activity->analistas as $analista)
                                                <input type="hidden" name="analista_id[]" value="{{ $analista->id }}">
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div id="selected-analysts-summary" class="mt-2" style="display: none;">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                        <span id="selected-count">0</span> analista(s) seleccionado(s):
                                        <span id="selected-names" class="font-weight-bold"></span>
                                    </small>
                                </div>
                                @if ($activity->analistas && $activity->analistas->count() == 0)
                                    <small class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Esta actividad no tiene analistas asignados. Debes seleccionar al menos uno.
                                    </small>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="parent_id">
                                        <i class="fas fa-sitemap text-primary"></i> Actividad Padre
                                    </label>
                                    <input type="text" class="form-control" id="parent_id_search"
                                        placeholder="Buscar actividad padre...">
                                    <input type="hidden" name="parent_id" id="parent_id"
                                        value="{{ $activity->parent_id }}">
                                    <div id="parent_id_results" class="list-group"
                                        style="position: absolute; z-index: 1000; width: 100%; display: none; max-height: 200px; overflow-y: auto;">
                                    </div>
                                    <script>
                                        // ... (el script de búsqueda de actividad padre permanece igual)
                                    </script>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="fecha_recepcion">
                                        <i class="fas fa-calendar text-primary"></i> Fecha de Recepción
                                    </label>
                                    <input type="date" class="form-control" id="fecha_recepcion"
                                        name="fecha_recepcion"
                                        value="{{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('Y-m-d') : '' }}">
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                        <i class="fas fa-save"></i> Actualizar Información Básica
                                    </button>
                                </div>
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Los cambios se guardarán al hacer clic en "Actualizar"
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Tabla de actividad principal y subactividades (igual que en index) -->
                <!-- Tabla de actividad principal y subactividades (igual que en index) -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-sitemap text-primary"></i>
                            Actividad y Subactividades de <strong>{{ $activity->name }}</strong>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <!-- Scroll horizontal superior opcional -->
                        <div id="top-scroll" style="overflow-x: auto; width: 100%;">
                            <div style="width: 1800px; height: 1px;"></div>
                        </div>
                        <!-- Contenedor con scroll horizontal siempre visible -->
                        <div id="subactivitiesTableContainer" style="overflow-x: auto; width: 100%;">
                            <div style="min-width: 1800px;">
                                @include('activities.partials.activity_table', [
                                    'activities' => collect([$activity])->merge($activity->subactivities),
                                    'statusLabels' => $statusLabels,
                                    'statusColors' => $statusColors,
                                    'analistas' => $analistas,
                                    'editMode' => true,
                                ])
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var topScroll = document.getElementById('top-scroll');
                    var tableScroll = document.getElementById('subactivitiesTableContainer');
                    if (topScroll && tableScroll) {
                        // Cuando haces scroll en la barra superior, mueve la tabla
                        topScroll.addEventListener('scroll', function() {
                            tableScroll.scrollLeft = topScroll.scrollLeft;
                        });
                        // Cuando haces scroll en la tabla, mueve la barra superior
                        tableScroll.addEventListener('scroll', function() {
                            topScroll.scrollLeft = tableScroll.scrollLeft;
                        });
                    }
                });
            </script>
            <!-- Pestaña: Requerimientos -->
            <div class="tab-pane fade" id="requirements" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list-check"></i> Gestión de Requerimientos</h5>
                        <div>
                            <a href="{{ route('requirements.create', ['activity_id' => $activity->id]) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Requerimiento
                            </a>
                            <a href="{{ route('requirements.index', ['activity_id' => $activity->id]) }}"
                                class="btn btn-info btn-sm">
                                <i class="fas fa-external-link-alt"></i> Ver Todos
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Estadísticas rápidas --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $activity->requirements->count() }}</h4>
                                        <small>Total Requerimientos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $activity->requirements->where('status', 'pendiente')->count() }}</h4>
                                        <small>Pendientes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $activity->requirements->where('status', 'recibido')->count() }}</h4>
                                        <small>Recibidos</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Mostrar requerimientos existentes --}}
                        @if ($activity->requirements->count() > 0)
                            <div class="form-group">
                                <label class="font-weight-bold">Requerimientos de esta Actividad</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Descripción
                                                    <div class="custom-dropdown ml-2">
                                                        <button class="btn btn-sm btn-outline-secondary filter-toggle"
                                                            type="button" data-filter="status"
                                                            style="padding: 2px 6px;">
                                                            <i class="fas fa-filter"></i>
                                                        </button>
                                                        <div class="custom-dropdown-menu" id="status-filter-menu"
                                                            style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                                                            <h6 class="dropdown-header"
                                                                style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                                                Filtrar por Estado</h6>
                                                            <div class="px-3 py-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input status-filter"
                                                                        type="checkbox" value="" id="status-all"
                                                                        checked>
                                                                    <label class="form-check-label"
                                                                        for="status-all">Todos</label>
                                                                </div>
                                                                @foreach ($statusLabels as $key => $label)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input status-filter"
                                                                            type="checkbox" value="{{ $key }}"
                                                                            id="status-{{ $key }}">
                                                                        <label
                                                                            class="form-check-label d-flex align-items-center"
                                                                            for="status-{{ $key }}">
                                                                            <span class="badge badge-pill mr-2"
                                                                                style="background-color: {{ $statusColors[$key] ?? '#6c757d' }}; color: white; width: 18px; height: 18px; display: inline-block; text-align: center; line-height: 18px; font-size: 0.8em; border-radius: 50%;">
                                                                                &nbsp;
                                                                            </span>
                                                                            {{ $label }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                </th>
                                                <th>Estado</th>
                                                <th>Fecha Creación</th>
                                                <th>Fecha Recepción</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activity->requirements as $requirement)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            {{ Str::limit($requirement->description, 1000) }}
                                                            @if ($requirement->notas)
                                                                <br><small class="text-muted">
                                                                    <i class="fas fa-sticky-note"></i>
                                                                    {{ Str::limit($requirement->notas, 50) }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($requirement->status === 'pendiente')
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock"></i> Pendiente
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check-circle"></i> Recibido
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $requirement->created_at->format('d/m/Y H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($requirement->fecha_recepcion)
                                                            <small
                                                                class="text-success">{{ $requirement->fecha_recepcion->format('d/m/Y H:i') }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('requirements.show', $requirement) }}"
                                                                class="btn btn-info btn-xs" title="Ver detalles">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('requirements.edit', $requirement) }}"
                                                                class="btn btn-warning btn-xs" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            @if ($requirement->status === 'pendiente')
                                                                <form
                                                                    action="{{ route('requirements.mark-received', $requirement) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="from_activity"
                                                                        value="1">
                                                                    <button type="submit" class="btn btn-success btn-xs"
                                                                        title="Marcar como recibido">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <form
                                                                    action="{{ route('requirements.mark-pending', $requirement) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="from_activity"
                                                                        value="1">
                                                                    <button type="submit"
                                                                        class="btn btn-secondary btn-xs"
                                                                        title="Marcar como pendiente">
                                                                        <i class="fas fa-undo"></i>
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <form
                                                                action="{{ route('requirements.destroy', $requirement) }}"
                                                                method="POST" style="display:inline;"
                                                                onsubmit="return confirm('¿Estás seguro de eliminar este requerimiento?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-xs"
                                                                    title="Eliminar">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay requerimientos</h5>
                                <p class="text-muted">Esta actividad aún no tiene requerimientos asociados.</p>
                                <a href="{{ route('requirements.create', ['activity_id' => $activity->id]) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Requerimiento
                                </a>
                            </div>
                        @endif

                        {{-- Formulario rápido para agregar requerimiento --}}
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-3">
                                <i class="fas fa-plus-circle text-success"></i> Agregar Requerimiento Rápido
                            </h6>
                            <form action="{{ route('requirements.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <textarea class="form-control" name="description" rows="2" placeholder="Describe el requerimiento..."
                                                required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control" name="status" required>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="recibido">Recibido</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="notas" rows="1" placeholder="Notas adicionales (opcional)"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Agregar Requerimiento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Comentarios -->
            <div class="tab-pane fade" id="comments" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comments"></i> Gestión de Comentarios</h5>
                    </div>
                    <div class="card-body">
                        {{-- Mostrar comentarios existentes --}}
                        @if ($activity->comments->count() > 0)
                            <div class="form-group">
                                <label>Comentarios Existentes</label>
                                <div class="card">
                                    <div class="card-body">
                                        @foreach ($activity->comments as $comment)
                                            <div
                                                class="border-bottom pb-2 mb-2 d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <p class="mb-1">{{ $comment->comment }}</p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $comment->created_at->format('d/m/Y H:i:s') }}
                                                        <span class="ml-2">
                                                            ({{ $comment->created_at->diffForHumans() }})
                                                        </span>
                                                    </small>
                                                </div>
                                                <div class="ml-2">
                                                    <form action="{{ route('comments.destroy', $comment) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('¿Estás seguro de eliminar este comentario?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            title="Eliminar comentario">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('activities.comments.tab.store', $activity) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="comments">Agregar Nuevos Comentarios</label>
                                <div id="comments-container">
                                    <div class="comment-item mb-2">
                                        <div class="input-group">
                                            <textarea class="form-control" name="comments[]" placeholder="Agrega nuevos comentarios (deja vacío si no hay)"></textarea>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-danger remove-comment"
                                                    title="Eliminar comentario">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary" id="add-comment">
                                    <i class="fas fa-plus"></i> Agregar Comentario
                                </button>

                                <!-- Botón de Actualizar para Comentarios -->
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save"></i> Actualizar Comentarios
                                            </button>
                                            <a href="{{ route('activities.comments', $activity) }}"
                                                class="btn btn-info btn-lg ml-2">
                                                <i class="fas fa-eye"></i> Ver Página de Comentarios
                                            </a>
                                        </div>
                                        <div>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Los cambios se guardarán al hacer clic en "Actualizar"
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Pestaña: Correos -->
            <div class="tab-pane fade" id="emails" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-envelope"></i> Gestión de Correos</h5>
                        <button type="button" class="btn btn-success btn-sm" id="nuevoCorreoBtn">
                            <i class="fas fa-plus"></i> Nuevo Correo
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar correos existentes -->
                        @if ($activity->emails->count() > 0)
                            <div class="mb-4">
                                <h6>Correos Existentes ({{ $activity->emails->count() }} total)</h6>
                                <div class="card">
                                    <div class="card-body">
                                        @foreach ($activity->emails->sortByDesc('created_at')->take(5) as $email)
                                            <div
                                                class="border rounded p-3 mb-3 {{ $email->type == 'sent' ? 'border-primary' : 'border-success' }}">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span
                                                                class="badge badge-{{ $email->type == 'sent' ? 'primary' : 'success' }} mr-2">
                                                                <i
                                                                    class="fas fa-{{ $email->type == 'sent' ? 'paper-plane' : 'inbox' }}"></i>
                                                                {{ $email->type == 'sent' ? 'Enviado' : 'Recibido' }}
                                                            </span>
                                                            <h6 class="mb-0">{{ $email->subject }}</h6>
                                                        </div>

                                                        <div class="mb-2">
                                                            <strong>{{ $email->type == 'sent' ? 'Para:' : 'De:' }}</strong>
                                                            {{ $email->sender_recipient ?: 'No especificado' }}
                                                        </div>

                                                        <div class="mb-2">
                                                            <strong>Contenido:</strong>
                                                            <div class="bg-light p-2 rounded mt-1"
                                                                style="max-height: 350px; overflow-y: auto;">
                                                                {!! $email->content !!}
                                                            </div>
                                                        </div>

                                                        @if ($email->attachments && count($email->attachments) > 0)
                                                            <div class="mb-2">
                                                                <strong>Archivos Adjuntos:</strong>
                                                                <ul class="list-unstyled mb-0 ml-3">
                                                                    @foreach ($email->attachments as $index => $attachment)
                                                                        <li class="mb-1">
                                                                            <i class="fas fa-paperclip text-primary"></i>
                                                                            @if (is_array($attachment))
                                                                                <a href="{{ route('emails.download', [$email, $index]) }}"
                                                                                    class="text-decoration-none"
                                                                                    target="_blank">
                                                                                    {{ $attachment['original_name'] }}
                                                                                </a>
                                                                                <small class="text-muted">
                                                                                    ({{ number_format($attachment['file_size'] / 1024, 1) }}
                                                                                    KB)
                                                                                </small>
                                                                            @else
                                                                                <span
                                                                                    class="text-muted">{{ $attachment }}</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4 text-right">
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock"></i>
                                                                {{ $email->created_at->format('d/m/Y H:i:s') }}
                                                            </small>
                                                            <br>
                                                            <small class="text-muted">
                                                                ({{ $email->created_at->diffForHumans() }})
                                                            </small>
                                                        </div>

                                                        <form action="{{ route('emails.destroy', $email) }}"
                                                            method="POST" style="display: inline;"
                                                            onsubmit="return confirm('¿Estás seguro de eliminar este correo?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                title="Eliminar correo">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if ($activity->emails->count() > 5)
                                            <div class="text-center mt-2">
                                                <a href="{{ route('activities.emails', $activity) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Ver todos los correos
                                                    ({{ $activity->emails->count() }})
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Formulario para agregar nuevo correo -->
                        <form id="emailForm" action="{{ route('activities.emails.store', $activity) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Agregar Nuevo Correo</label>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">
                                                <i class="fas fa-exchange-alt text-primary"></i> Tipo de Correo
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="type" name="type" required>
                                                <option value="">Seleccionar tipo</option>
                                                <option value="received">Correo Recibido</option>
                                                <option value="sent">Correo Enviado</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="email_type">
                                                <i class="fas fa-envelope-open-text text-primary"></i> Tipo de Correo
                                                Especial
                                            </label>
                                            <select class="form-control" id="email_type" name="email_type">
                                                <option value="">-- Ninguno --</option>
                                                <option value="Solicitud de Insumos">Solicitud de Insumos</option>
                                                <option value="Invitación a certificar">Invitación a certificar</option>
                                                <option value="Envío de Pases">Envío de Pases</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                Si seleccionas una opción, se actualizará el estado de la actividad
                                                automáticamente.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sender_recipient">
                                                <i class="fas fa-user text-primary"></i> De/Para
                                            </label>
                                            <input type="email" class="form-control" id="sender_recipient"
                                                name="sender_recipient" placeholder="correo@ejemplo.com">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="subject">
                                        <i class="fas fa-tag text-primary"></i> Asunto
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="subject" name="subject"
                                        placeholder="Asunto del correo" required>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Obtener el asunto del correo más reciente (si existe)
                                        @if ($activity->emails->count() > 0)
                                            var lastSubject = @json($activity->emails->sortByDesc('created_at')->first()->subject);
                                            document.getElementById('subject').value = lastSubject;
                                        @endif
                                        // Seleccionar "Correo Enviado" por defecto
                                        var typeSelect = document.getElementById('type');
                                        if (typeSelect) {
                                            typeSelect.value = "sent";
                                        }
                                    });
                                </script>

                                <div class="form-group" id="nuevo-correo-formulario">
                                    <label for="content">
                                        <i class="fas fa-align-left text-primary"></i> Contenido
                                        <span class="text-danger">*</span>
                                    </label>
                                    <!-- Quill editor container -->
                                    <div id="quill-editor" style="height: 250px;"></div>
                                    <!-- Hidden textarea to submit HTML content -->
                                    <textarea id="content" name="content" style="display:none;"></textarea>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var btn = document.getElementById('nuevoCorreoBtn');
                                        if (btn) {
                                            btn.addEventListener('click', function() {
                                                // Cambia a la pestaña de correos si no está activa
                                                var emailsTab = document.querySelector('#activityTabs a[href="#emails"]');
                                                if (emailsTab && !emailsTab.classList.contains('active')) {
                                                    emailsTab.click();
                                                }
                                                // Espera un poco para que la pestaña se muestre antes de hacer scroll
                                                setTimeout(function() {
                                                    var target = document.getElementById('nuevo-correo-formulario');
                                                    if (target) {
                                                        target.scrollIntoView({
                                                            behavior: 'smooth',
                                                            block: 'center'
                                                        });
                                                    }
                                                }, 200);
                                            });
                                        }
                                    });
                                </script>

                                <!-- Quill Editor (sin registro, sin advertencias) -->
                                <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
                                <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var quill = new Quill('#quill-editor', {
                                            theme: 'snow',
                                            placeholder: 'Contenido del correo...',
                                            modules: {
                                                toolbar: [
                                                    [{
                                                        'header': [1, 2, false]
                                                    }],
                                                    ['bold', 'italic', 'underline', 'strike'],
                                                    [{
                                                        'color': []
                                                    }, {
                                                        'background': []
                                                    }],
                                                    [{
                                                        'list': 'ordered'
                                                    }, {
                                                        'list': 'bullet'
                                                    }],
                                                    [{
                                                        'align': []
                                                    }],
                                                    ['blockquote', 'code-block'],
                                                    ['link', 'image'],
                                                    ['clean']
                                                ]
                                            }
                                        });

                                        var form = document.getElementById('emailForm');
                                        if (form) {
                                            form.addEventListener('submit', function(e) {
                                                var html = quill.root.innerHTML.trim();
                                                // Quitar el <p><br></p> vacío que pone Quill por defecto
                                                if (html === '<p><br></p>' || html === '') {
                                                    alert('El contenido del correo no puede estar vacío.');
                                                    e.preventDefault();
                                                    return false;
                                                }
                                                document.getElementById('content').value = html;
                                            });
                                        }
                                    });
                                </script>


                                <div class="form-group">
                                    <label for="attachments">
                                        <i class="fas fa-paperclip text-primary"></i> Archivos Adjuntos
                                    </label>
                                    <div id="drop-area"
                                        style="border: 2px dashed #aaa; border-radius: 8px; padding: 16px; text-align: center; background: #f8f9fa; cursor: pointer;">
                                        <span id="drop-area-text">Arrastra y suelta archivos aquí o haz clic en "Agregar
                                            archivo(s)"</span>
                                        <input type="file" class="form-control-file" id="attachments"
                                            name="attachments[]" multiple
                                            accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar,.csv,.xml"
                                            style="display:none;">
                                        <button type="button" class="btn btn-secondary btn-sm mt-2" id="addFileBtn">
                                            <i class="fas fa-plus"></i> Agregar archivo(s)
                                        </button>
                                        <div id="file-list" class="mt-2"></div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Máximo 10MB por archivo. Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, TXT,
                                        JPG, PNG, GIF, ZIP, RAR, CSV, XML
                                    </small>
                                    <script>
                                        let selectedFiles = [];

                                        document.getElementById('addFileBtn').addEventListener('click', function() {
                                            document.getElementById('attachments').click();
                                        });

                                        document.getElementById('attachments').addEventListener('change', function(e) {
                                            handleFiles(e.target.files);
                                            e.target.value = '';
                                        });

                                        // Drag & Drop
                                        const dropArea = document.getElementById('drop-area');
                                        dropArea.addEventListener('dragover', function(e) {
                                            e.preventDefault();
                                            dropArea.style.background = '#e2e6ea';
                                        });
                                        dropArea.addEventListener('dragleave', function(e) {
                                            e.preventDefault();
                                            dropArea.style.background = '#f8f9fa';
                                        });
                                        dropArea.addEventListener('drop', function(e) {
                                            e.preventDefault();
                                            dropArea.style.background = '#f8f9fa';
                                            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                                                handleFiles(e.dataTransfer.files);
                                            }
                                        });
                                        dropArea.addEventListener('click', function(e) {
                                            // Solo abrir el input si no se hizo click en el botón eliminar
                                            if (!e.target.closest('.btn-danger')) {
                                                document.getElementById('attachments').click();
                                            }
                                        });

                                        function handleFiles(fileList) {
                                            for (let i = 0; i < fileList.length; i++) {
                                                let file = fileList[i];
                                                if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                                                    selectedFiles.push(file);
                                                }
                                            }
                                            renderFileList();
                                        }

                                        function renderFileList() {
                                            const fileListDiv = document.getElementById('file-list');
                                            fileListDiv.innerHTML = '';
                                            selectedFiles.forEach((file, idx) => {
                                                const fileRow = document.createElement('div');
                                                fileRow.className = 'd-flex align-items-center mb-1';
                                                fileRow.innerHTML = `
<span class="mr-2">${file.name}</span>
<button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${idx}); event.stopPropagation();">
    <i class="fas fa-trash"></i> Eliminar
</button>
`;
                                                fileListDiv.appendChild(fileRow);
                                            });
                                            // Actualizar el input file con todos los archivos seleccionados
                                            const dataTransfer = new DataTransfer();
                                            selectedFiles.forEach(file => dataTransfer.items.add(file));
                                            document.getElementById('attachments').files = dataTransfer.files;
                                        }

                                        window.removeFile = function(idx) {
                                            selectedFiles.splice(idx, 1);
                                            renderFileList();
                                        };

                                        // Al enviar el formulario, asegúrate de que el input tenga los archivos correctos
                                        document.getElementById('emailForm').addEventListener('submit', function(e) {
                                            const dataTransfer = new DataTransfer();
                                            selectedFiles.forEach(file => dataTransfer.items.add(file));
                                            document.getElementById('attachments').files = dataTransfer.files;
                                        });
                                    </script>

                                    <!-- Botón de Agregar Correo -->
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="fas fa-plus"></i> Agregar Correo
                                                </button>
                                                <a href="{{ route('activities.emails', $activity) }}"
                                                    class="btn btn-info btn-lg ml-2">
                                                    <i class="fas fa-eye"></i> Ver Todos los Correos
                                                </a>
                                            </div>
                                            <div>
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i>
                                                    El correo se agregará al hacer clic en "Agregar Correo"
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Edit form JavaScript loaded');

                // Activar pestaña específica si viene desde una redirección
                @if (session('active_tab'))
                    const targetTab = '{{ session('active_tab') }}';
                    const tabLink = document.querySelector(`#activityTabs a[href="#${targetTab}"]`);
                    if (tabLink) {
                        // Remover clases activas de todas las pestañas
                        document.querySelectorAll('#activityTabs .nav-link').forEach(link => link.classList.remove(
                            'active'));
                        document.querySelectorAll('.tab-pane').forEach(pane => {
                            pane.classList.remove('show', 'active');
                        });

                        // Activar la pestaña objetivo
                        tabLink.classList.add('active');
                        const targetPane = document.querySelector(`#${targetTab}`);
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                        }
                    }
                @endif

                // ===== FUNCIONALIDAD DE PESTAÑAS =====

                // Inicializar pestañas de Bootstrap
                const tabLinks = document.querySelectorAll('#activityTabs .nav-link');
                const tabPanes = document.querySelectorAll('.tab-pane');

                // Manejar clicks en las pestañas
                tabLinks.forEach(function(tabLink) {
                    tabLink.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Remover clases activas de todas las pestañas
                        tabLinks.forEach(link => link.classList.remove('active'));
                        tabPanes.forEach(pane => {
                            pane.classList.remove('show', 'active');
                        });

                        // Activar la pestaña clickeada
                        this.classList.add('active');

                        // Mostrar el contenido correspondiente
                        const targetId = this.getAttribute('href').substring(1);
                        const targetPane = document.getElementById(targetId);
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                        }
                    });
                });

                // Función para activar una pestaña específica
                function activateTab(tabId) {
                    // Remover clases activas
                    tabLinks.forEach(link => link.classList.remove('active'));
                    tabPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Activar la pestaña específica
                    const tabLink = document.querySelector(`#activityTabs .nav-link[href="#${tabId}"]`);
                    const tabPane = document.getElementById(tabId);

                    if (tabLink && tabPane) {
                        tabLink.classList.add('active');
                        tabPane.classList.add('show', 'active');
                    }
                }

                // Verificar si hay una pestaña activa desde el servidor
                @if (session('active_tab'))
                    activateTab('{{ session('active_tab') }}');
                @endif

                // Verificar si hay un hash en la URL para activar una pestaña específica
                if (window.location.hash) {
                    const hashTab = window.location.hash.substring(1);
                    if (['basic', 'requirements', 'comments', 'emails'].includes(hashTab)) {
                        activateTab(hashTab);
                    }
                }

                // ===== FUNCIONALIDAD DE REQUERIMIENTOS =====

                // Agregar requerimiento
                const addRequirementBtn = document.getElementById('add-requirement');
                if (addRequirementBtn) {
                    addRequirementBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const container = document.getElementById('requirements-container');
                        const newRequirement = document.createElement('div');
                        newRequirement.classList.add('requirement-item', 'mb-2');
                        newRequirement.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="requirements[]" placeholder="Descripción del requerimiento">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-requirement" title="Eliminar requerimiento">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
                        container.appendChild(newRequirement);
                        attachRemoveHandlers();
                    });
                }

                // Agregar comentario
                const addCommentBtn = document.getElementById('add-comment');
                if (addCommentBtn) {
                    addCommentBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const container = document.getElementById('comments-container');
                        const newComment = document.createElement('div');
                        newComment.classList.add('comment-item', 'mb-2');
                        newComment.innerHTML = `
                <div class="input-group">
                    <textarea class="form-control" name="comments[]" placeholder="Descripción del comentario"></textarea>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-comment" title="Eliminar comentario">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
                        container.appendChild(newComment);
                        attachRemoveHandlers();
                    });
                }

                // Función para adjuntar manejadores de eliminación
                function attachRemoveHandlers() {
                    // Eliminar requerimientos - SOLO botones dentro de requirements-container
                    const requirementsContainer = document.getElementById('requirements-container');
                    if (requirementsContainer) {
                        requirementsContainer.querySelectorAll('.remove-requirement').forEach(function(button) {
                            // Remover listeners existentes para evitar duplicados
                            button.removeEventListener('click', removeRequirement);
                            button.addEventListener('click', removeRequirement);
                        });
                    }

                    // Eliminar comentarios - SOLO botones dentro de comments-container
                    const commentsContainer = document.getElementById('comments-container');
                    if (commentsContainer) {
                        commentsContainer.querySelectorAll('.remove-comment').forEach(function(button) {
                            // Remover listeners existentes para evitar duplicados
                            button.removeEventListener('click', removeComment);
                            button.addEventListener('click', removeComment);
                        });
                    }
                }

                function removeRequirement(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const container = document.getElementById('requirements-container');
                    if (container.children.length > 1) {
                        const item = e.target.closest('.requirement-item');
                        if (item) {
                            item.remove();
                        }
                    } else {
                        alert('Debe mantener al menos un campo de requerimiento.');
                    }
                }

                function removeComment(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const container = document.getElementById('comments-container');
                    if (container.children.length > 1) {
                        const item = e.target.closest('.comment-item');
                        if (item) {
                            item.remove();
                        }
                    } else {
                        alert('Debe mantener al menos un campo de comentario.');
                    }
                }

                // ===== FUNCIONALIDAD DE SELECCIÓN DE ANALISTAS =====
                let selectedAnalysts = [];

                // Inicializar analistas seleccionados desde el servidor
                function initializeSelectedAnalysts() {
                    const existingInputs = document.querySelectorAll(
                        '#selected-analysts-inputs input[name="analista_id[]"]');
                    existingInputs.forEach(input => {
                        const analystId = input.value;
                        const analystCard = document.querySelector(`[data-analyst-id="${analystId}"]`);
                        if (analystCard) {
                            const analystName = analystCard.dataset.analystName;
                            selectedAnalysts.push({
                                id: analystId,
                                name: analystName
                            });
                            analystCard.classList.add('selected');
                        }
                    });
                    updateAnalystsDisplay();
                }

                // Manejar clicks en las tarjetas de analistas
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.analyst-card')) {
                        const card = e.target.closest('.analyst-card');
                        const analystId = card.dataset.analystId;
                        const analystName = card.dataset.analystName;

                        if (card.classList.contains('selected')) {
                            // Deseleccionar
                            card.classList.remove('selected');
                            selectedAnalysts = selectedAnalysts.filter(a => a.id !== analystId);
                        } else {
                            // Seleccionar
                            card.classList.add('selected');
                            selectedAnalysts.push({
                                id: analystId,
                                name: analystName
                            });
                        }

                        updateAnalystsDisplay();
                    }
                });

                // Actualizar la visualización y los inputs ocultos
                function updateAnalystsDisplay() {
                    const container = document.getElementById('selected-analysts-inputs');
                    const summary = document.getElementById('selected-analysts-summary');
                    const countSpan = document.getElementById('selected-count');
                    const namesSpan = document.getElementById('selected-names');

                    // Limpiar inputs existentes
                    container.innerHTML = '';

                    // Crear nuevos inputs
                    selectedAnalysts.forEach(analyst => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'analista_id[]';
                        input.value = analyst.id;
                        container.appendChild(input);
                    });

                    // Actualizar resumen
                    if (selectedAnalysts.length > 0) {
                        countSpan.textContent = selectedAnalysts.length;
                        namesSpan.textContent = selectedAnalysts.map(a => a.name).join(', ');
                        summary.style.display = 'block';
                    } else {
                        summary.style.display = 'none';
                    }
                }

                // Inicializar manejadores para elementos existentes
                attachRemoveHandlers();

                // Inicializar analistas seleccionados
                initializeSelectedAnalysts();

                // ===== SISTEMA DE ESTADOS MÚLTIPLES =====

                // Event listener para el botón de editar estados
                document.getElementById('editStatusesBtn').addEventListener('click', function() {
                    const activityId = this.getAttribute('data-activity-id');
                    openStatusEditModal(activityId);
                });

                // Función para actualizar la visualización de estados en la vista edit
                function updateEditStatusDisplay(activityId, statuses) {
                    const statusContainer = document.getElementById('currentStatuses');
                    if (!statusContainer) {
                        console.log('No se encontró el contenedor de estados');
                        return;
                    }

                    let html = '';
                    if (statuses && statuses.length > 0) {
                        statuses.forEach(status => {
                            const contrastColor = getContrastColor(status.color);
                            html += `
                    <span class="badge badge-pill mr-1 mb-1" 
                          style="background-color: ${status.color}; color: ${contrastColor};">
                        <i class="${status.icon || 'fas fa-circle'}"></i> ${status.label}
                    </span>
                `;
                        });
                    } else {
                        html = `
                <span class="text-muted">
                    <i class="fas fa-exclamation-triangle"></i> Sin estados asignados
                </span>
            `;
                    }

                    statusContainer.innerHTML = html;
                    console.log('Estados actualizados en la vista edit para actividad:', activityId);
                }

                // Sobrescribir la función updateStatusDisplay para que funcione en la vista edit
                if (typeof updateStatusDisplay === 'function') {
                    const originalUpdateStatusDisplay = updateStatusDisplay;
                    updateStatusDisplay = function(activityId, statuses) {
                        // Llamar a la función original (para la vista index)
                        originalUpdateStatusDisplay(activityId, statuses);
                        // Llamar a la función específica para la vista edit
                        updateEditStatusDisplay(activityId, statuses);
                    };
                } else {
                    // Si no existe la función original, crear una nueva
                    window.updateStatusDisplay = updateEditStatusDisplay;
                }
            });
        </script>

        {{-- Script para editar Prioridad y orden en tabla --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Al hacer click en el valor, mostrar input
                document.querySelectorAll('.editable-cell .editable-value').forEach(function(span) {
                    span.addEventListener('click', function() {
                        const cell = span.closest('.editable-cell');
                        const input = cell.querySelector('.editable-input');
                        span.style.display = 'none';
                        input.style.display = 'inline-block';
                        input.focus();
                        input.select();
                    });
                });

                // Al perder foco o presionar Enter, enviar AJAX
                document.querySelectorAll('.editable-cell .editable-input').forEach(function(input) {
                    input.addEventListener('blur', saveInlineEdit);
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            saveInlineEdit.call(input, e);
                        }
                    });
                });

                function saveInlineEdit(e) {
                    const input = this;
                    const cell = input.closest('.editable-cell');
                    const span = cell.querySelector('.editable-value');
                    const activityId = cell.getAttribute('data-activity-id');
                    const field = cell.getAttribute('data-field');
                    const value = input.value;

                    fetch(`/activities/${activityId}/inline-update`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                field,
                                value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                span.textContent = value;
                            } else {
                                alert('Error al actualizar');
                            }
                            input.style.display = 'none';
                            span.style.display = 'inline-block';
                        })
                        .catch(() => {
                            alert('Error al actualizar');
                            input.style.display = 'none';
                            span.style.display = 'inline-block';
                        });
                }
            });
        </script>

        <!-- Scripts adicionales -->
        <script src="{{ asset('js/multiple-statuses.js') }}"></script>

        <!-- Modal para editar estados -->
        <div class="modal fade" id="statusEditModal" tabindex="-1" role="dialog"
            aria-labelledby="statusEditModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="statusEditModalLabel">
                            <i class="fas fa-flag"></i> Editar Estados de la Actividad
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="statusModalBody">
                        <!-- El contenido se carga dinámicamente -->
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Cargando estados...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="saveStatusBtn" onclick="saveStatusChanges()">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Analistas -->
    <div class="modal fade" id="analystsEditModal" tabindex="-1" role="dialog"
        aria-labelledby="analystsEditModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="analystsEditForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="activity_id" id="modalAnalystsActivityId">
                <input type="hidden" name="redirect_to_edit" value="1">
                <input type="hidden" name="parent_activity_id" id="parentActivityId" value="{{ $activity->id }}">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="analystsEditModalLabel">
                            <i class="fas fa-users"></i> Editar Analistas de Actividad
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modalAnalystsSelect">Selecciona analistas:</label>
                            <select name="analista_id[]" id="modalAnalystsSelect" class="form-control" multiple>
                                @foreach ($analistas as $analista)
                                    <option value="{{ $analista->id }}">{{ $analista->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

<script src="{{ asset('js/activities-filters-sort.js') }}?v={{ time() }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botón para abrir el modal de analistas (funciona para actividades y subactividades)
        document.querySelectorAll('.edit-analysts-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var activityId = btn.getAttribute('data-activity-id');
                var analysts = [];
                // Busca los badges de analistas en la misma celda
                btn.closest('td').querySelectorAll('.badge').forEach(function(badge) {
                    analysts.push(badge.textContent.trim());
                });

                // Selecciona la opción correspondiente en el select del modal
                var select = document.getElementById('modalAnalystsSelect');
                for (var i = 0; i < select.options.length; i++) {
                    select.options[i].selected = false;
                    if (analysts.includes(select.options[i].text.trim())) {
                        select.options[i].selected = true;
                    }
                }

                // Cambia la acción del formulario
                var form = document.getElementById('analystsEditForm');
                form.action = '/activities/' + activityId + '/analysts';

                // Muestra el modal
                $('#analystsEditModal').modal('show');
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var analystsForm = document.getElementById('analystsEditForm');
        if (analystsForm) {
            analystsForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var form = this;
                var url = form.action;
                var formData = new FormData(form);

                // Deshabilita el botón para evitar doble submit
                var submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cierra el modal
                            $('#analystsEditModal').modal('hide');
                            // Actualiza la tabla de analistas en la fila correspondiente
                            var activityId = form.action.match(/activities\/(\d+)/)[1];
                            var row = document.querySelector('tr[data-activity-id="' + activityId +
                                '"]');
                            if (row) {
                                var analystsCell = row.querySelector(
                                    'td .analysts-list, td .analysts-cell, td .analysts');
                                if (!analystsCell) {
                                    // Busca la celda de analistas (ajusta el selector si tu partial cambia)
                                    analystsCell = row.querySelectorAll('td')[6];
                                }
                                if (analystsCell && data.analistas) {
                                    var html = '';
                                    data.analistas.forEach(function(a) {
                                        html +=
                                            '<span class="badge badge-light mr-1 mb-1"><i class="fas fa-user"></i> ' +
                                            a.name + '</span>';
                                    });
                                    analystsCell.innerHTML = html;
                                }
                            }
                        } else {
                            alert('Error al actualizar analistas');
                        }
                    })
                    .catch(() => {
                        alert('Error al actualizar analistas');
                    })
                    .finally(() => {
                        if (submitBtn) submitBtn.disabled = false;
                    });
            });
        }
    });
</script>

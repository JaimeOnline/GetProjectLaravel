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
                    @if (isset($parentActivity))
                        <li class="breadcrumb-item"><a
                                href="{{ route('activities.edit', $parentActivity) }}">{{ $parentActivity->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear Subactividad</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Crear Nueva Actividad</li>
                    @endif
                </ol>
            </nav>
        </div>

        <!-- Barra de Acciones -->
        <div class="action-bar">
            <div class="action-group">
                <h1 class="text-gradient mb-0">
                    {{ isset($parentActivity) ? 'Crear Subactividad' : 'Crear Nueva Actividad' }}
                </h1>
            </div>
            <div class="action-group">
                <div class="quick-nav">
                    <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    @if (isset($parentActivity))
                        <a href="{{ route('activities.edit', $parentActivity) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver Actividad Padre
                        </a>
                    @endif
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
        <!-- Botón para descargar el archivo modelo Excel -->
        <div class="mb-3 d-flex align-items-center gap-2">
            <a href="{{ route('activities.excelTemplate') }}" class="btn btn-outline-info">
                <i class="fas fa-download"></i> Descargar archivo modelo Excel
            </a>
            <button type="button" class="btn btn-outline-primary ml-2" id="toggle-import-excel">
                <i class="fas fa-file-upload"></i> Cargar actividades desde Excel
            </button>
        </div>
        <!-- Formulario para carga masiva desde Excel (colapsable) -->
        <div id="import-excel-section" style="display: none;">
            <form action="{{ route('activities.importExcel') }}" method="POST" enctype="multipart/form-data"
                class="mb-4">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-upload"></i> Cargar Actividades desde Excel</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="excel_file">Selecciona archivo Excel (.xlsx)</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx"
                                required>
                            <small class="form-text text-muted">
                                Estructura: caso, estados, prioridad, orden_analista, nombre_actividad, descripcion,
                                estatus_operacional, analistas, actividad_padre, fecha_recepcion.<br>
                                Ejemplo: estados="Pendiente,En Ejecución", analistas="Juan, Maria",
                                actividad_padre="Actividad A, si no, déjalo vacío", fecha_recepcion= 2024-06-18, Proyecto
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Importar Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            document.getElementById('toggle-import-excel').addEventListener('click', function() {
                var section = document.getElementById('import-excel-section');
                section.style.display = section.style.display === 'none' ? 'block' : 'none';
            });
        </script>

        <form action="{{ route('activities.store') }}" method="POST">
            @csrf

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Información de la Nueva Actividad</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="caso">
                                <i class="fas fa-hashtag text-primary"></i> Caso <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="caso" name="caso" required
                                value="{{ old('caso', $defaultCaso ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">
                                <i class="fas fa-tag text-primary"></i> Nombre de la Actividad <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="description">
                            <i class="fas fa-align-left text-primary"></i> Descripción
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="Describe los detalles de la actividad..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="status_ids">
                                <i class="fas fa-flag text-primary"></i> Estados <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="status_ids" name="status_ids[]" multiple required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $status->id == 7 ? 'selected' : '' }}>
                                        {{ $status->label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Selecciona al menos un estado para la actividad.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="proyecto_id">
                                <i class="fas fa-project-diagram text-primary"></i> Proyecto
                            </label>
                            <select class="form-control" id="proyecto_id" name="proyecto_id">
                                <option value="">-- Sin proyecto --</option>
                                @foreach ($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id }}"
                                        {{ (old('proyecto_id') !== null ? old('proyecto_id') : $proyectoId ?? '') == $proyecto->id ? 'selected' : '' }}>
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
                            <input type="number" class="form-control" id="prioridad" name="prioridad" value="1"
                                min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="orden_analista">
                                <i class="fas fa-sort-numeric-up text-primary"></i> Orden Analista (número) <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="orden_analista" name="orden_analista"
                                value="1" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="cliente_id">
                                <i class="fas fa-user-tie text-primary"></i> Cliente <span class="text-danger">*</span>
                            </label>
                            @php
                                $btClienteId = $clientes->firstWhere('nombre', 'BT Banco del Tesoro')->id ?? null;
                                $selectedCliente = old('cliente_id');
                                if ($selectedCliente === null || $selectedCliente === '') {
                                    $selectedCliente = $btClienteId;
                                }
                            @endphp
                            <select class="form-control" id="cliente_id" name="cliente_id" required>
                                <option value="" {{ $selectedCliente ? '' : 'selected' }}>-- Selecciona un cliente
                                    --</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                        {{ (string) $selectedCliente === (string) $cliente->id ? 'selected' : '' }}>
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
                                <option value="">-- Selecciona un tipo de producto --</option>
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
                            <select class="form-control" id="categoria" name="categoria[]" multiple required>
                                <option value="proyecto"
                                    {{ request()->get('proyecto_id') && !old('categoria') ? 'selected' : (collect(old('categoria', $defaultCategoria ?? ['incidencia']))->contains('proyecto') ? 'selected' : '') }}>
                                    Proyecto</option>
                                <option value="incidencia"
                                    {{ !request()->get('proyecto_id') && !old('categoria') ? 'selected' : (collect(old('categoria', $defaultCategoria ?? ['incidencia']))->contains('incidencia') ? 'selected' : '') }}>
                                    Incidencia</option>
                                <option value="mejora_continua"
                                    {{ collect(old('categoria', $defaultCategoria ?? ['incidencia']))->contains('mejora_continua') ? 'selected' : '' }}>
                                    Mejora Continua</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="estatus_operacional">
                                <i class="fas fa-cogs text-primary"></i> Estatus Operacional
                            </label>
                            <textarea class="form-control" id="estatus_operacional" name="estatus_operacional" rows="3"
                                placeholder="Ingrese el estatus operacional de la actividad..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="porcentaje_avance">
                                <i class="fas fa-percentage text-primary"></i> Porcentaje de Avance (%)
                            </label>
                            <input type="number" class="form-control" id="porcentaje_avance" name="porcentaje_avance"
                                min="0" max="100" value="{{ old('porcentaje_avance', 0) }}">
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
                                <p class="text-muted mb-0">Haz clic en las tarjetas para seleccionar/deseleccionar</p>
                            </div>
                            <div class="analysts-grid">
                                @foreach ($analistas as $analista)
                                    <div class="analyst-card
                                @if (isset($defaultAnalistas) && in_array($analista->id, $defaultAnalistas)) selected
                                @elseif (!isset($defaultAnalistas) && $analista->id == 7)
                                    selected @endif
                            "
                                        data-analyst-id="{{ $analista->id }}" data-analyst-name="{{ $analista->name }}">
                                        <div class="analyst-avatar">
                                            {{ strtoupper(substr($analista->name, 0, 2)) }}
                                        </div>
                                        <p class="analyst-name">{{ $analista->name }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div id="selected-analysts-inputs"></div>
                        </div>
                        <div id="selected-analysts-summary" class="mt-2" style="display: none;">
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i>
                                <span id="selected-count">0</span> analista(s) seleccionado(s):
                                <span id="selected-names" class="font-weight-bold"></span>
                            </small>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            Debes seleccionar al menos un analista para la actividad.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            @if (!isset($parentActivity))
                                <label class="form-label" for="parent_id">
                                    <i class="fas fa-sitemap text-primary"></i> Actividad Padre
                                </label>
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="">-- Seleccionar Actividad Padre (Opcional) --</option>
                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fecha_recepcion">
                                <i class="fas fa-calendar text-primary"></i> Fecha de Recepción
                            </label>
                            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion"
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-center align-items-center">
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                                <i class="fas fa-save"></i> Crear Actividad
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-3" onclick="window.history.back()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Todos los campos marcados con * son obligatorios
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            @php
                $defaultAnalyst = $analistas->firstWhere('id', 7);
            @endphp
            // ===== FUNCIONALIDAD DE SELECCIÓN DE ANALISTAS =====
            let selectedAnalysts = [];
            @if (isset($defaultAnalistas) && count($defaultAnalistas) > 0)
                @foreach ($analistas as $analista)
                    @if (in_array($analista->id, $defaultAnalistas))
                        selectedAnalysts.push({
                            id: "{{ $analista->id }}",
                            name: "{{ $analista->name }}"
                        });
                    @endif
                @endforeach
            @elseif ($defaultAnalyst)
                selectedAnalysts.push({
                    id: "{{ $defaultAnalyst->id }}",
                    name: "{{ $defaultAnalyst->name }}"
                });
            @endif

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
                const selector = document.getElementById('analysts-selector');
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

                // Actualizar visualización
                if (selectedAnalysts.length > 0) {
                    selector.classList.add('has-selection');
                    summary.style.display = 'block';
                    countSpan.textContent = selectedAnalysts.length;
                    namesSpan.textContent = selectedAnalysts.map(a => a.name).join(', ');
                } else {
                    selector.classList.remove('has-selection');
                    summary.style.display = 'none';
                }
            }
            updateAnalystsDisplay();
        </script>
    @endsection

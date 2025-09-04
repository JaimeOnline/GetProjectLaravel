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
        <form action="{{ route('activities.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Información de la Nueva Actividad</h5>
                </div>
                <div class="card-body">
                    @if (isset($parentActivity))
                        <input type="hidden" name="parent_id" value="{{ $parentActivity->id }}">
                        <div class="alert alert-info fade-in">
                            <i class="fas fa-info-circle"></i>
                            <strong>Actividad Padre:</strong> {{ $parentActivity->name }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="caso">
                                    <i class="fas fa-hashtag text-primary"></i> Caso
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="caso" name="caso" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status_ids">
                                    <i class="fas fa-flag text-primary"></i> Estados
                                    <span class="text-danger">*</span>
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
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">
                            <i class="fas fa-tag text-primary"></i> Nombre de la Actividad
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">
                            <i class="fas fa-align-left text-primary"></i> Descripción
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="Describe los detalles de la actividad..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="estatus_operacional">
                            <i class="fas fa-cogs text-primary"></i> Estatus Operacional
                        </label>
                        <textarea class="form-control" id="estatus_operacional" name="estatus_operacional" rows="3"
                            placeholder="Ingrese el estatus operacional de la actividad..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-users text-primary"></i> Seleccionar Analistas
                            <span class="text-danger">*</span>
                        </label>

                        <div class="analysts-selector" id="analysts-selector">
                            <div class="text-center mb-2">
                                <i class="fas fa-user-friends fa-2x text-muted"></i>
                                <p class="mb-1 font-weight-bold">Selecciona los analistas para esta actividad</p>
                                <p class="text-muted mb-0">Haz clic en las tarjetas para seleccionar/deseleccionar</p>
                            </div>

                            <div class="analysts-grid">
                                @foreach ($analistas as $analista)
                                    <div class="analyst-card{{ $analista->id == 7 ? ' selected' : '' }}"
                                        data-analyst-id="{{ $analista->id }}" data-analyst-name="{{ $analista->name }}">
                                        <div class="analyst-avatar">
                                            {{ strtoupper(substr($analista->name, 0, 2)) }}
                                        </div>
                                        <p class="analyst-name">{{ $analista->name }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Inputs ocultos para enviar los datos -->
                            <div id="selected-analysts-inputs">
                            </div>
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

                    @if (!isset($parentActivity))
                        <div class="form-group">
                            <label class="form-label" for="parent_id">
                                <i class="fas fa-sitemap text-primary"></i> Actividad Padre
                            </label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">-- Seleccionar Actividad Padre (Opcional) --</option>
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label" for="fecha_recepcion">
                            <i class="fas fa-calendar text-primary"></i> Fecha de Recepción
                        </label>
                        <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion"
                            value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="action-group">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Crear Actividad
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="window.history.back()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <div class="action-group">
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
            @if ($defaultAnalyst)
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

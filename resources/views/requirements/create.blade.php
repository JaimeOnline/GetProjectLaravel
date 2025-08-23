@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-gradient mb-2">
                    <i class="fas fa-plus-circle text-success"></i> Nuevo Requerimiento
                </h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Crea un nuevo requerimiento asociado a una actividad
                </p>
            </div>
            <div class="action-buttons">
                <a href="{{ route('requirements.index') }}" class="btn btn-secondary btn-lg shadow-sm">
                    <i class="fas fa-arrow-left"></i> Volver a Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit text-primary"></i> Información del Requerimiento
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('requirements.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="activity_id" class="font-weight-bold required">
                                <i class="fas fa-tasks text-primary"></i> Actividad Asociada
                            </label>
                            <select class="form-control @error('activity_id') is-invalid @enderror" 
                                    id="activity_id" 
                                    name="activity_id" 
                                    required>
                                <option value="">Selecciona una actividad...</option>
                                @foreach($activities as $activity)
                                    <option value="{{ $activity->id }}" 
                                            {{ old('activity_id', $selectedActivityId) == $activity->id ? 'selected' : '' }}>
                                        {{ $activity->caso ? '[' . $activity->caso . '] ' : '' }}{{ $activity->name }}
                                        @if($activity->parent)
                                            (Sub-actividad de: {{ $activity->parent->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('activity_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Selecciona la actividad o subactividad a la que pertenece este requerimiento
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="font-weight-bold required">
                                <i class="fas fa-flag text-primary"></i> Estado Inicial
                            </label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="pendiente" {{ old('status', 'pendiente') === 'pendiente' ? 'selected' : '' }}>
                                    <i class="fas fa-clock"></i> Pendiente
                                </option>
                                <option value="recibido" {{ old('status') === 'recibido' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle"></i> Recibido
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Si ya recibiste el requerimiento, selecciona "Recibido"
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="font-weight-bold required">
                        <i class="fas fa-align-left text-primary"></i> Descripción del Requerimiento
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4" 
                              required
                              placeholder="Describe detalladamente qué necesitas recibir...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Máximo 1000 caracteres. Sé específico sobre qué documentos, información o recursos necesitas.
                    </small>
                </div>

                <div class="row" id="fecha-recepcion-row" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_recepcion" class="font-weight-bold">
                                <i class="fas fa-calendar-check text-primary"></i> Fecha de Recepción
                            </label>
                            <input type="datetime-local" 
                                   class="form-control @error('fecha_recepcion') is-invalid @enderror" 
                                   id="fecha_recepcion" 
                                   name="fecha_recepcion"
                                   value="{{ old('fecha_recepcion') }}">
                            @error('fecha_recepcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Si no especificas una fecha, se usará la fecha actual
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notas" class="font-weight-bold">
                        <i class="fas fa-sticky-note text-primary"></i> Notas Adicionales
                    </label>
                    <textarea class="form-control @error('notas') is-invalid @enderror" 
                              id="notas" 
                              name="notas" 
                              rows="3"
                              placeholder="Agrega cualquier información adicional, comentarios o detalles relevantes...">{{ old('notas') }}</textarea>
                    @error('notas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Máximo 2000 caracteres. Campo opcional para información complementaria.
                    </small>
                </div>

                <hr>

                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Crear Requerimiento
                            </button>
                            <button type="reset" class="btn btn-outline-secondary btn-lg ml-2">
                                <i class="fas fa-eraser"></i> Limpiar Formulario
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('requirements.index') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const fechaRecepcionRow = document.getElementById('fecha-recepcion-row');
    
    function toggleFechaRecepcion() {
        if (statusSelect.value === 'recibido') {
            fechaRecepcionRow.style.display = 'block';
        } else {
            fechaRecepcionRow.style.display = 'none';
            document.getElementById('fecha_recepcion').value = '';
        }
    }
    
    // Inicializar estado
    toggleFechaRecepcion();
    
    // Escuchar cambios
    statusSelect.addEventListener('change', toggleFechaRecepcion);
});
</script>

<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.form-actions {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
}

.card-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.card-header h5 {
    margin: 0;
}

.card-header i {
    color: rgba(255, 255, 255, 0.8);
}
</style>
@endsection
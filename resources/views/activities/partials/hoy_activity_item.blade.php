<div class="card mb-3 activity-hoy" data-activity-id="{{ $activity->id }}">
    <div class="card-body">
        <h5 class="card-title">
            {{ $index ?? 1 }}. {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
        </h5>

        {{-- Estatus operacional editable --}}
        <p>
            <strong>Estatus:</strong>
            <span class="estatus-operacional-display" style="cursor:pointer;">
                {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}
            </span>
            <input type="text" class="form-control form-control-sm estatus-operacional-input"
                value="{{ $activity->estatus_operacional }}" style="display:none; max-width: 400px; margin-top: 4px;">
        </p>

        <p>
            <strong>Responsable:</strong>
            <span class="responsable-display" style="cursor:pointer;">
                @if ($activity->analistas->count())
                    {{ $activity->analistas->pluck('name')->implode(', ') }}
                @else
                    No asignado
                @endif
            </span>

            <span class="responsable-editor" style="display:none; margin-top:4px;">
                <select class="form-control form-control-sm responsable-select" multiple
                    style="max-width: 300px; display:inline-block; vertical-align:middle;">
                    @foreach ($analistas as $analista)
                        <option value="{{ $analista->id }}"
                            {{ $activity->analistas->pluck('id')->contains($analista->id) ? 'selected' : '' }}>
                            {{ $analista->name }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-primary ml-1 responsable-save-btn">
                    Guardar
                </button>
                <button type="button" class="btn btn-sm btn-secondary ml-1 responsable-cancel-btn">
                    Cancelar
                </button>
            </span>
        </p>

        <p>
            <strong>Avance:</strong>
            <span class="avance-display" style="cursor:pointer;">
                {{ $activity->porcentaje_avance ?? 0 }}%
            </span>
            <input type="number" class="form-control form-control-sm avance-input"
                value="{{ $activity->porcentaje_avance ?? 0 }}" min="0" max="100"
                style="display:none; max-width: 120px; margin-top: 4px;">
        </p>

        <p>
            <strong>Estimación de entrega:</strong>
            <span class="estimacion-display" style="cursor:pointer;">
                {{ $activity->fecha_estimacion_entrega ? \Carbon\Carbon::parse($activity->fecha_estimacion_entrega)->format('d-m-y') : 'Sin estimación' }}
            </span>
            <input type="date" class="form-control form-control-sm estimacion-input"
                value="{{ $activity->fecha_estimacion_entrega ? \Carbon\Carbon::parse($activity->fecha_estimacion_entrega)->format('Y-m-d') : '' }}"
                style="display:none; max-width: 200px; margin-top: 4px;">
        </p>

        @php
            $hasEnEjecucion = $activity->statuses->contains(function ($s) {
                return $s->name === 'en_ejecucion';
            });
            $hasAtendiendo = $activity->statuses->contains(function ($s) {
                return $s->name === 'atendiendo_hoy';
            });
        @endphp

        <div class="mt-2">
            <small class="text-muted d-block mb-1">Estatus de flujo (solo pantalla y filtros):</small>
            <div class="form-check form-check-inline">
                <input class="form-check-input hoy-status-checkbox" type="checkbox"
                    id="hoy_en_ejecucion_{{ $activity->id }}" data-status-name="en_ejecucion"
                    {{ $hasEnEjecucion ? 'checked' : '' }}>
                <label class="form-check-label" for="hoy_en_ejecucion_{{ $activity->id }}">
                    En ejecución
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input hoy-status-checkbox" type="checkbox"
                    id="hoy_atendiendo_{{ $activity->id }}" data-status-name="atendiendo_hoy"
                    {{ $hasAtendiendo ? 'checked' : '' }}>
                <label class="form-check-label" for="hoy_atendiendo_{{ $activity->id }}">
                    Atendiendo hoy
                </label>
            </div>
        </div>

        <div class="mt-2">
            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary hoy-edit-link"
                target="_blank">
                <i class="fas fa-edit"></i> Ver/Editar
            </a>
        </div>
    </div>
</div>

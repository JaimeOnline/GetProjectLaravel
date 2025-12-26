<div class="mb-3 activity-insumo" data-activity-id="{{ $activity->id }}">
    <strong>{{ $index ?? 1 }}.
        {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}</strong><br>

    Estatus:
    <span class="estatus-operacional-display">
        {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}
    </span>
    <input type="text" class="form-control form-control-sm estatus-operacional-input"
        value="{{ $activity->estatus_operacional }}" style="display:none; max-width: 400px; margin-top: 4px;">

    <div class="mt-1">
        <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary insumo-edit-link"
            target="_blank">
            <i class="fas fa-edit"></i> Ver/Editar
        </a>
    </div>
</div>

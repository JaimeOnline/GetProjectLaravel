<div class="card mb-3 activity-insumo" data-activity-id="{{ $activity->id }}">
    <div class="card-body">
        <h5 class="card-title mb-2">
            {{ $index ?? 1 }}.
            {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
        </h5>

        <p class="mb-2">
            <strong>Estatus:</strong>
            <span class="estatus-operacional-display" style="cursor:pointer;">
                {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}
            </span>
            <input type="text" class="form-control form-control-sm estatus-operacional-input"
                value="{{ $activity->estatus_operacional }}" style="display:none; max-width: 400px; margin-top: 4px;">
        </p>

        <div class="mt-1">
            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-outline-primary insumo-edit-link"
                target="_blank">
                <i class="fas fa-edit"></i> Ver/Editar
            </a>
        </div>
    </div>
</div>

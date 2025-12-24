@if (!isset($statuses))
    @php
        $statuses = \App\Models\Status::orderBy('order')->get();
    @endphp
@endif

@if ($activities->count())
    <table class="table table-sm table-hover bg-white rounded" id="activities-table-{{ $analistaId ?? '' }}">
        <thead class="thead-light">
            <tr>
                <th>Orden</th>
                <th>Caso</th>
                <th>Nombre</th>
                <th>Estados</th>
                <th>Estatus Operacional</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="sortable-tbody-{{ $analistaId ?? '' }}">
            @foreach ($activities->sortBy('orden_analista') as $activity)
                <tr data-activity-id="{{ $activity->id }}">
                    <td class="orden-analista-handle" style="cursor:move;">
                        <span class="badge badge-info">{{ $activity->orden_analista }}</span>
                        <i class="fas fa-arrows-alt"></i>
                    </td>
                    <td>{{ $activity->caso }}</td>
                    <td>{{ $activity->name }}</td>
                    <td>
                        @if ($activity->statuses->count())
                            @foreach ($activity->statuses as $status)
                                <span class="badge"
                                    style="background: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                    {{ $status->label }}
                                </span>
                            @endforeach
                        @else
                            <span
                                class="badge badge-secondary">{{ $activity->status_label ?? $activity->status }}</span>
                        @endif
                    </td>
                    <td style="white-space: pre-line;">{{ $activity->estatus_operacional }}</td>
                    <td>
                        <a href="{{ route('activities.edit', $activity) }}"
                            class="btn btn-sm btn-outline-primary edit-activity-link" target="_blank"
                            data-activity-id="{{ $activity->id }}">
                            <i class="fas fa-edit"></i> Ver/Editar
                        </a>
                        <button type="button" class="btn btn-sm btn-success ml-1 reload-activity-btn"
                            data-activity-id="{{ $activity->id }}" style="display:none;">
                            <i class="fas fa-sync"></i> Actualizar
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted">No tiene actividades asignadas.</p>
@endif

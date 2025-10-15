{{-- Partial para mostrar subactividades de forma recursiva --}}
@php
    $level = $level ?? 0;
@endphp

@foreach ($subactivities as $subactivity)
    <tr class="subactivity-row activity-row level-{{ $level }}" data-parent-id="{{ $parentId }}"
        data-activity-id="{{ $subactivity->id }}"
        @if ($level == 0) style="display: table-row;" @else style="display: none;" @endif>
        <td class="align-middle">
            <span class="badge badge-outline-primary font-weight-bold">
                {{ $subactivity->caso }}
            </span>
        </td>
        <td class="align-middle position-relative" style="position: relative;">
            <div class="d-flex align-items-center">
                @if ($subactivity->subactivities->count() > 0)
                    <span class="toggle-subactivities mr-2" style="cursor: pointer;"
                        data-activity-id="{{ $subactivity->id }}">
                        <i class="fas fa-chevron-right text-primary" id="icon-{{ $subactivity->id }}"></i>
                    </span>
                @endif
                <a href="{{ route('activities.edit', $subactivity) }}" class="font-weight-bold text-dark small"
                    title="Ver/Editar subactividad">
                    {{ Str::limit($subactivity->name, 40) }}
                </a>
                @if (strlen($subactivity->name) > 40)
                    <span class="text-primary" style="cursor: pointer;" title="{{ $subactivity->name }}"
                        data-toggle="tooltip">
                        <i class="fas fa-info-circle"></i>
                    </span>
                @endif
                @if ($subactivity->subactivities->count() > 0)
                    <small class="text-muted ml-2">
                        <i class="fas fa-sitemap"></i>
                        {{ $subactivity->subactivities->count() }} subactividad(es)
                    </small>
                @endif
            </div>
            <div class="action-buttons"
                style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); display: none; z-index: 2;">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('activities.edit', $subactivity) }}" class="btn btn-warning btn-sm action-btn"
                        data-tooltip="Ver/Editar" title="Ver/Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('activities.create', ['parentId' => $subactivity->id]) }}"
                        class="btn btn-secondary btn-sm action-btn" data-tooltip="Crear Subactividad"
                        title="Crear Subactividad">
                        <i class="fas fa-plus"></i>
                    </a>
                    <form action="{{ route('activities.destroy', $subactivity) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm action-btn" data-tooltip="Eliminar"
                            title="Eliminar"
                            onclick="return confirm('¿Estás seguro de eliminar esta actividad y todas sus subactividades?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </td>
        <td class="align-middle editable-cell" data-activity-id="{{ $subactivity->id }}" data-field="prioridad"
            data-sort-value="{{ $subactivity->prioridad ?? 0 }}">
            <span class="badge badge-outline-info editable-value">
                {{ $subactivity->prioridad ?? '-' }}
            </span>
            <input type="number" class="form-control form-control-sm editable-input"
                value="{{ $subactivity->prioridad ?? 1 }}" style="display:none; width: 70px;" min="1">
        </td>
        <td class="align-middle editable-cell" data-activity-id="{{ $subactivity->id }}" data-field="orden_analista"
            data-sort-value="{{ $subactivity->orden_analista ?? 0 }}">
            <span class="badge badge-outline-secondary editable-value">
                {{ $subactivity->orden_analista ?? '-' }}
            </span>
            <input type="number" class="form-control form-control-sm editable-input"
                value="{{ $subactivity->orden_analista ?? 1 }}" style="display:none; width: 70px;" min="1">
        </td>
        <td class="align-middle">
            {{ $subactivity->cliente ? \Illuminate\Support\Str::before($subactivity->cliente->nombre, ' ') : '-' }}
        </td>
        <td class="align-middle">
            {{ Str::limit($subactivity->estatus_operacional, 40) }}
            @if (strlen($subactivity->estatus_operacional) > 40)
                <span class="text-primary" style="cursor: pointer;" title="{{ $subactivity->estatus_operacional }}">
                    <i class="fas fa-info-circle"></i>
                </span>
            @endif
        </td>
        <td class="align-middle editable-cell" data-activity-id="{{ $subactivity->id }}" data-field="porcentaje_avance"
            data-sort-value="{{ $subactivity->porcentaje_avance ?? 0 }}">
            <span class="badge editable-value"
                style="background-color: {{ heatmap_color($subactivity->porcentaje_avance ?? 0) }}; color: #fff;">
                {{ $subactivity->porcentaje_avance ?? 0 }}%
            </span>
            <input type="number" class="form-control form-control-sm editable-input"
                value="{{ $subactivity->porcentaje_avance ?? 0 }}" style="display:none; width: 70px;" min="0"
                max="100">
        </td>
        <td class="align-middle">
            <div class="description-cell">
                {{ Str::limit($subactivity->description, 30) }}
                @if (strlen($subactivity->description) > 30)
                    <span class="text-primary" style="cursor: pointer;" title="{{ $subactivity->description }}"
                        data-toggle="tooltip">
                        <i class="fas fa-info-circle"></i>
                    </span>
                @endif
            </div>
        </td>
        <td class="align-middle">
            <div class="status-cell" data-activity-id="{{ $subactivity->id }}">
                <div class="status-display">
                    @if ($subactivity->statuses->count() > 0)
                        @foreach ($subactivity->statuses as $status)
                            <span class="badge badge-pill mr-1 mb-1"
                                style="background-color: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                <i class="{{ $status->icon ?? 'fas fa-circle' }}"></i>
                                {{ $status->label }}
                            </span>
                        @endforeach
                    @else
                        {{-- Fallback al sistema anterior --}}
                        @php
                            $statusClass = match ($subactivity->status) {
                                'culminada' => 'success',
                                'en_ejecucion' => 'primary',
                                'en_espera_de_insumos' => 'warning',
                                default => 'secondary',
                            };
                            $statusIcon = match ($subactivity->status) {
                                'culminada' => 'check-circle',
                                'en_ejecucion' => 'play-circle',
                                'en_espera_de_insumos' => 'pause-circle',
                                default => 'circle',
                            };
                        @endphp
                        <span class="badge badge-{{ $statusClass }} badge-pill">
                            <i class="fas fa-{{ $statusIcon }}"></i>
                            {{ $subactivity->status_label }}
                        </span>
                    @endif
                </div>
                <div class="status-edit-btn">
                    <button class="btn btn-sm btn-outline-secondary edit-status-btn"
                        data-activity-id="{{ $subactivity->id }}" title="Editar estados">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>
        </td>
        <td class="align-middle">
            @if ($subactivity->analistas->isEmpty())
                <span class="text-muted">
                    <i class="fas fa-user-slash"></i> Sin asignar
                </span>
            @else
                <div class="analysts-list d-inline">
                    @foreach ($subactivity->analistas as $analista)
                        <span class="badge badge-light mr-1 mb-1">
                            <i class="fas fa-user"></i> {{ $analista->name }}
                        </span>
                    @endforeach
                </div>
                <div class="analysts-edit-btn-group" style="display: none;">
                    <button class="btn btn-sm btn-outline-secondary edit-analysts-btn ml-2"
                        data-activity-id="{{ $subactivity->id }}" title="Editar analistas">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            @endif
        </td>
        <td class="align-middle">
            @if ($subactivity->requirements->count() > 0)
                <div class="requirements-info">
                    <a href="{{ route('requirements.index', ['activity_id' => $subactivity->id]) }}"
                        class="text-decoration-none">
                        <span class="badge badge-warning badge-pill">
                            <i class="fas fa-clipboard-list"></i>
                            {{ $subactivity->requirements->count() }}
                        </span>
                    </a>
                    <div class="mt-1">
                        @php
                            $pendientes = $subactivity->requirements->where('status', 'pendiente')->count();
                            $recibidos = $subactivity->requirements->where('status', 'recibido')->count();
                        @endphp
                        <small class="text-muted d-block">
                            <span class="badge badge-sm badge-warning">{{ $pendientes }} pendientes</span>
                            <span class="badge badge-sm badge-success">{{ $recibidos }} recibidos</span>
                        </small>
                        @if ($subactivity->requirements->count() > 0)
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                {{ $subactivity->requirements->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            @else
                <span class="text-muted">
                    <i class="fas fa-clipboard"></i> Sin requerimientos
                </span>
            @endif
        </td>
        <td class="align-middle">
            @if ($subactivity->fecha_recepcion)
                <div class="date-info">
                    <span class="badge badge-outline-info">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $subactivity->fecha_recepcion->format('d/m/Y') }}
                    </span>
                    <div class="mt-1">
                        <small class="text-muted">
                            {{ $subactivity->fecha_recepcion->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @else
                <span class="text-muted">
                    <i class="fas fa-calendar-times"></i> No asignada
                </span>
            @endif
        </td>

    </tr>
    @if ($subactivity->subactivities->count() > 0)
        @include('activities.partials.subactivities', [
            'subactivities' => $subactivity->subactivities,
            'parentId' => $subactivity->id,
            'level' => $level + 1,
        ])
    @endif
@endforeach

<style>
    /* Botones de acción */
    .action-buttons .btn.btn-sm {
        margin: 0.1rem 0;
        border-radius: 6px;
        font-size: 0.8rem;
        padding: 0.2rem 0.4rem;
        transition: all 0.2s ease;
    }

    .action-buttons {
        display: none;
    }

    .activity-row:hover .action-buttons,
    .subactivity-row:hover .action-buttons {
        display: block !important;
    }

    /* Oculta el botón de editar analistas por defecto */
    .analysts-edit-btn-group {
        display: none;
    }

    /* Muestra el botón al hacer hover sobre la fila */
    .activity-row:hover .analysts-edit-btn-group,
    .subactivity-row:hover .analysts-edit-btn-group {
        display: inline-block !important;
    }
</style>

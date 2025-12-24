@php
    function heatmap_color($value)
    {
        $value = (int) $value;
        if ($value <= 0) {
            return '#dc3545';
        } // rojo
        if ($value < 50) {
            return '#fd7e14';
        } // naranja
        if ($value < 80) {
            return '#ffc107';
        } // amarillo
        return '#28a745'; // verde
    }
@endphp

<table id="main-activities-table" class="table table-hover mb-0 modern-table" style="min-width: 1100px;">
    <thead class="thead-light sticky-thead">
        <tr>
            <th class="border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="sortable" data-sort="caso" style="cursor: pointer;">
                        <i class="fas fa-hashtag text-primary"></i> Caso
                        <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                    </div>
                </div>
            </th>
            <th class="border-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-alt text-primary"></i> Nombre
                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="toggleAllSubactivitiesBtn"
                        title="Expandir/Colapsar todas las subactividades" style="margin-left: 8px; padding: 2px 8px;">
                        <i class="fas fa-chevron-down" id="toggleAllSubactivitiesIcon"></i>
                    </button>
                </div>
            </th>
            <th class="border-0 sortable" data-sort="prioridad" style="cursor: pointer;">
                <i class="fas fa-arrow-up text-primary"></i> Prioridad
                <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                <div class="custom-dropdown">
                    <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button"
                        data-filter="prioridad" style="padding: 2px 6px;">
                        <i class="fas fa-filter"></i>
                    </button>
                    <div class="custom-dropdown-menu" id="prioridad-filter-menu"
                        style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 150px;">
                        <h6 class="dropdown-header"
                            style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                            Filtrar por Prioridad</h6>
                        <div class="px-3 py-2">
                            <div class="form-check">
                                <input class="form-check-input prioridad-filter" type="checkbox" value=""
                                    id="prioridad-all" checked>
                                <label class="form-check-label" for="prioridad-all">Todas</label>
                            </div>
                            @for ($i = 1; $i <= 10; $i++)
                                <div class="form-check">
                                    <input class="form-check-input prioridad-filter" type="checkbox"
                                        value="{{ $i }}" id="prioridad-{{ $i }}">
                                    <label class="form-check-label"
                                        for="prioridad-{{ $i }}">{{ $i }}</label>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </th>
            <th class="border-0 sortable" data-sort="orden_analista" style="cursor: pointer;">
                <i class="fas fa-sort-numeric-up text-primary"></i> Orden
                <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                <div class="custom-dropdown">
                    <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button"
                        data-filter="orden_analista" style="padding: 2px 6px;">
                        <i class="fas fa-filter"></i>
                    </button>
                    <div class="custom-dropdown-menu" id="orden_analista-filter-menu"
                        style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 120px;">
                        <h6 class="dropdown-header"
                            style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                            Filtrar por Orden</h6>
                        <div class="px-3 py-2">
                            <div class="form-check">
                                <input class="form-check-input orden-filter" type="checkbox" value=""
                                    id="orden-all" checked>
                                <label class="form-check-label" for="orden-all">Todos</label>
                            </div>
                            @for ($i = 1; $i <= 10; $i++)
                                <div class="form-check">
                                    <input class="form-check-input orden-filter" type="checkbox"
                                        value="{{ $i }}" id="orden-{{ $i }}">
                                    <label class="form-check-label"
                                        for="orden-{{ $i }}">{{ $i }}</label>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </th>
            <th class="border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-user-tie text-primary"></i> Cliente
                    </span>
                    <div class="custom-dropdown">
                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button"
                            data-filter="clientes" style="padding: 2px 6px;">
                            <i class="fas fa-filter"></i>
                        </button>
                        <div class="custom-dropdown-menu" id="clientes-filter-menu"
                            style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                            <h6 class="dropdown-header"
                                style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                Filtrar por Cliente
                            </h6>
                            <div class="px-3 py-2">
                                <div class="form-check">
                                    <input class="form-check-input cliente-filter" type="checkbox" value=""
                                        id="cliente-all" checked>
                                    <label class="form-check-label" for="cliente-all">Todos</label>
                                </div>
                                @foreach ($clientes ?? [] as $cliente)
                                    <div class="form-check">
                                        <input class="form-check-input cliente-filter" type="checkbox"
                                            value="{{ $cliente->id }}" id="cliente-{{ $cliente->id }}">
                                        <label class="form-check-label" for="cliente-{{ $cliente->id }}">
                                            {{ \Illuminate\Support\Str::before($cliente->nombre, ' ') }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </th>
            <th class="border-0">
                <i class="fas fa-tasks text-primary"></i> Estado Operacional
            </th>
            <th class="border-0">
                <i class="fas fa-percentage text-primary"></i> % Avance
            </th>
            <th class="border-0">
                <i class="fas fa-align-left text-primary"></i> Descripción
            </th>
            <th class="border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="sortable" data-sort="status" style="cursor: pointer;">
                        <i class="fas fa-flag text-primary"></i> Estado
                        <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                    </div>
                    <div class="custom-dropdown">
                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button"
                            data-filter="status" style="padding: 2px 6px;">
                            <i class="fas fa-filter"></i>
                        </button>
                        <div class="custom-dropdown-menu" id="status-filter-menu"
                            style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                            <h6 class="dropdown-header"
                                style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                Filtrar por Estado</h6>
                            <div class="px-3 py-2">
                                <div class="form-check">
                                    <input class="form-check-input status-filter" type="checkbox" value=""
                                        id="status-all" checked>
                                    <label class="form-check-label" for="status-all">Todos</label>
                                </div>
                                @foreach ($statusLabels as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input status-filter" type="checkbox"
                                            value="{{ $key }}" id="status-{{ $key }}">
                                        <label class="form-check-label d-flex align-items-center"
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
                </div>
            </th>
            <th class="border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="sortable" data-sort="analistas" style="cursor: pointer;">
                        <i class="fas fa-users text-primary"></i> Analistas
                        <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                    </div>
                    <div class="custom-dropdown">
                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button"
                            data-filter="analistas" style="padding: 2px 6px;">
                            <i class="fas fa-filter"></i>
                        </button>
                        <div class="custom-dropdown-menu" id="analistas-filter-menu"
                            style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px;">
                            <h6 class="dropdown-header"
                                style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                Filtrar por Analista</h6>
                            <div class="px-3 py-2">
                                <div class="form-check">
                                    <input class="form-check-input analista-filter" type="checkbox" value=""
                                        id="analista-all" checked>
                                    <label class="form-check-label" for="analista-all">Todos</label>
                                </div>
                                @foreach ($analistas as $analista)
                                    <div class="form-check">
                                        <input class="form-check-input analista-filter" type="checkbox"
                                            value="{{ $analista->id }}" id="analista-{{ $analista->id }}">
                                        <label class="form-check-label"
                                            for="analista-{{ $analista->id }}">{{ $analista->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </th>
            <th class="border-0">
                <i class="fas fa-clipboard-list text-primary"></i> Requerimientos
            </th>
            <th class="border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="sortable" data-sort="fecha_recepcion" style="cursor: pointer;">
                        <i class="fas fa-calendar text-primary"></i> Fecha
                        <i class="fas fa-sort sort-icon text-muted ml-1"></i>
                    </div>
                    <div class="custom-dropdown">
                        <button class="btn btn-sm btn-outline-secondary filter-toggle" type="button"
                            data-filter="fecha" style="padding: 2px 6px;">
                            <i class="fas fa-filter"></i>
                        </button>
                        <div class="custom-dropdown-menu" id="fecha-filter-menu"
                            style="display: none; position: absolute; right: 0; top: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 250px;">
                            <h6 class="dropdown-header"
                                style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; margin: 0; padding: 0.5rem 1rem; border-radius: 8px 8px 0 0; font-weight: 600;">
                                Filtrar por Fecha</h6>
                            <div class="px-3 py-2">
                                <div class="form-group mb-2">
                                    <label class="small">Desde:</label>
                                    <input type="date" class="form-control form-control-sm"
                                        id="fecha-desde-filter">
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small">Hasta:</label>
                                    <input type="date" class="form-control form-control-sm"
                                        id="fecha-hasta-filter">
                                </div>
                                <button class="btn btn-sm btn-primary btn-block"
                                    id="apply-date-filter">Aplicar</button>
                                <button class="btn btn-sm btn-outline-secondary btn-block"
                                    id="clear-date-filter">Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse ($activities as $activity)
            {{-- Aquí va tu fila de actividad y subactividades, igual que antes --}}
            <tr class="parent-activity activity-row" data-activity-id="{{ $activity->id }}">
                <td class="align-middle">
                    <span class="badge badge-outline-primary font-weight-bold">
                        {{ $activity->caso }}
                    </span>
                    <div class="action-buttons" style="display: none;">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('activities.edit', $activity) }}"
                                class="btn btn-warning btn-sm action-btn" data-tooltip="Ver/Editar"
                                title="Ver/Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}"
                                class="btn btn-secondary btn-sm action-btn" data-tooltip="Crear Subactividad"
                                title="Crear Subactividad">
                                <i class="fas fa-plus"></i>
                            </a>
                            <form action="{{ route('activities.destroy', $activity) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm action-btn"
                                    data-tooltip="Eliminar" title="Eliminar"
                                    onclick="return confirm('¿Estás seguro de eliminar esta actividad y todas sus subactividades?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
                <td class="align-middle" style="vertical-align: middle;">
                    <div class="d-flex align-items-center">
                        @if ($activity->subactivities->count() > 0)
                            <span class="toggle-subactivities mr-2" style="cursor: pointer;"
                                data-activity-id="{{ $activity->id }}">
                                <i class="fas fa-chevron-right text-primary" id="icon-{{ $activity->id }}"></i>
                            </span>
                        @endif
                        <div>
                            <div class="font-weight-bold text-dark small">
                                {{ Str::limit($activity->name, 40) }}
                                @if (strlen($activity->name) > 40)
                                    <span class="text-primary" style="cursor: pointer;"
                                        title="{{ $activity->name }}" data-toggle="tooltip">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                @endif
                            </div>
                            @if ($activity->subactivities->count() > 0)
                                <small class="text-muted">
                                    <i class="fas fa-sitemap"></i>
                                    {{ $activity->subactivities->count() }} subactividad(es)
                                </small>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="align-middle editable-cell" data-activity-id="{{ $activity->id }}" data-field="prioridad"
                    data-sort-value="{{ $activity->prioridad ?? 0 }}">
                    <span class="badge badge-outline-info editable-value">{{ $activity->prioridad ?? '-' }}</span>
                    <input type="number" class="form-control form-control-sm editable-input"
                        value="{{ $activity->prioridad ?? 1 }}" style="display:none; width: 70px;" min="1">
                </td>
                <td class="align-middle editable-cell" data-activity-id="{{ $activity->id }}"
                    data-field="orden_analista" data-sort-value="{{ $activity->orden_analista ?? 0 }}">
                    <span
                        class="badge badge-outline-secondary editable-value">{{ $activity->orden_analista ?? '-' }}</span>
                    <input type="number" class="form-control form-control-sm editable-input"
                        value="{{ $activity->orden_analista ?? 1 }}" style="display:none; width: 70px;"
                        min="1">
                </td>
                <td class="align-middle">
                    {{ $activity->cliente ? \Illuminate\Support\Str::before($activity->cliente->nombre, ' ') : '-' }}
                </td>
                <td class="align-middle">
                    {{ Str::limit($activity->estatus_operacional, 40) }}
                    @if (strlen($activity->estatus_operacional) > 40)
                        <span class="text-primary" style="cursor: pointer;"
                            title="{{ $activity->estatus_operacional }}">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    @endif
                </td>
                <td class="align-middle editable-cell" data-activity-id="{{ $activity->id }}"
                    data-field="porcentaje_avance" data-sort-value="{{ $activity->porcentaje_avance ?? 0 }}">
                    <span class="badge editable-value"
                        style="background-color: {{ heatmap_color($activity->porcentaje_avance ?? 0) }}; color: #fff;">
                        {{ $activity->porcentaje_avance ?? 0 }}%
                    </span>
                    <input type="number" class="form-control form-control-sm editable-input"
                        value="{{ $activity->porcentaje_avance ?? 0 }}" style="display:none; width: 70px;"
                        min="0" max="100">
                </td>
                <td class="align-middle">
                    <div class="description-cell">
                        {{ Str::limit($activity->description, 30) }}
                        @if (strlen($activity->description) > 30)
                            <span class="text-primary" style="cursor: pointer;" title="{{ $activity->description }}"
                                data-toggle="tooltip">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        @endif
                    </div>
                </td>
                <td class="align-middle">
                    <div class="status-cell" data-activity-id="{{ $activity->id }}">
                        <div class="status-display">
                            @if ($activity->statuses->count() > 0)
                                @foreach ($activity->statuses as $status)
                                    <span class="badge badge-pill mr-1 mb-1 status-badge"
                                        data-status-id="{{ $status->id }}"
                                        style="background-color: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                        <i class="{{ $status->icon ?? 'fas fa-circle' }}"></i>
                                        {{ $status->label }}
                                    </span>
                                @endforeach
                            @else
                                @php
                                    $statusClass = match ($activity->status) {
                                        'culminada' => 'success',
                                        'en_ejecucion' => 'primary',
                                        'en_espera_de_insumos' => 'warning',
                                        default => 'secondary',
                                    };
                                    $statusIcon = match ($activity->status) {
                                        'culminada' => 'check-circle',
                                        'en_ejecucion' => 'play-circle',
                                        'en_espera_de_insumos' => 'pause-circle',
                                        default => 'circle',
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }} badge-pill">
                                    <i class="fas fa-{{ $statusIcon }}"></i>
                                    {{ $activity->status_label }}
                                </span>
                            @endif
                        </div>
                        <div class="status-edit-btn">
                            <button class="btn btn-sm btn-outline-secondary edit-status-btn"
                                data-activity-id="{{ $activity->id }}" title="Editar estados"
                                data-current-statuses="{{ $activity->statuses->pluck('id')->implode(',') }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="analysts-list d-inline">
                        @foreach ($activity->analistas as $analista)
                            <span class="badge badge-light mr-1 mb-1">
                                <i class="fas fa-user"></i> {{ $analista->name }}
                            </span>
                        @endforeach
                    </div>
                    <div class="analysts-edit-btn-group" style="display: none;">
                        <button class="btn btn-sm btn-outline-secondary edit-analysts-btn ml-2"
                            data-activity-id="{{ $activity->id }}" title="Editar analistas">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
                <td class="align-middle">
                    @if ($activity->requirements->count() > 0)
                        <div class="requirements-info">
                            <a href="{{ route('requirements.index', ['activity_id' => $activity->id]) }}"
                                class="text-decoration-none">
                                <span class="badge badge-warning badge-pill">
                                    <i class="fas fa-clipboard-list"></i>
                                    {{ $activity->requirements->count() }}
                                </span>
                            </a>
                            <div class="mt-1">
                                @php
                                    $pendientes = $activity->requirements->where('status', 'pendiente')->count();
                                    $recibidos = $activity->requirements->where('status', 'recibido')->count();
                                @endphp
                                <small class="text-muted d-block">
                                    <span class="badge badge-sm badge-warning">{{ $pendientes }} pendientes</span>
                                    <span class="badge badge-sm badge-success">{{ $recibidos }} recibidos</span>
                                </small>
                                @if ($activity->requirements->count() > 0)
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        {{ $activity->requirements->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}
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
                    @if ($activity->fecha_recepcion)
                        <div class="date-info">
                            <span class="badge badge-outline-info">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $activity->fecha_recepcion->format('d/m/Y') }}
                            </span>
                            <div class="mt-1">
                                <small class="text-muted">
                                    {{ $activity->fecha_recepcion->diffForHumans() }}
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
            @if ($activity->subactivities->count() > 0)
                @include('activities.partials.subactivities', [
                    'subactivities' => $activity->subactivities,
                    'parentId' => $activity->id,
                    'level' => 1,
                ])
            @endif
        @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay actividades registradas</h5>
                        <p class="text-muted">Comienza creando tu primera actividad</p>
                        <a href="{{ route('activities.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Actividad
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

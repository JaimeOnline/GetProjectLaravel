@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Actividades</h1>
    <a href="{{ route('activities.create') }}" class="btn btn-primary">Crear Nueva Actividad</a>
    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Caso</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Usuarios Asignados</th>
                <th>Comentarios</th>
                <th>Fecha de Recepción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
                <tr class="parent-activity" data-activity-id="{{ $activity->id }}">
                    <td>{{ $activity->caso }}</td>
                    <td>
                        @if ($activity->subactivities->count() > 0)
                            <span class="toggle-subactivities" style="cursor: pointer; margin-right: 5px;">
                                <i class="fas fa-chevron-right" id="icon-{{ $activity->id }}"></i>
                            </span>
                        @endif
                        {{ $activity->name }}
                        @if ($activity->subactivities->count() > 0)
                            <small class="text-muted">({{ $activity->subactivities->count() }} subactividades)</small>
                        @endif
                    </td>
                    <td>{{ $activity->description }}</td>
                    <td>{{ $activity->status_label }}</td>
                    <td>
                        @if ($activity->users->isEmpty())
                            Sin usuarios asignados
                        @else
                            @foreach ($activity->users as $user)
                                <span>{{ $user->name }}</span>
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if ($activity->comments->count() > 0)
                            <a href="{{ route('activities.comments', $activity) }}" class="text-decoration-none">
                                <span class="badge badge-info">{{ $activity->comments->count() }} comentario(s)</span>
                            </a>
                            <div class="mt-1">
                                <small class="text-muted">
                                    Último: {{ $activity->comments->last()->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        @else
                            <span class="text-muted">Sin comentarios</span>
                        @endif
                    </td>
                    <td>{{ $activity->fecha_recepcion ? $activity->fecha_recepcion->format('d-m-Y') : 'No asignada' }}</td>
                    <td>
                        <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('activities.destroy', $activity) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta actividad?')">Eliminar</button>
                        </form>
                        <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" class="btn btn-secondary btn-sm">Crear Subactividad</a>
                    </td>
                </tr>
                {{-- Mostrar subactividades (inicialmente ocultas) --}}
                @if ($activity->subactivities->count() > 0)
                    @include('activities.partials.subactivities', ['subactivities' => $activity->subactivities, 'parentId' => $activity->id, 'level' => 1])
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<style>
.subactivity-row {
    display: none;
    background-color: #f8f9fa;
}
.subactivity-row.level-1 td:first-child {
    padding-left: 30px;
}
.subactivity-row.level-2 td:first-child {
    padding-left: 50px;
}
.subactivity-row.level-3 td:first-child {
    padding-left: 70px;
}
.toggle-subactivities {
    transition: transform 0.2s;
}
.toggle-subactivities.expanded {
    transform: rotate(90deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para manejar el toggle de subactividades
    function setupToggleHandlers() {
        // Manejar el clic en las actividades padre para mostrar/ocultar subactividades
        document.querySelectorAll('.parent-activity').forEach(function(row) {
            const toggleIcon = row.querySelector('.toggle-subactivities');
            if (toggleIcon && !toggleIcon.hasAttribute('data-handler-attached')) {
                toggleIcon.setAttribute('data-handler-attached', 'true');
                toggleIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const activityId = row.getAttribute('data-activity-id');
                    const subactivities = document.querySelectorAll('.subactivity-row[data-parent-id="' + activityId + '"]');
                    const icon = document.getElementById('icon-' + activityId);
                    
                    if (subactivities.length > 0) {
                        const isVisible = subactivities[0].style.display !== 'none';
                        
                        subactivities.forEach(function(subRow) {
                            if (isVisible) {
                                subRow.style.display = 'none';
                                icon.className = 'fas fa-chevron-right';
                                toggleIcon.classList.remove('expanded');
                            } else {
                                subRow.style.display = 'table-row';
                                icon.className = 'fas fa-chevron-down';
                                toggleIcon.classList.add('expanded');
                            }
                        });
                    }
                });
            }
        });

        // Manejar el clic en subactividades que tienen sus propias subactividades
        document.querySelectorAll('.toggle-subactivities[data-subactivity-id]').forEach(function(toggle) {
            if (!toggle.hasAttribute('data-handler-attached')) {
                toggle.setAttribute('data-handler-attached', 'true');
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const subactivityId = this.getAttribute('data-subactivity-id');
                    const subactivities = document.querySelectorAll('.subactivity-row[data-parent-id="' + subactivityId + '"]');
                    const icon = document.getElementById('icon-sub-' + subactivityId);
                    
                    if (subactivities.length > 0) {
                        const isVisible = subactivities[0].style.display !== 'none';
                        
                        subactivities.forEach(function(subRow) {
                            if (isVisible) {
                                subRow.style.display = 'none';
                                icon.className = 'fas fa-chevron-right';
                                toggle.classList.remove('expanded');
                            } else {
                                subRow.style.display = 'table-row';
                                icon.className = 'fas fa-chevron-down';
                                toggle.classList.add('expanded');
                            }
                        });
                    }
                });
            }
        });
    }

    // Configurar handlers inicialmente
    setupToggleHandlers();
    
    // Reconfigurar handlers después de cualquier cambio dinámico en el DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                setupToggleHandlers();
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
</script>
@endsection
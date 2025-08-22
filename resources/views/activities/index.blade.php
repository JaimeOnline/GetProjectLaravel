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
                    <i class="fas fa-tasks text-primary"></i> Gestión de Actividades
                </h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> 
                    Administra todas las actividades del sistema y sus subactividades
                </p>
            </div>
            <div class="action-buttons">
                <a href="{{ route('activities.create') }}" class="btn btn-success btn-lg shadow-sm">
                    <i class="fas fa-plus"></i> Nueva Actividad
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $activities->count() }}</h3>
                    <p>Total Actividades</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $activities->where('status', 'culminada')->count() }}</h3>
                    <p>Culminadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-info">
                <div class="stats-icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $activities->where('status', 'en_ejecucion')->count() }}</h3>
                    <p>En Ejecución</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $activities->where('status', 'en_espera_de_insumos')->count() }}</h3>
                    <p>En Espera</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Lista de Actividades
                </h5>
                <div class="header-actions">
                    <small class="text-light">
                        <i class="fas fa-info-circle"></i> 
                        Haz clic en <i class="fas fa-chevron-right"></i> para ver subactividades
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 modern-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">
                                <i class="fas fa-hashtag text-primary"></i> Caso
                            </th>
                            <th class="border-0">
                                <i class="fas fa-file-alt text-primary"></i> Nombre
                            </th>
                            <th class="border-0">
                                <i class="fas fa-align-left text-primary"></i> Descripción
                            </th>
                            <th class="border-0">
                                <i class="fas fa-flag text-primary"></i> Estado
                            </th>
                            <th class="border-0">
                                <i class="fas fa-users text-primary"></i> Analistas
                            </th>
                            <th class="border-0">
                                <i class="fas fa-comments text-primary"></i> Comentarios
                            </th>
                            <th class="border-0">
                                <i class="fas fa-envelope text-primary"></i> Correos
                            </th>
                            <th class="border-0">
                                <i class="fas fa-calendar text-primary"></i> Fecha Recepción
                            </th>
                            <th class="border-0 text-center">
                                <i class="fas fa-cogs text-primary"></i> Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr class="parent-activity activity-row" data-activity-id="{{ $activity->id }}">
                                <td class="align-middle">
                                    <span class="badge badge-outline-primary font-weight-bold">
                                        {{ $activity->caso }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @if ($activity->subactivities->count() > 0)
                                            <span class="toggle-subactivities mr-2" style="cursor: pointer;">
                                                <i class="fas fa-chevron-right text-primary" id="icon-{{ $activity->id }}"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $activity->name }}</div>
                                            @if ($activity->subactivities->count() > 0)
                                                <small class="text-muted">
                                                    <i class="fas fa-sitemap"></i> 
                                                    {{ $activity->subactivities->count() }} subactividad(es)
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="description-cell">
                                        {{ Str::limit($activity->description, 80) }}
                                        @if(strlen($activity->description) > 80)
                                            <span class="text-primary" style="cursor: pointer;" 
                                                  title="{{ $activity->description }}" 
                                                  data-toggle="tooltip">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @php
                                        $statusClass = match($activity->status) {
                                            'culminada' => 'success',
                                            'en_ejecucion' => 'primary',
                                            'en_espera_de_insumos' => 'warning',
                                            default => 'secondary'
                                        };
                                        $statusIcon = match($activity->status) {
                                            'culminada' => 'check-circle',
                                            'en_ejecucion' => 'play-circle',
                                            'en_espera_de_insumos' => 'pause-circle',
                                            default => 'circle'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} badge-pill">
                                        <i class="fas fa-{{ $statusIcon }}"></i> {{ $activity->status_label }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    @if ($activity->analistas->isEmpty())
                                        <span class="text-muted">
                                            <i class="fas fa-user-slash"></i> Sin asignar
                                        </span>
                                    @else
                                        <div class="analysts-list">
                                            @foreach ($activity->analistas as $analista)
                                                <span class="badge badge-light mr-1 mb-1">
                                                    <i class="fas fa-user"></i> {{ $analista->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if ($activity->comments->count() > 0)
                                        <div class="comments-info">
                                            <a href="{{ route('activities.comments', $activity) }}" class="text-decoration-none">
                                                <span class="badge badge-info badge-pill">
                                                    <i class="fas fa-comments"></i> {{ $activity->comments->count() }}
                                                </span>
                                            </a>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $activity->comments->last()->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-comment-slash"></i> Sin comentarios
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if ($activity->emails->count() > 0)
                                        <div class="emails-info">
                                            <a href="{{ route('activities.emails', $activity) }}" class="text-decoration-none">
                                                <span class="badge badge-success badge-pill">
                                                    <i class="fas fa-envelope"></i> {{ $activity->emails->count() }}
                                                </span>
                                            </a>
                                            <div class="mt-1">
                                                @php
                                                    $lastEmail = $activity->emails->sortByDesc('created_at')->first();
                                                    $sentCount = $activity->emails->where('type', 'sent')->count();
                                                    $receivedCount = $activity->emails->where('type', 'received')->count();
                                                @endphp
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <span class="badge badge-outline-primary badge-sm mr-1">
                                                        <i class="fas fa-paper-plane"></i> {{ $sentCount }}
                                                    </span>
                                                    <span class="badge badge-outline-success badge-sm">
                                                        <i class="fas fa-inbox"></i> {{ $receivedCount }}
                                                    </span>
                                                </div>
                                                @if($lastEmail)
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> 
                                                        {{ $lastEmail->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-envelope-open"></i> Sin correos
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($activity->fecha_recepcion)
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
                                <td class="align-middle text-center">
                                    <div class="action-buttons">
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <a href="{{ route('activities.edit', $activity) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar actividad">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="{{ route('activities.emails', $activity) }}" 
                                               class="btn btn-info btn-sm"
                                               title="Ver correos">
                                                <i class="fas fa-envelope"></i> Correos
                                            </a>
                                            <a href="{{ route('activities.create', ['parentId' => $activity->id]) }}" 
                                               class="btn btn-secondary btn-sm"
                                               title="Crear subactividad">
                                                <i class="fas fa-plus"></i> Subactividad
                                            </a>
                                        </div>
                                        <form action="{{ route('activities.destroy', $activity) }}" 
                                              method="POST" 
                                              style="display:inline;" 
                                              class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm" 
                                                    title="Eliminar actividad"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta actividad y todas sus subactividades?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            {{-- Mostrar subactividades (inicialmente ocultas) --}}
                            @if ($activity->subactivities->count() > 0)
                                @include('activities.partials.subactivities', ['subactivities' => $activity->subactivities, 'parentId' => $activity->id, 'level' => 1])
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
            </div>
        </div>
    </div>
</div>

<style>
/* ===== ESTILOS ESPECÍFICOS PARA LA VISTA DE ACTIVIDADES ===== */

/* Header y estadísticas */
.page-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.text-gradient {
    background: linear-gradient(135deg, #007bff, #0056b3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Tarjetas de estadísticas */
.stats-card {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stats-card.bg-success {
    background: linear-gradient(135deg, #28a745, #1e7e34);
}

.stats-card.bg-info {
    background: linear-gradient(135deg, #17a2b8, #117a8b);
}

.stats-card.bg-warning {
    background: linear-gradient(135deg, #ffc107, #e0a800);
}

.stats-icon {
    font-size: 2.5rem;
    margin-right: 1rem;
    opacity: 0.8;
}

.stats-content h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.stats-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Tabla moderna */
.modern-table {
    font-size: 0.9rem;
}

.modern-table thead th {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    border-bottom: 2px solid #dee2e6;
}

.activity-row {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.activity-row:hover {
    background-color: #f8f9fa;
    border-left-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.activity-row td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #f1f3f4;
}

/* Badges mejorados */
.badge-outline-primary {
    color: #007bff;
    border: 1px solid #007bff;
    background: rgba(0, 123, 255, 0.1);
}

.badge-outline-success {
    color: #28a745;
    border: 1px solid #28a745;
    background: rgba(40, 167, 69, 0.1);
}

.badge-outline-info {
    color: #17a2b8;
    border: 1px solid #17a2b8;
    background: rgba(23, 162, 184, 0.1);
}

.badge-pill {
    border-radius: 50px;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

/* Información de analistas */
.analysts-list .badge {
    margin: 0.1rem;
    font-size: 0.75rem;
}

/* Información de comentarios y correos */
.comments-info, .emails-info, .date-info {
    text-align: center;
}

/* Botones de acción */
.action-buttons .btn {
    margin: 0.1rem 0;
    border-radius: 6px;
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    transition: all 0.2s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Subactividades */
.subactivity-row {
    display: none;
    background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
    border-left: 3px solid #007bff;
}

.subactivity-row.level-1 td:first-child {
    padding-left: 2rem;
}

.subactivity-row.level-2 td:first-child {
    padding-left: 3rem;
}

.subactivity-row.level-3 td:first-child {
    padding-left: 4rem;
}

.toggle-subactivities {
    transition: transform 0.3s ease;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 50%;
    background: rgba(0, 123, 255, 0.1);
}

.toggle-subactivities:hover {
    background: rgba(0, 123, 255, 0.2);
}

.toggle-subactivities.expanded {
    transform: rotate(90deg);
}

/* Estado vacío */
.empty-state {
    padding: 3rem;
}

.empty-state i {
    opacity: 0.5;
}

/* Descripción con tooltip */
.description-cell {
    max-width: 200px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .page-header {
        padding: 1rem;
    }
    
    .page-header .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .action-buttons {
        margin-top: 1rem;
    }
    
    .modern-table {
        font-size: 0.8rem;
    }
    
    .activity-row td {
        padding: 0.5rem;
    }
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.activity-row {
    animation: fadeIn 0.5s ease-out;
}

/* Tooltips mejorados */
[data-toggle="tooltip"] {
    cursor: help;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    if (typeof $!== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

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
                        
                        // Animación suave para mostrar/ocultar
                        subactivities.forEach(function(subRow, index) {
                            setTimeout(function() {
                                if (isVisible) {
                                    subRow.style.display = 'none';
                                    icon.className = 'fas fa-chevron-right text-primary';
                                    toggleIcon.classList.remove('expanded');
                                } else {
                                    subRow.style.display = 'table-row';
                                    icon.className = 'fas fa-chevron-down text-primary';
                                    toggleIcon.classList.add('expanded');
                                }
                            }, index * 50); // Retraso escalonado para efecto visual
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
                        
                        subactivities.forEach(function(subRow, index) {
                            setTimeout(function() {
                                if (isVisible) {
                                    subRow.style.display = 'none';
                                    icon.className = 'fas fa-chevron-right text-primary';
                                    toggle.classList.remove('expanded');
                                } else {
                                    subRow.style.display = 'table-row';
                                    icon.className = 'fas fa-chevron-down text-primary';
                                    toggle.classList.add('expanded');
                                }
                            }, index * 50);
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
                // Reinicializar tooltips para elementos nuevos
                if (typeof $!== 'undefined' && $.fn.tooltip) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Animación de entrada para las tarjetas de estadísticas
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(function(card, index) {
        setTimeout(function() {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(function() {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });

    // Mejorar la experiencia de hover en las filas
    document.querySelectorAll('.activity-row').forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Auto-dismiss para alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.classList.contains('show')) {
                alert.classList.remove('show');
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 150);
            }
        }, 5000);
    });
});
</script>
@endsection
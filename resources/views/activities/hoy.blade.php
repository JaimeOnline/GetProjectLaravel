@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
            <button class="btn btn-primary mb-2 mb-md-0" onclick="copiarReporte()">
                <i class="fas fa-copy"></i> Copiar reporte
            </button>

            {{-- Filtro de cliente --}}
            <form method="GET" action="{{ route('activities.hoy') }}" class="form-inline">
                <label for="cliente_id" class="mr-2 mb-1 mb-md-0">Cliente:</label>
                <select name="cliente_id" id="cliente_id" class="form-control form-control-sm"
                    onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($clientes ?? [] as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::before($cliente->nombre, ' ') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <h1>Atenciones {{ \Carbon\Carbon::now()->format('d-m-y') }}</h1>
        <p class="text-muted">
            Total de actividades en este reporte:
            <strong>{{ $activities->count() }}</strong>
        </p>

        @forelse($activities as $activity)
            @include('activities.partials.hoy_activity_item', [
                'activity' => $activity,
                'index' => $loop->iteration,
                'analistas' => $analistas,
            ])
        @empty
            <p>No hay actividades atendiendo hoy.</p>
        @endforelse

        <!-- Área invisible para copiar el texto plano -->
        <textarea id="reporte-texto" style="position:absolute; left:-9999px; top:-9999px;">
Atenciones {{ \Carbon\Carbon::now()->format('d-m-y') }}

@forelse($activities as $activity)
{{ $loop->iteration }}. {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
Estatus: {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}
Responsable: @if ($activity->analistas->count())
{{ $activity->analistas->pluck('name')->implode(', ') }}
@else
No asignado
@endif
Avance: {{ $activity->porcentaje_avance ?? 0 }}%
Estimación de entrega: {{ $activity->fecha_estimacion_entrega ? \Carbon\Carbon::parse($activity->fecha_estimacion_entrega)->format('d-m-y') : 'Sin estimación' }}

@empty
No hay actividades atendiendo hoy.
@endforelse
        </textarea>
    </div>
    <script>
        function copiarReporte() {
            const textarea = document.getElementById('reporte-texto');
            textarea.select();
            document.execCommand('copy');
            alert('Reporte copiado al portapapeles');
        }

        document.addEventListener('DOMContentLoaded', function() {

            function attachHoyHandlers(container) {
                const activityId = container.getAttribute('data-activity-id');
                if (!activityId) return;

                // Guardar id al hacer clic en Ver/Editar
                const editLink = container.querySelector('.hoy-edit-link');
                if (editLink) {
                    editLink.addEventListener('click', function() {
                        localStorage.setItem('reload_hoy_activity_id', activityId);
                    });
                }

                // --- Estatus operacional ---
                const estatusSpan = container.querySelector('.estatus-operacional-display');
                const estatusInput = container.querySelector('.estatus-operacional-input');
                if (estatusSpan && estatusInput) {
                    estatusSpan.addEventListener('click', function() {
                        estatusSpan.style.display = 'none';
                        estatusInput.style.display = 'block';
                        estatusInput.focus();
                        estatusInput.select();
                    });

                    function saveEstatus() {
                        const value = estatusInput.value;
                        fetch(`/activities/${activityId}/inline-estatus`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    estatus_operacional: value
                                })
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    estatusSpan.textContent = value || 'Sin estatus operacional';
                                } else {
                                    alert('Error al actualizar el estatus operacional');
                                }
                                estatusInput.style.display = 'none';
                                estatusSpan.style.display = 'inline';
                            }).catch(() => {
                                alert('Error al actualizar el estatus operacional');
                                estatusInput.style.display = 'none';
                                estatusSpan.style.display = 'inline';
                            });
                    }

                    estatusInput.addEventListener('blur', saveEstatus);
                    estatusInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            saveEstatus();
                        } else if (e.key === 'Escape') {
                            estatusInput.style.display = 'none';
                            estatusSpan.style.display = 'inline';
                        }
                    });
                }

                // --- Avance (porcentaje_avance) ---
                const avanceSpan = container.querySelector('.avance-display');
                const avanceInput = container.querySelector('.avance-input');
                if (avanceSpan && avanceInput) {
                    avanceSpan.addEventListener('click', function() {
                        avanceSpan.style.display = 'none';
                        avanceInput.style.display = 'block';
                        avanceInput.focus();
                        avanceInput.select();
                    });

                    function saveAvance() {
                        let value = parseInt(avanceInput.value || '0', 10);
                        if (isNaN(value)) value = 0;
                        if (value < 0) value = 0;
                        if (value > 100) value = 100;

                        fetch(`/activities/${activityId}/inline-update`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    field: 'porcentaje_avance',
                                    value: value
                                })
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    avanceSpan.textContent = (data.value ?? value) + '%';
                                } else {
                                    alert('Error al actualizar el avance');
                                }
                                avanceInput.style.display = 'none';
                                avanceSpan.style.display = 'inline';
                            }).catch(() => {
                                alert('Error al actualizar el avance');
                                avanceInput.style.display = 'none';
                                avanceSpan.style.display = 'inline';
                            });
                    }

                    avanceInput.addEventListener('blur', saveAvance);
                    avanceInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            saveAvance();
                        } else if (e.key === 'Escape') {
                            avanceInput.style.display = 'none';
                            avanceSpan.style.display = 'inline';
                        }
                    });
                }

                // --- Fecha estimación de entrega ---
                const estimacionSpan = container.querySelector('.estimacion-display');
                const estimacionInput = container.querySelector('.estimacion-input');
                if (estimacionSpan && estimacionInput) {
                    estimacionSpan.addEventListener('click', function() {
                        estimacionSpan.style.display = 'none';
                        estimacionInput.style.display = 'block';
                        estimacionInput.focus();
                    });

                    function formatFecha(d) {
                        if (!d) return 'Sin estimación';
                        const parts = d.split('-');
                        if (parts.length !== 3) return 'Sin estimación';
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }

                    function saveEstimacion() {
                        const value = estimacionInput.value || null;

                        fetch(`/activities/${activityId}/inline-fecha-estimacion`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    fecha_estimacion_entrega: value
                                })
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    estimacionSpan.textContent = formatFecha(value);
                                } else {
                                    alert('Error al actualizar la estimación de entrega');
                                }
                                estimacionInput.style.display = 'none';
                                estimacionSpan.style.display = 'inline';
                            }).catch(() => {
                                alert('Error al actualizar la estimación de entrega');
                                estimacionInput.style.display = 'none';
                                estimacionSpan.style.display = 'inline';
                            });
                    }

                    estimacionInput.addEventListener('blur', saveEstimacion);
                    estimacionInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            saveEstimacion();
                        } else if (e.key === 'Escape') {
                            estimacionInput.style.display = 'none';
                            estimacionSpan.style.display = 'inline';
                        }
                    });
                }

                // --- Responsable (analistas) ---
                const respSpan = container.querySelector('.responsable-display');
                const respEditor = container.querySelector('.responsable-editor');
                const respSelect = container.querySelector('.responsable-select');
                const respSaveBtn = container.querySelector('.responsable-save-btn');
                const respCancelBtn = container.querySelector('.responsable-cancel-btn');

                if (respSpan && respEditor && respSelect && respSaveBtn && respCancelBtn) {
                    // Mostrar editor al hacer clic en el texto
                    respSpan.addEventListener('click', function() {
                        respSpan.style.display = 'none';
                        respEditor.style.display = 'inline-block';
                    });

                    // Cancelar edición
                    respCancelBtn.addEventListener('click', function() {
                        respEditor.style.display = 'none';
                        respSpan.style.display = 'inline';
                    });

                    // Guardar cambios
                    respSaveBtn.addEventListener('click', function() {
                        const selectedOptions = Array.from(respSelect.options)
                            .filter(opt => opt.selected)
                            .map(opt => opt.value);

                        if (selectedOptions.length === 0) {
                            if (!confirm(
                                    'No hay analistas seleccionados. ¿Deseas dejar la actividad sin responsable?'
                                )) {
                                return;
                            }
                        }

                        fetch(`/activities/${activityId}/analysts`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    analista_id: selectedOptions
                                })
                            }).then(res => res.json())
                            .then(data => {
                                if (data.success && data.analistas) {
                                    const names = data.analistas.map(a => a.name).join(', ');
                                    respSpan.textContent = names || 'No asignado';
                                } else {
                                    console.error('Error updateAnalysts:', data);
                                    alert('Error al actualizar el responsable');
                                }
                                respEditor.style.display = 'none';
                                respSpan.style.display = 'inline';
                            }).catch((err) => {
                                console.error('Error updateAnalysts catch:', err);
                                alert('Error al actualizar el responsable');
                                respEditor.style.display = 'none';
                                respSpan.style.display = 'inline';
                            });
                    });
                }
            }

            // Inicializar handlers para todas las tarjetas existentes
            document.querySelectorAll('.activity-hoy').forEach(function(container) {
                attachHoyHandlers(container);
            });

            // Al volver de editar, recargar automáticamente la tarjeta
            window.addEventListener('focus', function() {
                const activityId = localStorage.getItem('reload_hoy_activity_id');
                if (!activityId) return;

                const container = document.querySelector('.activity-hoy[data-activity-id="' + activityId +
                    '"]');
                if (!container) {
                    localStorage.removeItem('reload_hoy_activity_id');
                    return;
                }

                fetch("{{ url('/activities/hoy') }}/" + activityId)
                    .then(res => res.text())
                    .then(html => {
                        const tmp = document.createElement('div');
                        tmp.innerHTML = html.trim();
                        const newNode = tmp.firstElementChild;
                        if (newNode) {
                            container.replaceWith(newNode);
                            attachHoyHandlers(newNode);
                        }
                        localStorage.removeItem('reload_hoy_activity_id');
                    });
            });
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary" onclick="copiarReporte()">
                <i class="fas fa-copy"></i> Copiar reporte
            </button>

            {{-- Filtro de cliente --}}
            <form method="GET" action="{{ route('activities.insumos') }}" class="form-inline">
                <label for="cliente_id" class="mr-2 mb-0">Cliente:</label>
                <select name="cliente_id" id="cliente_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($clientes ?? [] as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::before($cliente->nombre, ' ') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <h1>Pendientes por insumos/información:</h1>
        <p class="text-muted">
            Total de actividades en este reporte:
            <strong>{{ $activities->count() }}</strong>
        </p>

        @forelse($activities as $activity)
            @include('activities.partials.insumo_activity_item', [
                'activity' => $activity,
                'index' => $loop->iteration,
            ])
        @empty
            <p>No hay actividades pendientes por insumos o información.</p>
        @endforelse

        <!-- Área invisible para copiar el texto plano -->
        <textarea id="reporte-texto" style="position:absolute; left:-9999px; top:-9999px;">
Pendientes por insumos/información:

@forelse($activities as $activity)
{{ $loop->iteration }}. {{ $activity->caso ? $activity->caso . ' ' : '' }}{{ $activity->name }}
Estatus: {{ $activity->estatus_operacional ?? 'Sin estatus operacional' }}

@empty
No hay actividades pendientes por insumos o información.
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
            // Edición en línea de estatus_operacional
            document.querySelectorAll('.activity-insumo').forEach(function(container) {
                const activityId = container.getAttribute('data-activity-id');
                const displaySpan = container.querySelector('.estatus-operacional-display');
                const input = container.querySelector('.estatus-operacional-input');

                if (!displaySpan || !input) return;

                displaySpan.style.cursor = 'pointer';

                displaySpan.addEventListener('click', function() {
                    displaySpan.style.display = 'none';
                    input.style.display = 'block';
                    input.focus();
                    input.select();
                });

                function saveEstatus() {
                    const value = input.value;

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
                                displaySpan.textContent = value || 'Sin estatus operacional';
                            } else {
                                alert('Error al actualizar el estatus operacional');
                            }
                            input.style.display = 'none';
                            displaySpan.style.display = 'inline';
                        }).catch(() => {
                            alert('Error al actualizar el estatus operacional');
                            input.style.display = 'none';
                            displaySpan.style.display = 'inline';
                        });
                }

                input.addEventListener('blur', saveEstatus);
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        saveEstatus();
                    } else if (e.key === 'Escape') {
                        input.style.display = 'none';
                        displaySpan.style.display = 'inline';
                    }
                });
            });

            // Delegación para enlace Ver/Editar: guardar actividad a recargar
            document.querySelectorAll('.insumo-edit-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    const container = this.closest('.activity-insumo');
                    if (!container) return;
                    const activityId = container.getAttribute('data-activity-id');
                    if (activityId) {
                        localStorage.setItem('reload_insumo_activity_id', activityId);
                    }
                });
            });

            // Al volver de editar, recargar automáticamente el bloque de actividad
            window.addEventListener('focus', function() {
                const activityId = localStorage.getItem('reload_insumo_activity_id');
                if (!activityId) return;

                const container = document.querySelector('.activity-insumo[data-activity-id="' +
                    activityId + '"]');
                if (!container) {
                    localStorage.removeItem('reload_insumo_activity_id');
                    return;
                }

                fetch("{{ url('/activities/insumos') }}/" + activityId)
                    .then(res => res.text())
                    .then(html => {
                        const tmp = document.createElement('div');
                        tmp.innerHTML = html.trim();
                        const newNode = tmp.firstElementChild;
                        if (newNode) {
                            container.replaceWith(newNode);
                        }
                        // Volver a enganchar handlers de edición en línea y enlace editar para este bloque
                        const span = newNode.querySelector('.estatus-operacional-display');
                        const input = newNode.querySelector('.estatus-operacional-input');
                        if (span && input) {
                            span.style.cursor = 'pointer';
                            span.addEventListener('click', function() {
                                span.style.display = 'none';
                                input.style.display = 'block';
                                input.focus();
                                input.select();
                            });

                            function saveEstatusInline() {
                                const value = input.value;
                                fetch(`/activities/${activityId}/inline-estatus`, {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').getAttribute(
                                                'content')
                                        },
                                        body: JSON.stringify({
                                            estatus_operacional: value
                                        })
                                    }).then(res => res.json())

                                    .then(data => {
                                        if (data.success) {
                                            span.textContent = value || 'Sin estatus operacional';
                                        } else {
                                            alert('Error al actualizar el estatus operacional');
                                        }
                                        input.style.display = 'none';
                                        span.style.display = 'inline';
                                    }).catch(() => {
                                        alert('Error al actualizar el estatus operacional');
                                        input.style.display = 'none';
                                        span.style.display = 'inline';
                                    });
                            }
                            input.addEventListener('blur', saveEstatusInline);
                            input.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    saveEstatusInline();
                                } else if (e.key === 'Escape') {
                                    input.style.display = 'none';
                                    span.style.display = 'inline';
                                }
                            });
                        }

                        const editLink = newNode.querySelector('.insumo-edit-link');
                        if (editLink) {
                            editLink.addEventListener('click', function() {
                                const container = this.closest('.activity-insumo');
                                const aid = container && container.getAttribute(
                                    'data-activity-id');
                                if (aid) {
                                    localStorage.setItem('reload_insumo_activity_id', aid);
                                }
                            });
                        }

                        localStorage.removeItem('reload_insumo_activity_id');
                    });
            });
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Actividades por Analista</h1>

        <!-- Botón para mostrar/ocultar filtros en móvil -->
        <div class="d-flex justify-content-between align-items-center mb-2 d-md-none">
            <button type="button" class="btn btn-outline-primary btn-sm" id="toggleFiltersAnalistas">
                <i class="fas fa-filter"></i> Mostrar filtros
            </button>
        </div>

        <div id="sticky-filter"
            style="position:sticky;top:0;z-index:20;background:#fff;padding-top:1rem;padding-bottom:0.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <div id="sticky-filter-inner">
                <form method="GET" class="mb-0" id="status-filter-form" autocomplete="off" onsubmit="return false;">
                    <div class="row align-items-end">
                        <div class="col-md-7">
                            <label class="font-weight-bold mb-1 d-block">Filtrar por Estado:</label>
                            <div class="btn-group btn-group-toggle flex-wrap" data-toggle="buttons" id="status-btn-group">
                                @foreach ($statuses as $status)
                                    <label class="btn btn-outline-primary mb-2 mr-2"
                                        style="min-width: 120px; font-weight: 500;">
                                        <input type="checkbox" name="status[]" value="{{ $status->id }}"
                                            autocomplete="off">
                                        <span class="badge"
                                            style="background: {{ $status->color }}; color: {{ $status->getContrastColor() }};">
                                            {{ $status->label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <small class="text-muted d-block mt-1">Haz clic en los botones para seleccionar/deseleccionar
                                estados.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="font-weight-bold mb-1 d-block">Cliente:</label>
                            <select id="filterCliente" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                @foreach ($clientes ?? [] as $cliente)
                                    <option value="{{ $cliente->id }}">
                                        {{ \Illuminate\Support\Str::before($cliente->nombre, ' ') }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Aplica a todas las actividades cargadas.</small>
                        </div>

                        <div class="col-md-2">
                            <a href="{{ route('activities.analistas') }}" class="btn btn-secondary mb-2 ml-2">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Toggle filtros en móvil
                const toggleBtn = document.getElementById('toggleFiltersAnalistas');
                const filterInner = document.getElementById('sticky-filter-inner');
                if (toggleBtn && filterInner) {
                    // Ocultar por defecto en pantallas pequeñas
                    if (window.innerWidth <= 768) {
                        filterInner.style.display = 'none';
                    }

                    toggleBtn.addEventListener('click', function() {
                        const isHidden = filterInner.style.display === 'none';
                        filterInner.style.display = isHidden ? 'block' : 'none';
                        this.innerHTML = isHidden ?
                            '<i class="fas fa-filter"></i> Ocultar filtros' :
                            '<i class="fas fa-filter"></i> Mostrar filtros';
                    });
                }
                // --- Preseleccionar "En Ejecución" en el front si no hay filtro ---
                const statusCheckboxes = document.querySelectorAll('input[type="checkbox"][name="status[]"]');
                const hasAnyChecked = Array.from(statusCheckboxes).some(cb => cb.checked);
                if (!hasAnyChecked) {
                    // Buscar el checkbox de "En Ejecución" y marcarlo
                    let found = false;
                    statusCheckboxes.forEach(function(cb) {
                        const label = cb.parentElement.textContent.trim().toLowerCase();
                        if (!found && (label.includes('ejecución') || label.includes('ejecucion'))) {
                            cb.checked = true;
                            cb.parentElement.classList.add('active');
                            found = true;
                        }
                    });
                    // Disparar el evento change para que se aplique el filtro automáticamente
                    if (found) {
                        statusCheckboxes.forEach(function(cb) {
                            if (cb.checked) {
                                cb.dispatchEvent(new Event('change'));
                            }
                        });
                    }
                }

                const checkboxes = document.querySelectorAll('input[type="checkbox"][name="status[]"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        // Guardar los IDs de los acordeones abiertos
                        const openAnalistas = Array.from(document.querySelectorAll('.collapse.show'))
                            .map(div => div.id.replace('collapse-', ''));
                        // Recargar solo los abiertos y mantenerlos abiertos
                        openAnalistas.forEach(function(analistaId) {
                            const collapseDiv = document.getElementById('collapse-' +
                                analistaId);
                            if (collapseDiv) {
                                collapseDiv.classList.remove('loaded');
                                // Recargar actividades con los nuevos filtros
                                loadActivities(analistaId, 1, false);
                                // Mantener abierto
                                collapseDiv.classList.add('show');
                            }
                        });
                    });
                });

                function initSortable(analistaId) {
                    var tbody = document.getElementById('sortable-tbody-' + analistaId);
                    if (tbody && typeof Sortable !== "undefined") {
                        if (tbody._sortable) {
                            tbody._sortable.destroy();
                        }
                        tbody._sortable = new Sortable(tbody, {
                            handle: '.orden-analista-handle',
                            animation: 150,
                            onEnd: function(evt) {
                                const ids = Array.from(tbody.querySelectorAll('tr')).map(tr => tr
                                    .getAttribute('data-activity-id'));
                                fetch('/activities/reorder', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        ids: ids
                                    })
                                }).then(res => res.json()).then(data => {
                                    Array.from(tbody.querySelectorAll('tr')).forEach(function(tr,
                                        idx) {
                                        tr.querySelector('.orden-analista-handle .badge')
                                            .textContent = idx + 1;
                                    });
                                });
                            }
                        });
                    }
                }



                // loadActivities ya no necesita statusFilterOverride, siempre toma el filtro actual
                window.loadActivities = function(analistaId, page = 1, append = false) {
                    const tableDiv = document.getElementById('activities-table-' + analistaId);
                    const loadMoreDiv = document.getElementById('load-more-' + analistaId);
                    const statusFilter = Array.from(document.querySelectorAll('input[name="status[]"]:checked'))
                        .map(cb => cb.value);
                    if (!append) {
                        tableDiv.innerHTML =
                            '<div class="text-center text-muted py-3">Cargando actividades...</div>';
                        loadMoreDiv.innerHTML = '';
                    } else {
                        loadMoreDiv.innerHTML = '<div class="text-center text-muted py-2">Cargando más...</div>';
                    }
                    let url = "{{ url('/activities/analistas') }}/" + analistaId + "/actividades?page=" + page;
                    if (statusFilter.length) {
                        url += "&" + statusFilter.map(s => "status[]=" + encodeURIComponent(s)).join("&");
                    }

                    // Filtro de cliente (si está seleccionado)
                    const clienteSelect = document.getElementById('filterCliente');
                    if (clienteSelect && clienteSelect.value) {
                        url += "&cliente_id=" + encodeURIComponent(clienteSelect.value);
                    }

                    fetch(url, {
                            cache: 'no-store'
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!append) {
                                tableDiv.innerHTML = data.html;
                                initSortable(analistaId);
                            } else {
                                tableDiv.querySelector('tbody').insertAdjacentHTML('beforeend', data.html
                                    .replace(/^[\s\S]*<tbody>|<\/tbody>[\s\S]*$/g, ''));
                                initSortable(analistaId);
                            }
                            // Actualizar badge de cantidad
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const count = (tempDiv.querySelectorAll('tbody tr').length) || 0;
                            document.getElementById('badge-count-' + analistaId).textContent = count;

                            if (data.next_page) {
                                loadMoreDiv.innerHTML = '<button class="btn btn-link btn-sm" data-next-page="' +
                                    (
                                        page + 1) + '">Cargar más</button>';
                                loadMoreDiv.querySelector('button').onclick = function() {
                                    loadActivities(analistaId, page + 1, true);
                                };
                            } else {
                                loadMoreDiv.innerHTML = '';
                            }
                        });
                }

            });
        </script>

        <div class="mb-3 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-primary btn-sm mr-2" id="expand-all-btn">
                <i class="fas fa-plus-square"></i> Expandir todos
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="collapse-all-btn">
                <i class="fas fa-minus-square"></i> Colapsar todos
            </button>
        </div>
        <div class="accordion" id="analistasAccordion">
            @foreach ($analistas as $analista)
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between"
                        id="heading-{{ $analista->id }}" style="border-radius: 0.5rem;">
                        <div class="d-flex align-items-center">
                            <div
                                style="background: #007bff; color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-right: 1rem;">
                                {{ strtoupper(substr($analista->name, 0, 2)) }}
                            </div>
                            <span class="font-weight-bold"
                                style="font-size: 1.2rem; color: #222;">{{ $analista->name }}</span>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-toggle="collapse"
                            data-target="#collapse-{{ $analista->id }}" aria-expanded="false"
                            aria-controls="collapse-{{ $analista->id }}">
                            <i class="fas fa-chevron-down"></i>
                            <span class="ml-2 badge badge-primary" id="badge-count-{{ $analista->id }}">...</span>
                        </button>
                    </div>
                    <div id="collapse-{{ $analista->id }}" class="collapse" aria-labelledby="heading-{{ $analista->id }}"
                        data-parent="#analistasAccordion">
                        <div class="card-body bg-light">
                            <div class="table-responsive analyst-table-wrapper" id="activities-table-{{ $analista->id }}">
                                <div class="text-center text-muted py-3">Cargando actividades...</div>
                            </div>
                            <div class="text-center mt-2" id="load-more-{{ $analista->id }}"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <script>
            // Cargar actividades por AJAX al abrir el acordeón y paginar con infinite scroll
            document.addEventListener('DOMContentLoaded', function() {
                // const statusFilter = Array.from(document.querySelectorAll('input[name="status[]"]:checked')).map(cb =>
                //     cb.value);

                // Nueva versión de loadActivities: siempre toma el filtro actual
                // loadActivities ya no necesita statusFilterOverride, siempre toma el filtro actual

                // Recargar solo una fila de actividad (tras editar)
                function reloadSingleActivity(analistaId, activityId, btn) {
                    let url = "{{ url('/activities/analistas') }}/" + analistaId + "/actividades?activity_id=" +
                        activityId;
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            // Reemplazar la fila de la actividad editada
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const newRow = tempDiv.querySelector('tr[data-activity-id="' + activityId + '"]');
                            const table = document.getElementById('activities-table-' + analistaId).querySelector(
                                'table');
                            if (table && newRow) {
                                const oldRow = table.querySelector('tr[data-activity-id="' + activityId + '"]');
                                if (oldRow) {
                                    oldRow.replaceWith(newRow);
                                }
                            }

                            if (btn) btn.style.display = 'none';
                            // Limpiar el localStorage
                            localStorage.removeItem('reload_activity_id');
                            localStorage.removeItem('reload_analista_id');
                        });

                }

                function analistaIdFromBtn(btn) {
                    // Busca el analistaId desde el DOM
                    let el = btn;
                    while (el && !el.closest('.card')) el = el.parentElement;
                    if (el) {
                        const card = el.closest('.card');
                        if (card) {
                            const id = card.querySelector('.card-header .btn[data-toggle="collapse"]').getAttribute(
                                'data-target');
                            return id.replace('#collapse-', '');
                        }
                    }
                    return null;
                }

                // Delegación de eventos para enlaces de editar y botones de recarga
                const analistasAccordion = document.getElementById('analistasAccordion');
                if (analistasAccordion) {
                    analistasAccordion.addEventListener('click', function(e) {
                        const editLink = e.target.closest('.edit-activity-link');
                        if (editLink) {
                            const row = editLink.closest('tr[data-activity-id]');
                            const activityId = row ? row.getAttribute('data-activity-id') : null;
                            const analistaId = analistaIdFromBtn(editLink);
                            if (activityId && analistaId) {
                                localStorage.setItem('reload_activity_id', activityId);
                                localStorage.setItem('reload_analista_id', analistaId);
                            }
                            return; // dejamos que el link abra la pestaña normalmente
                        }

                        const reloadBtn = e.target.closest('.reload-activity-btn');
                        if (reloadBtn) {
                            const activityId = reloadBtn.dataset.activityId;
                            const analistaId = analistaIdFromBtn(reloadBtn);
                            if (activityId && analistaId) {
                                reloadSingleActivity(analistaId, activityId, reloadBtn);
                            }
                        }
                    });
                }


                // Al abrir un acordeón, cargar actividades si no se han cargado
                document.querySelectorAll('.card-header .btn[data-toggle="collapse"]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const analistaId = this.getAttribute('data-target').replace('#collapse-', '');
                        const collapseDiv = document.getElementById('collapse-' + analistaId);
                        if (!collapseDiv.classList.contains('loaded')) {
                            loadActivities(analistaId);
                            collapseDiv.classList.add('loaded');
                        }
                    });
                });

                // Si quieres abrir el primero automáticamente:
                @if ($analistas->count())
                    setTimeout(function() {
                        // Busca el primer acordeón y expándelo, luego carga actividades
                        const btn = document.querySelector('.card-header .btn[data-toggle="collapse"]');
                        if (btn) {
                            const analistaId = btn.getAttribute('data-target').replace('#collapse-', '');
                            const collapseDiv = document.getElementById('collapse-' + analistaId);
                            if (!collapseDiv.classList.contains('show')) {
                                collapseDiv.classList.add('show');
                            }
                            if (!collapseDiv.classList.contains('loaded')) {
                                loadActivities(analistaId);
                                collapseDiv.classList.add('loaded');
                            }
                        }
                    }, 300);
                @endif

                // Al cambiar el filtro de estado, recarga solo los acordeones abiertos y los mantiene abiertos
                document.querySelectorAll('input[name="status[]"]').forEach(function(cb) {
                    cb.addEventListener('change', function() {
                        const openAnalistas = Array.from(document.querySelectorAll('.collapse.show'))
                            .map(div => div.id.replace('collapse-', ''));
                        openAnalistas.forEach(function(analistaId) {
                            const collapseDiv = document.getElementById('collapse-' +
                                analistaId);
                            if (collapseDiv) {
                                collapseDiv.classList.remove('loaded');
                                loadActivities(analistaId);
                                collapseDiv.classList.add('show');
                            }
                        });
                    });
                });

                // Al cambiar el filtro de cliente, recargar también los acordeones abiertos
                const filterCliente = document.getElementById('filterCliente');
                if (filterCliente) {
                    filterCliente.addEventListener('change', function() {
                        const openAnalistas = Array.from(document.querySelectorAll('.collapse.show'))
                            .map(div => div.id.replace('collapse-', ''));
                        openAnalistas.forEach(function(analistaId) {
                            const collapseDiv = document.getElementById('collapse-' + analistaId);
                            if (collapseDiv) {
                                collapseDiv.classList.remove('loaded');
                                loadActivities(analistaId);
                                collapseDiv.classList.add('show');
                            }
                        });
                    });
                }

                // --- Al volver de editar, recargar automáticamente la actividad ---
                window.addEventListener('focus', function() {
                    const activityId = localStorage.getItem('reload_activity_id');
                    const analistaId = localStorage.getItem('reload_analista_id');
                    if (activityId && analistaId) {
                        // Busca el botón de recarga y llama a reloadSingleActivity
                        const tableDiv = document.getElementById('activities-table-' + analistaId);
                        if (tableDiv) {
                            const btn = tableDiv.querySelector('.reload-activity-btn[data-activity-id="' +
                                activityId + '"]');
                            if (btn) {
                                reloadSingleActivity(analistaId, activityId, btn);
                            } else {
                                // Si por alguna razón no está el botón, recargamos todo el acordeón
                                const collapseDiv = document.getElementById('collapse-' + analistaId);
                                if (collapseDiv) {
                                    collapseDiv.classList.remove('loaded');
                                    loadActivities(analistaId);
                                    collapseDiv.classList.add('show');
                                }
                                // Limpiamos localStorage igualmente
                                localStorage.removeItem('reload_activity_id');
                                localStorage.removeItem('reload_analista_id');
                            }
                        }
                    }
                });

                // --- Expandir y colapsar todos los analistas ---
                document.getElementById('expand-all-btn').addEventListener('click', function() {
                    document.querySelectorAll('.card .collapse').forEach(function(collapseDiv) {
                        // Abre el acordeón si no está abierto
                        if (!collapseDiv.classList.contains('show')) {
                            collapseDiv.classList.add('show');
                        }
                        // Cargar actividades si no están cargadas
                        const analistaId = collapseDiv.id.replace('collapse-', '');
                        if (!collapseDiv.classList.contains('loaded')) {
                            loadActivities(analistaId);
                            collapseDiv.classList.add('loaded');
                        }
                    });
                });

                document.getElementById('collapse-all-btn').addEventListener('click', function() {
                    document.querySelectorAll('.card .collapse').forEach(function(collapseDiv) {
                        if (collapseDiv.classList.contains('show')) {
                            $(collapseDiv).collapse('hide');
                        }
                    });
                });
            });
        </script>
    </div>
@endsection

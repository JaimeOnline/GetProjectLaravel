@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Actividades por Analista</h1>

        <div id="sticky-filter"
            style="position:sticky;top:0;z-index:20;background:#fff;padding-top:1rem;padding-bottom:0.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <form method="GET" class="mb-0" id="status-filter-form" autocomplete="off" onsubmit="return false;">
                <div class="row align-items-end">
                    <div class="col-md-9">
                        <label class="font-weight-bold mb-1 d-block">Filtrar por Estado:</label>
                        <div class="btn-group btn-group-toggle flex-wrap" data-toggle="buttons" id="status-btn-group">
                            @foreach ($statuses as $status)
                                <label class="btn btn-outline-primary mb-2 mr-2"
                                    style="min-width: 120px; font-weight: 500;">
                                    <input type="checkbox" name="status[]" value="{{ $status->id }}" autocomplete="off">
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
                        <a href="{{ route('activities.analistas') }}" class="btn btn-secondary mb-2 ml-2">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if (!append) {
                                tableDiv.innerHTML = data.html;
                            } else {
                                tableDiv.querySelector('tbody').insertAdjacentHTML('beforeend', data.html
                                    .replace(
                                        /^[\s\S]*<tbody>|<\/tbody>[\s\S]*$/g, ''));
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

                            // --- Activar recarga individual tras editar ---
                            setTimeout(function() {
                                document.querySelectorAll('.edit-activity-link').forEach(function(
                                    link) {
                                    link.addEventListener('click', function(e) {
                                        // Marcar el botón de recarga como visible para esta actividad
                                        const btn = this.closest('td').querySelector(
                                            '.reload-activity-btn');
                                        if (btn) {
                                            btn.style.display = 'inline-block';
                                            // Guardar el ID de la actividad en localStorage para saber cuál recargar
                                            localStorage.setItem('reload_activity_id',
                                                btn
                                                .dataset.activityId);
                                            localStorage.setItem('reload_analista_id',
                                                analistaId);
                                        }
                                    });
                                });
                                document.querySelectorAll('.reload-activity-btn').forEach(function(
                                    btn) {
                                    btn.addEventListener('click', function() {
                                        // Recargar solo la fila de la actividad editada
                                        const activityId = this.dataset.activityId;
                                        const analistaId = analistaIdFromBtn(this);
                                        reloadSingleActivity(analistaId, activityId,
                                            this);
                                    });
                                });
                            }, 200);
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
                            <div class="table-responsive" id="activities-table-{{ $analista->id }}">
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
                function loadActivities(analistaId, page = 1, append = false) {
                    const tableDiv = document.getElementById('activities-table-' + analistaId);
                    const loadMoreDiv = document.getElementById('load-more-' + analistaId);
                    const statusFilter = Array.from(document.querySelectorAll('input[name="status[]"]:checked')).map(
                        cb => cb.value);

                    if (!append) {
                        tableDiv.innerHTML = '<div class="text-center text-muted py-3">Cargando actividades...</div>';
                        loadMoreDiv.innerHTML = '';
                    } else {
                        loadMoreDiv.innerHTML = '<div class="text-center text-muted py-2">Cargando más...</div>';
                    }
                    let url = "{{ url('/activities/analistas') }}/" + analistaId + "/actividades";
                    const params = [];
                    params.push("page=" + page);
                    if (statusFilter.length) {
                        statusFilter.forEach(s => params.push("status[]=" + encodeURIComponent(s)));
                    }
                    if (params.length) {
                        url += "?" + params.join("&");
                    }

                    fetch(url)
                        .then(res => {
                            if (!res.ok) throw new Error('No response');
                            return res.json();
                        })
                        .then(data => {
                            if (!append) {
                                tableDiv.innerHTML = data.html;
                            } else {
                                tableDiv.querySelector('tbody').insertAdjacentHTML('beforeend', data.html.replace(
                                    /^[\s\S]*<tbody>|<\/tbody>[\s\S]*$/g, ''));
                            }
                            // Actualizar badge de cantidad
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const count = (tempDiv.querySelectorAll('tbody tr').length) || 0;
                            document.getElementById('badge-count-' + analistaId).textContent = count;

                            if (data.next_page) {
                                loadMoreDiv.innerHTML = '<button class="btn btn-link btn-sm" data-next-page="' + (
                                    page + 1) + '">Cargar más</button>';
                                loadMoreDiv.querySelector('button').onclick = function() {
                                    loadActivities(analistaId, page + 1, true);
                                };
                            } else {
                                loadMoreDiv.innerHTML = '';
                            }

                            // --- Activar recarga individual tras editar ---
                            setTimeout(function() {
                                document.querySelectorAll('.edit-activity-link').forEach(function(link) {
                                    link.addEventListener('click', function(e) {
                                        // Marcar el botón de recarga como visible para esta actividad
                                        const btn = this.closest('td').querySelector(
                                            '.reload-activity-btn');
                                        if (btn) {
                                            btn.style.display = 'inline-block';
                                            // Guardar el ID de la actividad en localStorage para saber cuál recargar
                                            localStorage.setItem('reload_activity_id', btn
                                                .dataset.activityId);
                                            localStorage.setItem('reload_analista_id',
                                                analistaId);
                                        }
                                    });
                                });
                                document.querySelectorAll('.reload-activity-btn').forEach(function(btn) {
                                    btn.addEventListener('click', function() {
                                        // Recargar solo la fila de la actividad editada
                                        const activityId = this.dataset.activityId;
                                        const analistaId = analistaIdFromBtn(this);
                                        reloadSingleActivity(analistaId, activityId, this);
                                    });
                                });
                            }, 200);
                        })
                        .catch(function() {
                            tableDiv.innerHTML =
                                '<div class="text-danger py-3">No se pudo cargar la información. Intenta recargar la página.</div>';
                        });
                }

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

                // Al cambiar el filtro, recarga solo los acordeones abiertos y los mantiene abiertos
                document.querySelectorAll('input[name="status[]"]').forEach(function(cb) {
                    cb.addEventListener('change', function() {
                        // Guardar los IDs de los acordeones abiertos
                        const openAnalistas = Array.from(document.querySelectorAll('.collapse.show'))
                            .map(div =>
                                div.id.replace('collapse-', '')
                            );
                        // Recargar solo los abiertos y mantenerlos abiertos
                        openAnalistas.forEach(function(analistaId) {
                            const collapseDiv = document.getElementById('collapse-' +
                                analistaId);
                            if (collapseDiv) {
                                collapseDiv.classList.remove('loaded');
                                loadActivities(analistaId);
                                // Asegurarse de que permanezca abierto
                                collapseDiv.classList.add('show');
                            }
                        });
                    });
                });

                // --- Al volver de editar, mostrar botón de recarga ---
                window.addEventListener('focus', function() {
                    const activityId = localStorage.getItem('reload_activity_id');
                    const analistaId = localStorage.getItem('reload_analista_id');
                    if (activityId && analistaId) {
                        // Mostrar el botón de recarga solo para esa actividad
                        const tableDiv = document.getElementById('activities-table-' + analistaId);
                        if (tableDiv) {
                            const btn = tableDiv.querySelector('.reload-activity-btn[data-activity-id="' +
                                activityId + '"]');
                            if (btn) btn.style.display = 'inline-block';
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

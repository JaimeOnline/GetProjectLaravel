/**
 * Script para manejar ordenamiento y filtros avanzados en la vista de actividades
 */
document.addEventListener('DOMContentLoaded', function () {
    // --- Toggle de filtros avanzados ---
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filtersSection = document.getElementById('filtersSection');
    const filterToggleText = document.getElementById('filterToggleText');

    if (toggleFiltersBtn && filtersSection && filterToggleText) {
        toggleFiltersBtn.addEventListener('click', function () {
            if (filtersSection.style.display === 'none' || filtersSection.style.display === '') {
                filtersSection.style.display = 'block';
                filterToggleText.textContent = 'Ocultar Filtros';
            } else {
                filtersSection.style.display = 'none';
                filterToggleText.textContent = 'Mostrar Filtros';
            }
        });
    }
    // Variables globales
    let activeFilters = {
        status: [],
        analistas: [],
        fechaDesde: null,
        fechaHasta: null
    };

    // Formatear etiquetas de estado al cargar
    formatStatusLabels();

    // Inicializar
    setupSortHandlersForAllTables();
    setupColumnFilters();

    // Solo ejecutar la expansión/colapso de subactividades en el index, NO en el edit
    const isEditPage = document.getElementById('edit-activity-page');
    [
        { btnId: 'toggleAllSubactivitiesBtn', iconId: 'toggleAllSubactivitiesIcon', tableSelector: '#tableContainer', onlyIndex: false },
        { btnId: 'toggleAllSubactivitiesBtnEdit', iconId: 'toggleAllSubactivitiesIconEdit', tableSelector: '#subactivitiesTableContainer', onlyIndex: true }
    ].forEach(cfg => {
        // Si es el botón de edición y estamos en la página de edición, NO ejecutar aquí (lo hace el otro JS)
        if (cfg.onlyIndex && isEditPage) return;

        const toggleAllBtn = document.getElementById(cfg.btnId);
        const toggleAllIcon = document.getElementById(cfg.iconId);
        let allExpanded = false;

        if (toggleAllBtn) {
            toggleAllBtn.addEventListener('click', function () {
                const tableBody = document.querySelector(`${cfg.tableSelector} tbody`);
                if (!tableBody) return;

                // Todas las filas de subactividad
                const subRows = tableBody.querySelectorAll('tr.subactivity-row');
                // Todos los toggles de actividades padre
                const toggles = tableBody.querySelectorAll('.toggle-subactivities');

                if (!allExpanded) {
                    // Expandir todas
                    subRows.forEach(row => row.style.display = 'table-row');
                    toggles.forEach(toggle => {
                        toggle.classList.add('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-right');
                            icon.classList.add('fa-chevron-down');
                        }
                    });
                    if (toggleAllIcon) {
                        toggleAllIcon.classList.remove('fa-chevron-down');
                        toggleAllIcon.classList.add('fa-chevron-up');
                    }
                    allExpanded = true;
                } else {
                    // Colapsar todas
                    subRows.forEach(row => row.style.display = 'none');
                    toggles.forEach(toggle => {
                        toggle.classList.remove('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-right');
                        }
                    });
                    if (toggleAllIcon) {
                        toggleAllIcon.classList.remove('fa-chevron-up');
                        toggleAllIcon.classList.add('fa-chevron-down');
                    }
                    allExpanded = false;
                }
            });
        }

        // Toggle subactividades usando event delegation sobre el tbody
        const tableBody = document.querySelector(`${cfg.tableSelector} tbody`);
        if (tableBody) {
            tableBody.addEventListener('click', function (e) {
                // Asegura que el click fue en el toggle o en su icono
                let btn = e.target;
                if (!btn.classList.contains('toggle-subactivities')) {
                    btn = btn.closest('.toggle-subactivities');
                }
                if (!btn) return;

                // El id de la actividad está en el data-activity-id del span
                const parentId = btn.getAttribute('data-activity-id');
                if (!parentId) {
                    return;
                }
                const icon = btn.querySelector('i');
                const subRows = tableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`);

                const isExpanded = btn.classList.contains('expanded');
                if (!isExpanded) {
                    btn.classList.add('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    }
                    // Mostrar subactividades directas
                    subRows.forEach(row => {
                        row.style.display = 'table-row';
                    });
                } else {
                    btn.classList.remove('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                    // Ocultar subactividades directas y sus descendientes recursivamente
                    function hideSubtree(parentId) {
                        tableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`).forEach(row => {
                            row.style.display = 'none';
                            // Si la subactividad tiene su propio toggle expandido, colapsar también
                            const subBtn = row.querySelector('.toggle-subactivities.expanded');
                            if (subBtn) {
                                subBtn.classList.remove('expanded');
                                const subIcon = subBtn.querySelector('i');
                                if (subIcon) {
                                    subIcon.classList.remove('fa-chevron-down');
                                    subIcon.classList.add('fa-chevron-right');
                                }
                            }
                            hideSubtree(row.getAttribute('data-activity-id'));
                        });
                    }
                    hideSubtree(parentId);
                }
            });
        }
    });

    /**
     * Formatear todas las etiquetas de estado para mostrar nombres legibles
     */
    function formatStatusLabels() {
        document.querySelectorAll('.status-filter').forEach(updateStatusCheckboxLabel);
    }

    /**
    * Configurar manejadores de ordenamiento para ambas tablas
    */
    function setupSortHandlersForAllTables() {
        [
            { container: '#tableContainer', rowSelector: 'tr.parent-activity' },
            { container: '#subactivitiesTableContainer', rowSelector: 'tr.subactivity-row' }
        ].forEach(cfg => {
            const container = document.querySelector(cfg.container);
            if (!container) return;
            const table = container.querySelector('table');
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            let currentSort = { column: null, direction: null };
            // Guardar el orden original de las filas raíz y sus subárboles
            let originalOrder = [];
            (function saveOriginalOrder() {
                // Solo filas raíz
                const rootRows = Array.from(tbody.querySelectorAll('tr.activity-row:not(.subactivity-row), tr.subactivity-row[level="0"], tr.subactivity-row:not([data-parent-id])'));
                // Para cada raíz, guarda la rama completa (raíz + subárbol)
                rootRows.forEach(row => {
                    const branch = [row];
                    function collectSubtree(parentId) {
                        const children = Array.from(tbody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`));
                        children.forEach(child => {
                            branch.push(child);
                            collectSubtree(child.getAttribute('data-activity-id'));
                        });
                    }
                    collectSubtree(row.getAttribute('data-activity-id'));
                    originalOrder.push(branch);
                });
            })();

            table.querySelectorAll('.sortable').forEach(header => {
                let clickCount = 0;
                header.addEventListener('click', function () {
                    const column = header.getAttribute('data-sort');
                    if (currentSort.column === column) {
                        if (currentSort.direction === 'asc') {
                            currentSort.direction = 'desc';
                        } else if (currentSort.direction === 'desc') {
                            currentSort.direction = null; // Tercer click: restaurar original
                        } else {
                            currentSort.direction = 'asc';
                        }
                    } else {
                        currentSort = { column: column, direction: 'asc' };
                    }

                    if (currentSort.direction === null) {
                        // Restaurar orden original
                        restoreOriginalOrder(tbody, originalOrder);
                        updateSortIcons(table, null, null);
                        window.lastSortConfig = null;
                    } else {
                        sortTable(tbody, column, currentSort.direction, cfg.rowSelector);
                        updateSortIcons(table, column, currentSort.direction);
                    }
                });
            });

            function restoreOriginalOrder(tbody, originalOrder) {
                // Limpia el tbody y re-inserta las ramas en el orden original
                originalOrder.forEach(branch => {
                    branch.forEach(row => {
                        tbody.appendChild(row);
                    });
                });
            }

            function sortTable(tbody, column, direction, rowSelector) {
                // Guardar el último sort globalmente
                window.lastSortConfig = {
                    table: tbody.closest('table'),
                    column,
                    direction,
                    rowSelector
                };

                // Solo ordenar las filas de nivel raíz (sin data-parent-id o parent-id vacío)
                const rootRows = Array.from(tbody.querySelectorAll('tr.activity-row:not(.subactivity-row), tr.subactivity-row[level="0"], tr.subactivity-row:not([data-parent-id])'));

                rootRows.sort((a, b) => {
                    let aValue = getSortValue(a, column);
                    let bValue = getSortValue(b, column);

                    // Ordenar por fecha
                    if (column === 'fecha_recepcion') {
                        if (!aValue && !bValue) return 0;
                        if (!aValue) return 1;
                        if (!bValue) return -1;
                        const aParts = aValue.split('/');
                        const bParts = bValue.split('/');
                        const aDate = aParts.length === 3 ? new Date(aParts[2], aParts[1] - 1, aParts[0]) : null;
                        const bDate = bParts.length === 3 ? new Date(bParts[2], bParts[1] - 1, bParts[0]) : null;
                        if (!aDate || isNaN(aDate)) return 1;
                        if (!bDate || isNaN(bDate)) return -1;
                        if (aDate < bDate) return direction === 'asc' ? -1 : 1;
                        if (aDate > bDate) return direction === 'asc' ? 1 : -1;
                        return 0;
                    }

                    // Ordenar por número si ambos son numéricos
                    const aNum = parseFloat(aValue);
                    const bNum = parseFloat(bValue);
                    if (!isNaN(aNum) && !isNaN(bNum)) {
                        if (aNum < bNum) return direction === 'asc' ? -1 : 1;
                        if (aNum > bNum) return direction === 'asc' ? 1 : -1;
                        return 0;
                    }

                    // Ordenar por texto
                    if (aValue < bValue) return direction === 'asc' ? -1 : 1;
                    if (aValue > bValue) return direction === 'asc' ? 1 : -1;
                    return 0;
                });

                // Función recursiva para agregar una rama completa de subactividades
                function appendSubtree(parentId) {
                    const children = Array.from(tbody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`));
                    children.forEach(child => {
                        tbody.appendChild(child);
                        appendSubtree(child.getAttribute('data-activity-id'));
                    });
                }

                // Limpiar el tbody y reinsertar en orden correcto
                rootRows.forEach(row => {
                    tbody.appendChild(row);
                    const activityId = row.getAttribute('data-activity-id');
                    appendSubtree(activityId);
                });
            }

            // Exponer la función globalmente para applyFilters
            window.globalSortTable = sortTable;


            function updateSortIcons(table, activeColumn, direction) {
                table.querySelectorAll('.sortable').forEach(header => {
                    const column = header.getAttribute('data-sort');
                    const icon = header.querySelector('.sort-icon');
                    if (!icon) return;
                    if (column === activeColumn) {
                        icon.className = `fas fa-sort-${direction === 'asc' ? 'up' : 'down'} text-primary ml-1`;
                    } else {
                        icon.className = 'fas fa-sort text-muted ml-1';
                    }
                });
            }
        });
    }


    /**
     * Obtener valor para ordenamiento
     */
    function getSortValue(row, column) {
        // Mapear el nombre de columna a índice fijo según tu tabla
        // Orden real: caso(0), nombre(1), prioridad(2), orden(3), descripcion(4), status(5), analistas(6), requerimientos(7), fecha(8)
        const COLUMN_INDEX = {
            'caso': 0,
            'nombre': 1,
            'prioridad': 2,
            'orden_analista': 3,
            'cliente': 4,
            'estatus_operacional': 5,
            'porcentaje_avance': 6,
            'descripcion': 7,
            'status': 8,
            'analistas': 9,
            'requerimientos': 10,
            'fecha_recepcion': 11
        };
        const cells = row.querySelectorAll('td');
        let value = '';

        if (column === 'prioridad') {
            const cell = cells[COLUMN_INDEX['prioridad']];
            return cell ? parseInt(cell.getAttribute('data-sort-value') || '0', 10) : 0;
        }
        if (column === 'orden_analista') {
            const cell = cells[COLUMN_INDEX['orden_analista']];
            return cell ? parseInt(cell.getAttribute('data-sort-value') || '0', 10) : 0;
        }

        switch (column) {
            case 'caso':
                value = cells[COLUMN_INDEX['caso']]?.textContent?.trim() || '';
                break;
            case 'nombre':
                value = cells[COLUMN_INDEX['nombre']]?.textContent?.trim() || '';
                break;
            case 'descripcion':
                value = cells[COLUMN_INDEX['descripcion']]?.textContent?.trim() || '';
                break;
            case 'status':
                value = cells[COLUMN_INDEX['status']]?.textContent?.trim() || '';
                break;
            case 'analistas':
                value = cells[COLUMN_INDEX['analistas']]?.textContent?.trim() || '';
                break;
            case 'requerimientos':
                value = cells[COLUMN_INDEX['requerimientos']]?.textContent?.trim() || '';
                break;
            case 'fecha_recepcion':
                // Extraer solo la fecha del formato "DD/MM/YYYY"
                const dateText = cells[COLUMN_INDEX['fecha_recepcion']]?.textContent?.trim() || '';
                const dateMatch = dateText.match(/\d{2}\/\d{2}\/\d{4}/);
                value = dateMatch ? dateMatch[0] : '';
                return value;
            default:
                value = '';
        }

        return value.toLowerCase();
    }




    /**
     * Configurar filtros de columna
     */
    function setupColumnFilters() {
        // Configurar botones de filtro
        const filterButtons = document.querySelectorAll('.filter-toggle');
        filterButtons.forEach(button => {
            // Remover event listeners existentes
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);

            // Agregar nuevo event listener
            newButton.addEventListener('click', function (e) {
                e.stopPropagation();
                const filterType = this.getAttribute('data-filter');
                toggleFilterMenu(filterType);
            });
        });

        // --- PRIORIDAD ---
        document.querySelectorAll('.prioridad-filter').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.value === '') {
                    // "Todas" seleccionada: deselecciona las demás
                    if (this.checked) {
                        document.querySelectorAll('.prioridad-filter').forEach(other => {
                            if (other.value !== '') other.checked = false;
                        });
                    }
                } else {
                    // Si se selecciona una prioridad, deselecciona "Todas"
                    document.querySelectorAll('.prioridad-filter[value=""]').forEach(allCb => {
                        allCb.checked = false;
                    });
                }
                // Si ninguna está seleccionada, selecciona "Todas"
                const anyChecked = Array.from(document.querySelectorAll('.prioridad-filter')).some(cb => cb.checked && cb.value !== '');
                if (!anyChecked) {
                    document.querySelectorAll('.prioridad-filter[value=""]').forEach(allCb => {
                        allCb.checked = true;
                    });
                }
                applyFilters();
                updateFilterIndicators();
            });
        });
        // Al cargar, selecciona "Todas" si ninguna está seleccionada
        const prioridadChecks = Array.from(document.querySelectorAll('.prioridad-filter'));
        if (!prioridadChecks.some(cb => cb.checked)) {
            document.querySelectorAll('.prioridad-filter[value=""]').forEach(allCb => {
                allCb.checked = true;
            });
        }
        // Forzar "Todos" en orden
        const ordenChecks = Array.from(document.querySelectorAll('.orden-filter'));
        if (!ordenChecks.some(cb => cb.checked)) {
            document.querySelectorAll('.orden-filter[value=""]').forEach(allCb => {
                allCb.checked = true;
            });
        }
        // Mostrar/ocultar botón limpiar filtros al cargar
        updateFilterIndicators();

        // --- ORDEN ---
        document.querySelectorAll('.orden-filter').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.value === '') {
                    // "Todos" seleccionada: deselecciona las demás
                    if (this.checked) {
                        document.querySelectorAll('.orden-filter').forEach(other => {
                            if (other.value !== '') other.checked = false;
                        });
                    }
                } else {
                    // Si se selecciona un orden, deselecciona "Todos"
                    document.querySelectorAll('.orden-filter[value=""]').forEach(allCb => {
                        allCb.checked = false;
                    });
                }
                // Si ninguna está seleccionada, selecciona "Todos"
                const anyChecked = Array.from(document.querySelectorAll('.orden-filter')).some(cb => cb.checked && cb.value !== '');
                if (!anyChecked) {
                    document.querySelectorAll('.orden-filter[value=""]').forEach(allCb => {
                        allCb.checked = true;
                    });
                }
                applyFilters();
                updateFilterIndicators();
            });
        });

        // Al cargar, selecciona "Todos" si ninguna está seleccionada
        if (!Array.from(document.querySelectorAll('.orden-filter')).some(cb => cb.checked)) {
            document.querySelectorAll('.orden-filter[value=""]').forEach(allCb => {
                allCb.checked = true;
            });
        }

        // Configurar checkboxes de estado
        document.querySelectorAll('.status-filter').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                handleStatusChange(this);
            });
        });

        // Configurar checkboxes de analista
        document.querySelectorAll('.analista-filter').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                handleAnalistaChange(this);
            });
        });

        // Configurar filtros de fecha
        setupDateFilters();

        // Configurar botón para limpiar todos los filtros
        const clearButton = document.getElementById('clearAllColumnFilters');
        if (clearButton) {
            clearButton.addEventListener('click', clearAllFilters);
        }

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.custom-dropdown') && !e.target.closest('.custom-dropdown-menu')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });
    }


    /**
     * Mostrar/ocultar menú de filtro
     */
    function toggleFilterMenu(filterType) {
        // Soportar "orden_analista" como id
        const menu = document.getElementById(`${filterType}-filter-menu`);

        if (menu) {
            // Cerrar otros dropdowns
            document.querySelectorAll('.custom-dropdown-menu').forEach(otherMenu => {
                if (otherMenu.id !== `${filterType}-filter-menu`) {
                    otherMenu.style.display = 'none';
                }
            });

            // Toggle actual
            const isVisible = menu.style.display === 'block';
            menu.style.display = isVisible ? 'none' : 'block';
        }
    }

    /**
     * Manejar cambio en filtro de estado
     */
    function handleStatusChange(checkbox) {
        // Actualizar etiqueta del checkbox para mostrar nombre legible
        updateStatusCheckboxLabel(checkbox);

        if (checkbox.value === '') {
            // Checkbox "Todos"
            if (checkbox.checked) {
                activeFilters.status = [];
                document.querySelectorAll('.status-filter').forEach(cb => {
                    if (cb.value !== '' && cb.id !== checkbox.id) cb.checked = false;
                });
            }
        } else {
            // Checkbox específico
            document.querySelectorAll('#status-all, #status-all-search').forEach(allCheckbox => {
                if (allCheckbox) allCheckbox.checked = false;
            });

            if (checkbox.checked) {
                if (!activeFilters.status.includes(checkbox.value)) {
                    activeFilters.status.push(checkbox.value);
                }
            } else {
                activeFilters.status = activeFilters.status.filter(s => s !== checkbox.value);
            }

            // Si no hay ningún filtro seleccionado, marcar "Todos"
            if (activeFilters.status.length === 0) {
                document.querySelectorAll('#status-all, #status-all-search').forEach(allCheckbox => {
                    if (allCheckbox) allCheckbox.checked = true;
                });
            }
        }

        // Actualizar todas las etiquetas de estado
        document.querySelectorAll('.status-filter').forEach(updateStatusCheckboxLabel);

        // Sincronizar checkboxes entre diferentes menús
        syncStatusCheckboxes();

        // Aplicar filtros
        applyFilters();
        updateFilterIndicators();
    }

    /**
     * Actualizar etiqueta de checkbox de estado para mostrar nombre legible
     */
    function updateStatusCheckboxLabel(checkbox) {
        if (checkbox.value === '') return;

        const label = checkbox.nextElementSibling;
        if (!label || !label.classList.contains('form-check-label')) return;

        // Si la etiqueta ya tiene el texto formateado, no hacer nada
        if (label.getAttribute('data-formatted') === 'true') return;

        // Mapeo de códigos a nombres legibles
        const statusMap = {
            'no_iniciada': 'No Iniciada',
            'en_ejecucion': 'En Ejecución',
            'en_espera_de_insumos': 'En Espera de Insumos',
            'en_certificacion_por_cliente': 'En Certificación',
            'pases_enviados': 'Pases Enviados',
            'culminada': 'Culminada',
            'pausada': 'Pausada',
            'reiterar': 'Reiterar',
            'atendiendo_hoy': 'Atendiendo hoy'
        };

        // Actualizar texto de la etiqueta si existe en el mapeo
        if (statusMap[checkbox.value]) {
            label.textContent = statusMap[checkbox.value];
            label.setAttribute('data-formatted', 'true');
        }
    }



    /**
     * Sincronizar checkboxes de estado entre diferentes menús
     */
    function syncStatusCheckboxes() {
        // Sincronizar "Todos"
        const statusAll = document.querySelector('#status-all');
        const allChecked = statusAll ? statusAll.checked : false;
        document.querySelectorAll('#status-all, #status-all-search').forEach(cb => {
            if (cb) cb.checked = allChecked;
        });

        // Sincronizar checkboxes específicos
        document.querySelectorAll('.status-filter').forEach(cb => {
            if (cb.value !== '') {
                const isChecked = activeFilters.status.includes(cb.value);
                document.querySelectorAll(`.status-filter[value="${cb.value}"]`).forEach(relatedCb => {
                    relatedCb.checked = isChecked;
                });
            }
        });
    }


    /**
     * Manejar cambio en filtro de analista
     */
    function handleAnalistaChange(checkbox) {
        if (checkbox.value === '') {
            // Checkbox "Todos"
            if (checkbox.checked) {
                activeFilters.analistas = [];
                document.querySelectorAll('.analista-filter').forEach(cb => {
                    if (cb.value !== '' && cb.id !== checkbox.id) cb.checked = false;
                });
            }
        } else {
            // Checkbox específico
            document.querySelectorAll('#analista-all, #analista-all-search').forEach(allCheckbox => {
                if (allCheckbox) allCheckbox.checked = false;
            });

            if (checkbox.checked) {
                if (!activeFilters.analistas.includes(checkbox.value)) {
                    activeFilters.analistas.push(checkbox.value);
                }
            } else {
                activeFilters.analistas = activeFilters.analistas.filter(a => a !== checkbox.value);
            }

            // Si no hay ningún filtro seleccionado, marcar "Todos"
            if (activeFilters.analistas.length === 0) {
                document.querySelectorAll('#analista-all, #analista-all-search').forEach(allCheckbox => {
                    if (allCheckbox) allCheckbox.checked = true;
                });
            }
        }

        // Sincronizar checkboxes entre diferentes menús
        syncAnalistaCheckboxes();

        // Aplicar filtros
        applyFilters();
        updateFilterIndicators();
    }

    /**
     * Sincronizar checkboxes de analista entre diferentes menús
     */
    function syncAnalistaCheckboxes() {
        // Sincronizar "Todos"
        const allChecked = document.querySelector('#analista-all')?.checked || false;
        document.querySelectorAll('#analista-all, #analista-all-search').forEach(cb => {
            if (cb) cb.checked = allChecked;
        });

        // Sincronizar checkboxes específicos
        document.querySelectorAll('.analista-filter').forEach(cb => {
            if (cb.value !== '') {
                const isChecked = activeFilters.analistas.includes(cb.value);
                document.querySelectorAll(`.analista-filter[value="${cb.value}"]`).forEach(relatedCb => {
                    relatedCb.checked = isChecked;
                });
            }
        });
    }

    /**
     * Configurar filtros de fecha
     */
    function setupDateFilters() {
        // Configurar filtrado para campos de fecha
        const fechaDesdeFilter = document.getElementById('fecha-desde-filter');
        const fechaHastaFilter = document.getElementById('fecha-hasta-filter');

        if (fechaDesdeFilter) {
            fechaDesdeFilter.addEventListener('change', function () {
                // Auto-completar fecha hasta si está vacía
                if (this.value && fechaHastaFilter && !fechaHastaFilter.value) {
                    fechaHastaFilter.value = this.value;
                }

                activeFilters.fechaDesde = this.value || null;
                syncDateFilters('desde', this.value);
                applyFilters();
                updateFilterIndicators();
            });
        }

        if (fechaHastaFilter) {
            fechaHastaFilter.addEventListener('change', function () {
                activeFilters.fechaHasta = this.value || null;
                syncDateFilters('hasta', this.value);
                applyFilters();
                updateFilterIndicators();
            });
        }

        // Configurar botones de aplicar/limpiar filtro de fecha
        const applyDateFilterBtn = document.getElementById('apply-date-filter');
        const clearDateFilterBtn = document.getElementById('clear-date-filter');

        if (applyDateFilterBtn) {
            applyDateFilterBtn.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('fecha-filter-menu').style.display = 'none';
            });
        }

        if (clearDateFilterBtn) {
            clearDateFilterBtn.addEventListener('click', function (e) {
                e.preventDefault();

                // Limpiar campos
                document.querySelectorAll('#fecha-desde-filter, #fecha-desde-filter-search, #filterFechaDesde').forEach(input => {
                    if (input) input.value = '';
                });

                document.querySelectorAll('#fecha-hasta-filter, #fecha-hasta-filter-search, #filterFechaHasta').forEach(input => {
                    if (input) input.value = '';
                });

                // Limpiar filtros activos
                activeFilters.fechaDesde = null;
                activeFilters.fechaHasta = null;

                // Aplicar filtros
                applyFilters();
                updateFilterIndicators();
            });
        }
    }

    /**
     * Sincronizar filtros de fecha entre diferentes menús
     */
    function syncDateFilters(type, value) {
        if (type === 'desde') {
            document.querySelectorAll('#fecha-desde-filter, #fecha-desde-filter-search, #filterFechaDesde').forEach(input => {
                if (input) input.value = value;
            });
        } else if (type === 'hasta') {
            document.querySelectorAll('#fecha-hasta-filter, #fecha-hasta-filter-search, #filterFechaHasta').forEach(input => {
                if (input) input.value = value;
            });
        }
    }

    /**
         * Aplicar todos los filtros activos en ambas tablas
         */
    function applyFilters() {
        [
            { container: '#tableContainer', rowSelector: 'tr.activity-row, tr.subactivity-row', count: true },
            { container: '#subactivitiesTableContainer', rowSelector: 'tr.activity-row, tr.subactivity-row', count: false }
        ].forEach(cfg => {
            const tableBody = document.querySelector(`${cfg.container} tbody`);
            if (!tableBody) return;
            const rows = tableBody.querySelectorAll(cfg.rowSelector);
            let visibleCount = 0;

            rows.forEach(row => {
                let shouldShow = true;
                const cells = row.querySelectorAll('td');

                // Filtro por estado (columna 8)
                if (activeFilters.status.length > 0 && cells[8]) {
                    // Extraer todos los textos de los badges dentro de la celda de estado
                    const badgeTexts = Array.from(cells[8].querySelectorAll('.badge'))
                        .map(badge => badge.textContent.trim().toLowerCase());

                    const statusMatch = activeFilters.status.some(status => {
                        switch (status) {
                            case 'no_iniciada':
                                return badgeTexts.some(text => text.includes('no iniciada'));
                            case 'en_ejecucion':
                                return badgeTexts.some(text => text.includes('en ejecución') || text.includes('ejecutando'));
                            case 'en_espera_de_insumos':
                                return badgeTexts.some(text => text.includes('en espera') || text.includes('insumos'));
                            case 'en_certificacion_por_cliente':
                                return badgeTexts.some(text => text.includes('certificación') || text.includes('certificando'));
                            case 'pases_enviados':
                                return badgeTexts.some(text => text.includes('pases enviados'));
                            case 'culminada':
                                return badgeTexts.some(text => text.includes('culminada') || text.includes('completada'));
                            case 'pausada':
                                return badgeTexts.some(text => text.includes('pausada'));
                            default:
                                return badgeTexts.some(text => text.includes(status.replace(/_/g, ' ').toLowerCase()));
                        }
                    });

                    if (!statusMatch) shouldShow = false;
                }

                // Filtro por analista (columna 9)
                if (shouldShow && activeFilters.analistas.length > 0 && cells[9]) {
                    const analistaText = cells[9].textContent.trim().toLowerCase();
                    const analistaMatch = activeFilters.analistas.some(analistaId => {
                        const analistaLabel = document.querySelector(`.analista-filter[value="${analistaId}"] + label`);
                        if (!analistaLabel) return false;
                        const nombre = analistaLabel.textContent.trim().toLowerCase();
                        return analistaText.includes(nombre);
                    });
                    if (!analistaMatch) shouldShow = false;
                }

                // Filtro por prioridad (columna 2)
                const prioridadFilterCheckboxes = document.querySelectorAll('.prioridad-filter:checked');
                const prioridadValues = Array.from(prioridadFilterCheckboxes)
                    .map(cb => cb.value)
                    .filter(val => val !== '');
                if (shouldShow && prioridadValues.length > 0) {
                    // Si "Todas" está seleccionada, no filtrar
                    if (!prioridadFilterCheckboxes[0] || prioridadFilterCheckboxes[0].value !== '') {
                        const prioridadCell = cells[2]; // Prioridad es la columna 2
                        const prioridadValue = prioridadCell ? prioridadCell.getAttribute('data-sort-value') : null;
                        if (!prioridadValues.includes(prioridadValue)) shouldShow = false;
                    }
                }

                // Filtro por orden (columna 3)
                const ordenFilterCheckboxes = document.querySelectorAll('.orden-filter:checked');
                const ordenValues = Array.from(ordenFilterCheckboxes)
                    .map(cb => cb.value)
                    .filter(val => val !== '');
                if (shouldShow && ordenValues.length > 0) {
                    // Si "Todos" está seleccionada, no filtrar
                    if (!ordenFilterCheckboxes[0] || ordenFilterCheckboxes[0].value !== '') {
                        const ordenCell = cells[3]; // Orden es la columna 3
                        const ordenValue = ordenCell ? ordenCell.getAttribute('data-sort-value') : null;
                        if (!ordenValues.includes(ordenValue)) shouldShow = false;
                    }
                }

                // Filtro por fecha (columna 11)
                if (shouldShow && (activeFilters.fechaDesde || activeFilters.fechaHasta) && cells[11]) {
                    const fechaText = cells[11].textContent.trim();
                    const fechaMatch = fechaText.match(/(\d{2})\/(\d{2})\/(\d{4})/);

                    if (fechaMatch) {
                        const day = fechaMatch[1];
                        const month = fechaMatch[2];
                        const year = fechaMatch[3];
                        const fechaActividad = new Date(`${year}-${month}-${day}`);

                        if (activeFilters.fechaDesde) {
                            const fechaDesde = new Date(activeFilters.fechaDesde);
                            if (fechaActividad < fechaDesde) shouldShow = false;
                        }

                        if (shouldShow && activeFilters.fechaHasta) {
                            const fechaHasta = new Date(activeFilters.fechaHasta);
                            if (fechaActividad > fechaHasta) shouldShow = false;
                        }
                    } else if (activeFilters.fechaDesde || activeFilters.fechaHasta) {
                        shouldShow = false;
                    }
                }

                // Aplicar visibilidad
                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) visibleCount++;
            });

            // Al aplicar filtros, fuerza la expansión visual de todas las subactividades visibles
            const allSubRows = document.querySelectorAll('#tableContainer tbody tr.subactivity-row');
            allSubRows.forEach(subRow => {
                if (subRow.style.display !== 'none') {
                    subRow.style.display = 'table-row';
                }
            });

            // Solo actualizar el contador en la tabla principal
            if (cfg.count) {
                updateResultsCount(visibleCount);
            }
        });

        // Reaplicar el último sort si existe
        if (window.lastSortConfig) {
            const { table, column, direction, rowSelector } = window.lastSortConfig;
            const tbody = table.querySelector('tbody');
            if (tbody) {
                // Llama a la función sortTable definida en setupSortHandlersForAllTables
                // Debes exponerla en window para poder llamarla aquí
                if (typeof window.globalSortTable === 'function') {
                    window.globalSortTable(tbody, column, direction, rowSelector);
                }
            }
        }
    }

    /**
     * Actualizar contador de resultados
     */
    function updateResultsCount(visibleCount) {
        // Contar todas las filas (actividades y subactividades)
        const totalRows = document.querySelectorAll('#tableContainer tbody tr.activity-row, #tableContainer tbody tr.subactivity-row').length;

        // Actualizar título de la tabla si existe
        const tableTitle = document.getElementById('tableTitle');
        if (tableTitle) {
            if (visibleCount === totalRows) {
                tableTitle.textContent = 'Lista de Actividades';
            } else {
                tableTitle.textContent = `Actividades filtradas (${visibleCount} de ${totalRows})`;
            }
        }
    }

    /**
     * Actualizar indicadores visuales de filtros activos
     */
    function updateFilterIndicators() {
        // Indicador para filtro de estado
        const statusFilterBtn = document.querySelector('[data-filter="status"]');
        if (statusFilterBtn) {
            if (activeFilters.status.length > 0) {
                statusFilterBtn.classList.add('active', 'btn-primary');
                statusFilterBtn.classList.remove('btn-outline-secondary');
            } else {
                statusFilterBtn.classList.remove('active', 'btn-primary');
                statusFilterBtn.classList.add('btn-outline-secondary');
            }
        }

        // Indicador para filtro de analista
        const analistaFilterBtn = document.querySelector('[data-filter="analistas"]');
        if (analistaFilterBtn) {
            if (activeFilters.analistas.length > 0) {
                analistaFilterBtn.classList.add('active', 'btn-primary');
                analistaFilterBtn.classList.remove('btn-outline-secondary');
            } else {
                analistaFilterBtn.classList.remove('active', 'btn-primary');
                analistaFilterBtn.classList.add('btn-outline-secondary');
            }
        }

        // Indicador para filtro de fecha
        const fechaFilterBtn = document.querySelector('[data-filter="fecha"]');
        if (fechaFilterBtn) {
            if (activeFilters.fechaDesde || activeFilters.fechaHasta) {
                fechaFilterBtn.classList.add('active', 'btn-primary');
                fechaFilterBtn.classList.remove('btn-outline-secondary');
            } else {
                fechaFilterBtn.classList.remove('active', 'btn-primary');
                fechaFilterBtn.classList.add('btn-outline-secondary');
            }
        }

        // Mostrar/ocultar botón de limpiar todos los filtros
        const clearAllBtn = document.getElementById('clearAllColumnFilters');
        if (clearAllBtn) {
            // Filtros de columna
            let hasActiveFilters = activeFilters.status.length > 0 ||
                activeFilters.analistas.length > 0 ||
                activeFilters.fechaDesde ||
                activeFilters.fechaHasta;

            // Filtros de prioridad y orden
            const prioridadChecked = Array.from(document.querySelectorAll('.prioridad-filter')).some(cb => cb.checked && cb.value !== '');
            const ordenChecked = Array.from(document.querySelectorAll('.orden-filter')).some(cb => cb.checked && cb.value !== '');
            if (prioridadChecked || ordenChecked) {
                hasActiveFilters = true;
            }

            // Filtros avanzados
            const estadoSelect = document.getElementById('filterEstado');
            const analistaSelect = document.getElementById('filterAnalista');
            const fechaInput = document.getElementById('filterFecha');
            if (
                (estadoSelect && estadoSelect.value) ||
                (analistaSelect && analistaSelect.value) ||
                (fechaInput && fechaInput.value)
            ) {
                hasActiveFilters = true;
            }

            clearAllBtn.style.display = hasActiveFilters ? 'block' : 'none';
        }
    }


    /**
     * Limpiar todos los filtros
     */
    function clearAllFilters() {
        // Limpiar filtros de estado
        document.querySelectorAll('.status-filter').forEach(cb => {
            cb.checked = cb.value === '';
        });

        // Limpiar filtros de analistas
        document.querySelectorAll('.analista-filter').forEach(cb => {
            cb.checked = cb.value === '';
        });

        // Limpiar filtros de prioridad
        document.querySelectorAll('.prioridad-filter').forEach(cb => {
            cb.checked = cb.value === '';
        });

        // Limpiar filtros de orden
        document.querySelectorAll('.orden-filter').forEach(cb => {
            cb.checked = cb.value === '';
        });

        // Limpiar filtros de fecha
        document.querySelectorAll('#fecha-desde-filter, #fecha-desde-filter-search, #filterFechaDesde').forEach(input => {
            if (input) input.value = '';
        });

        document.querySelectorAll('#fecha-hasta-filter, #fecha-hasta-filter-search, #filterFechaHasta').forEach(input => {
            if (input) input.value = '';
        });

        // Limpiar filtros avanzados
        const estadoSelect = document.getElementById('filterEstado');
        const analistaSelect = document.getElementById('filterAnalista');
        const fechaInput = document.getElementById('filterFecha');
        if (estadoSelect) {
            estadoSelect.value = '';
            estadoSelect.dispatchEvent(new Event('change'));
        }
        if (analistaSelect) {
            analistaSelect.value = '';
            analistaSelect.dispatchEvent(new Event('change'));
        }
        if (fechaInput) {
            fechaInput.value = '';
            fechaInput.dispatchEvent(new Event('change'));
        }

        // Resetear filtros activos
        activeFilters = {
            status: [],
            analistas: [],
            fechaDesde: null,
            fechaHasta: null
        };

        // Aplicar filtros para mostrar todas las filas
        applyFilters();

        // Cerrar todos los dropdowns
        document.querySelectorAll('.custom-dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });

        // Actualizar indicadores visuales
        updateFilterIndicators();
    }

    // --- INICIO: Actualización de indicadores al usar filtros avanzados ---
    const estadoSelect = document.getElementById('filterStatus');
    const analistaSelect = document.getElementById('filterAnalista');
    const fechaDesdeInput = document.getElementById('filterFechaDesde');
    const fechaHastaInput = document.getElementById('filterFechaHasta');

    function filtrarAvanzado() {
        // Estado
        if (estadoSelect && estadoSelect.value) {
            activeFilters.status = [estadoSelect.value];
        } else {
            activeFilters.status = [];
        }
        // Analista
        if (analistaSelect && analistaSelect.value) {
            activeFilters.analistas = [analistaSelect.value];
        } else {
            activeFilters.analistas = [];
        }
        // Fechas
        activeFilters.fechaDesde = (fechaDesdeInput && fechaDesdeInput.value) ? fechaDesdeInput.value : null;
        activeFilters.fechaHasta = (fechaHastaInput && fechaHastaInput.value) ? fechaHastaInput.value : null;

        applyFilters();
        updateFilterIndicators();
    }

    if (estadoSelect) estadoSelect.addEventListener('change', filtrarAvanzado);
    if (analistaSelect) analistaSelect.addEventListener('change', filtrarAvanzado);
    if (fechaDesdeInput) fechaDesdeInput.addEventListener('change', filtrarAvanzado);
    if (fechaHastaInput) fechaHastaInput.addEventListener('change', filtrarAvanzado);
    // --- FIN: Actualización de indicadores al usar filtros avanzados ---

    // --- BÚSQUEDA Y FILTRO AJAX (como en la versión anterior) ---

    const searchInput = document.getElementById('searchInput');
    const searchSpinner = document.getElementById('searchSpinner');
    const searchResultsCount = document.getElementById('searchResultsCount');
    const resultsNumber = document.getElementById('resultsNumber');
    const searchResultsAlert = document.getElementById('searchResultsAlert');
    const searchResultsText = document.getElementById('searchResultsText');
    const tableTitle = document.getElementById('tableTitle');

    let searchTimeout;
    let isSearchActive = false;
    let currentSearchQuery = '';

    // Guardar el HTML original de la tabla al cargar la página
    let originalTableContent = '';
    const tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        originalTableContent = tableContainer.innerHTML;
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            currentSearchQuery = query;

            clearTimeout(searchTimeout);

            if (query.length === 0) {
                clearSearch();
                return;
            }

            searchSpinner.style.display = 'inline-block';
            searchResultsCount.style.display = 'none';

            searchTimeout = setTimeout(function () {
                performSearch(query, getCurrentFilters());
            }, 300);
        });
    }


    function getCurrentFilters() {
        // Devuelve los filtros en el formato que espera el backend
        return {
            status: activeFilters.status,
            analista_id: activeFilters.analistas, // Enviar el array completo
            fecha_desde: activeFilters.fechaDesde,
            fecha_hasta: activeFilters.fechaHasta
        };
    }

    function performSearch(query, filters = {}) {
        const data = { query: query };
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                data[key] = filters[key];
            }
        });

        $.ajax({
            url: '/activities/search',
            method: 'GET',
            data: data,
            dataType: 'html', // Esperamos HTML, no JSON
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (html) {
                // Reemplaza el contenido del contenedor de la tabla con el HTML del partial
                $('#tableContainer').html(html);

                // Re-inicializa los botones de edición de analistas
                initEditAnalystsButtons();

                // Re-inicializa la edición en línea de prioridad y orden
                initInlineEdit();

                // Re-inicializa los filtros de columna y sortables
                initColumnFiltersAndSort();

                // Re-inicializa la expansión/colapso de subactividades
                initExpandCollapseSubactivities();

                searchSpinner.style.display = 'none';
            },




            error: function () {
                searchSpinner.style.display = 'none';
                showErrorMessage('Error al realizar la búsqueda. Inténtalo de nuevo.');
            }
        });
    }


    // Helper para heatmap color
    function heatmapColor(value) {
        value = parseInt(value, 10) || 0;
        if (value <= 0) return '#dc3545';
        if (value < 50) return '#fd7e14';
        if (value < 80) return '#ffc107';
        return '#28a745';
    }

    // Renderiza una fila de actividad (estructura igual al index)
    function renderActivityRow(activity, isSub = false) {
        // Estado
        let statusHtml = '';
        if (activity.statuses && activity.statuses.length > 0) {
            activity.statuses.forEach(status => {
                statusHtml += `<span class="badge badge-pill mr-1 mb-1" style="background-color: ${status.color}; color: #fff;">
                <i class="${status.icon || 'fas fa-circle'}"></i> ${status.label}
            </span>`;
            });
        } else {
            statusHtml = `<span class="badge badge-secondary badge-pill">${activity.status_label || activity.status || ''}</span>`;
        }

        // Analistas
        let analistasHtml = '';
        if (activity.analistas && activity.analistas.length > 0) {
            analistasHtml = activity.analistas.map(a => `<span class="badge badge-light mr-1 mb-1"><i class="fas fa-user"></i> ${a.name}</span>`).join('');
        } else {
            analistasHtml = '<span class="text-muted"><i class="fas fa-user-slash"></i> Sin asignar</span>';
        }

        // Requerimientos
        let reqHtml = '';
        if (activity.requirements && activity.requirements.length > 0) {
            reqHtml = `<span class="badge badge-warning badge-pill"><i class="fas fa-clipboard-list"></i> ${activity.requirements.length}</span>`;
        } else {
            reqHtml = '<span class="text-muted"><i class="fas fa-clipboard"></i> Sin requerimientos</span>';
        }

        // Fecha
        let fechaHtml = '';
        if (activity.fecha_recepcion) {
            const fecha = new Date(activity.fecha_recepcion);
            const day = String(fecha.getDate()).padStart(2, '0');
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const year = fecha.getFullYear();
            fechaHtml = `<span class="badge badge-outline-info"><i class="fas fa-calendar-alt"></i> ${day}/${month}/${year}</span>`;
        } else {
            fechaHtml = '<span class="text-muted"><i class="fas fa-calendar-times"></i> No asignada</span>';
        }

        // Prioridad y Orden
        let prioridad = (activity.prioridad !== undefined && activity.prioridad !== null)
            ? `<span class="badge badge-outline-info editable-value">${activity.prioridad}</span>`
            : '<span class="badge badge-outline-info editable-value">-</span>';
        let orden = (activity.orden_analista !== undefined && activity.orden_analista !== null)
            ? `<span class="badge badge-outline-secondary editable-value">${activity.orden_analista}</span>`
            : '<span class="badge badge-outline-secondary editable-value">-</span>';

        // Cliente (solo la primera palabra)
        let cliente = (activity.cliente && activity.cliente.nombre)
            ? activity.cliente.nombre.split(' ')[0]
            : '-';

        // Estado Operacional (limitado a 40 caracteres)
        let estatus_operacional = activity.estatus_operacional
            ? (activity.estatus_operacional.length > 40
                ? activity.estatus_operacional.substring(0, 40) + '...'
                : activity.estatus_operacional)
            : '-';

        // Porcentaje de avance (con heatmap)
        let porcentaje = `<span class="badge editable-value" style="background-color: ${heatmapColor(activity.porcentaje_avance ?? 0)}; color: #fff;">
        ${activity.porcentaje_avance ?? 0}%
    </span>
    <input type="number" class="form-control form-control-sm editable-input" value="${activity.porcentaje_avance ?? 0}" style="display:none; width: 70px;" min="0" max="100">`;

        // Descripción
        let descripcion = activity.description
            ? activity.description.substring(0, 30)
            : '';

        return `
    <tr class="${isSub ? 'subactivity-row' : 'parent-activity activity-row'}">
        <td class="align-middle"><span class="badge badge-outline-primary font-weight-bold">${activity.caso || ''}</span></td>
        <td class="align-middle position-relative" style="position: relative;">
            <div class="d-flex align-items-center">
                ${activity.subactivities && activity.subactivities.length > 0
                ? `<span class="toggle-subactivities mr-2" style="cursor: pointer;" data-activity-id="${activity.id}">
                            <i class="fas fa-chevron-right text-primary" id="icon-${activity.id}"></i>
                        </span>`
                : ''
            }
                <div>
                    <div class="font-weight-bold text-dark small">
                        ${activity.name ? activity.name.substring(0, 40) : ''}
                        ${activity.name && activity.name.length > 40
                ? `<span class="text-primary" style="cursor: pointer;" title="${activity.name}" data-toggle="tooltip">
                                    <i class="fas fa-info-circle"></i>
                                </span>`
                : ''
            }
                    </div>
                    ${activity.subactivities && activity.subactivities.length > 0
                ? `<small class="text-muted">
                                <i class="fas fa-sitemap"></i>
                                ${activity.subactivities.length} subactividad(es)
                            </small>`
                : ''
            }
                </div>
            </div>
            <div class="action-buttons"
                style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); display: none; z-index: 2;">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="/activities/${activity.id}/edit"
                        class="btn btn-warning btn-sm action-btn"
                        data-tooltip="Ver/Editar" title="Ver/Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/activities/create?parentId=${activity.id}"
                        class="btn btn-secondary btn-sm action-btn"
                        data-tooltip="Crear Subactividad" title="Crear Subactividad">
                        <i class="fas fa-plus"></i>
                    </a>
                    <form action="/activities/${activity.id}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm action-btn"
                            data-tooltip="Eliminar" title="Eliminar"
                            onclick="return confirm('¿Estás seguro de eliminar esta actividad y todas sus subactividades?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </td>
        <td class="align-middle editable-cell" data-activity-id="${activity.id}" data-field="prioridad" data-sort-value="${activity.prioridad ?? 0}">
            ${prioridad}
        </td>
        <td class="align-middle editable-cell" data-activity-id="${activity.id}" data-field="orden_analista" data-sort-value="${activity.orden_analista ?? 0}">
            ${orden}
        </td>
        <td class="align-middle">${cliente}</td>
        <td class="align-middle">${estatus_operacional}</td>
        <td class="align-middle editable-cell" data-activity-id="${activity.id}" data-field="porcentaje_avance" data-sort-value="${activity.porcentaje_avance ?? 0}">
            ${porcentaje}
        </td>
        <td class="align-middle"><div class="description-cell">${descripcion}</div></td>
        <td class="align-middle">${statusHtml}</td>
        <td class="align-middle">${analistasHtml}</td>
        <td class="align-middle">${reqHtml}</td>
        <td class="align-middle">${fechaHtml}</td>
    </tr>
    `;
    }

    function clearSearch() {
        if (searchInput) {
            searchInput.value = '';
            // Mantener el foco y seleccionar el input para nueva búsqueda
            searchInput.focus();
            searchInput.select();
        }
        currentSearchQuery = '';
        searchSpinner.style.display = 'none';
        searchResultsCount.style.display = 'none';
        searchResultsAlert.style.display = 'none';
        // Restaurar la tabla original
        const tableContainer = document.getElementById('tableContainer');
        if (tableContainer && originalTableContent) {
            tableContainer.innerHTML = originalTableContent;
        }
        tableTitle.textContent = 'Lista de Actividades';
        isSearchActive = false;
        // Vuelve a aplicar filtros locales si quieres
        applyFilters();

        // Re-inicializa los filtros de columna y sortables
        initColumnFiltersAndSort();

        // Re-inicializa la edición en línea de prioridad y orden
        initInlineEdit();

        // Re-inicializa los botones de edición de analistas
        initEditAnalystsButtons();

        // Re-inicializa la expansión/colapso de subactividades
        initExpandCollapseSubactivities();
    }



    // Puedes agregar eventos para limpiar búsqueda con el botón o ESC
    const clearSearchBtn = document.getElementById('clearSearch');
    if (clearSearchBtn) clearSearchBtn.addEventListener('click', clearSearch);

    // Función para inicializar la edición en línea de prioridad y orden
    function initInlineEdit() {
        document.querySelectorAll('.editable-cell .editable-value').forEach(function (span) {
            span.onclick = function () {
                const cell = span.closest('.editable-cell');
                const input = cell.querySelector('.editable-input');
                span.style.display = 'none';
                input.style.display = 'inline-block';
                input.focus();
                input.select();
            };
        });

        document.querySelectorAll('.editable-cell .editable-input').forEach(function (input) {
            input.onblur = saveInlineEdit;
            input.onkeydown = function (e) {
                if (e.key === 'Enter') {
                    saveInlineEdit.call(input, e);
                }
            };
        });

        function syncAdvancedAndColumnFilters() {
            // Sincroniza el filtro avanzado de estado con los checkboxes de columna
            const estadoSelect = document.getElementById('filterStatus');
            if (estadoSelect) {
                const value = estadoSelect.value;
                document.querySelectorAll('.status-filter').forEach(cb => {
                    cb.checked = (cb.value === value) || (value === '' && cb.value === '');
                });
            }
            // Sincroniza el filtro avanzado de analista con los checkboxes de columna
            const analistaSelect = document.getElementById('filterAnalista');
            if (analistaSelect) {
                const value = analistaSelect.value;
                document.querySelectorAll('.analista-filter').forEach(cb => {
                    cb.checked = (cb.value === value) || (value === '' && cb.value === '');
                });
            }
        }

        function saveInlineEdit(e) {
            const input = this;
            const cell = input.closest('.editable-cell');
            const activityId = cell.getAttribute('data-activity-id');
            const field = cell.getAttribute('data-field');
            let value = input.value;

            // Validación para los campos
            if (field === 'prioridad' || field === 'orden_analista') {
                value = Math.max(1, parseInt(value, 10) || 1);
            }
            if (field === 'porcentaje_avance') {
                value = Math.max(0, Math.min(100, parseInt(value, 10) || 0));
            }

            fetch(`/activities/${activityId}/inline-update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    field,
                    value
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Sincroniza filtros antes de recargar la tabla
                        syncAdvancedAndColumnFilters();
                        if (typeof performSearch === 'function') {
                            performSearch(document.getElementById('searchInput').value, getCurrentFilters());
                        } else {
                            location.reload();
                        }
                    } else {
                        alert('Error al actualizar');
                        input.style.display = 'none';
                        cell.querySelector('.editable-value').style.display = 'inline-block';
                    }
                })
                .catch(() => {
                    alert('Error al actualizar');
                    input.style.display = 'none';
                    cell.querySelector('.editable-value').style.display = 'inline-block';
                });
        }


    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function () {
        initEditAnalystsButtons();
        initInlineEdit();
        initColumnFiltersAndSort();
        initExpandCollapseSubactivities();
    });

    /**
     * Unifica la lógica de expandir/colapsar subactividades para index y edit
     */
    function initExpandCollapseSubactivities() {
        // Para index
        const toggleAllBtn = document.getElementById('toggleAllSubactivitiesBtn');
        const toggleAllIcon = document.getElementById('toggleAllSubactivitiesIcon');
        let allExpanded = false;

        if (toggleAllBtn) {
            toggleAllBtn.onclick = function () {
                const tableBody = document.querySelector('#main-activities-table tbody');
                if (!tableBody) return;

                const subRows = tableBody.querySelectorAll('tr.subactivity-row');
                const toggles = tableBody.querySelectorAll('.toggle-subactivities');

                if (!allExpanded) {
                    subRows.forEach(row => row.style.display = 'table-row');
                    toggles.forEach(toggle => {
                        toggle.classList.add('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-right');
                            icon.classList.add('fa-chevron-down');
                        }
                    });
                    if (toggleAllIcon) {
                        toggleAllIcon.classList.remove('fa-chevron-down');
                        toggleAllIcon.classList.add('fa-chevron-up');
                    }
                    allExpanded = true;
                } else {
                    subRows.forEach(row => row.style.display = 'none');
                    toggles.forEach(toggle => {
                        toggle.classList.remove('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-right');
                        }
                    });
                    if (toggleAllIcon) {
                        toggleAllIcon.classList.remove('fa-chevron-up');
                        toggleAllIcon.classList.add('fa-chevron-down');
                    }
                    allExpanded = false;
                }
            };
        }

        // Para edit
        const toggleAllBtnEdit = document.getElementById('toggleAllSubactivitiesBtnEdit');
        const toggleAllIconEdit = document.getElementById('toggleAllSubactivitiesIconEdit');
        let allExpandedEdit = false;

        if (toggleAllBtnEdit) {
            toggleAllBtnEdit.onclick = function () {
                const tableBody = document.querySelector('#subactivitiesTableContainer table tbody');
                if (!tableBody) return;

                const subRows = tableBody.querySelectorAll('tr.subactivity-row');
                const toggles = tableBody.querySelectorAll('.toggle-subactivities');

                if (!allExpandedEdit) {
                    subRows.forEach(row => row.style.display = 'table-row');
                    toggles.forEach(toggle => {
                        toggle.classList.add('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-right');
                            icon.classList.add('fa-chevron-down');
                        }
                    });
                    if (toggleAllIconEdit) {
                        toggleAllIconEdit.classList.remove('fa-chevron-down');
                        toggleAllIconEdit.classList.add('fa-chevron-up');
                    }
                    allExpandedEdit = true;
                } else {
                    subRows.forEach(row => {
                        if (row.classList.contains('level-0')) {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    toggles.forEach(toggle => {
                        toggle.classList.remove('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-right');
                        }
                    });
                    if (toggleAllIconEdit) {
                        toggleAllIconEdit.classList.remove('fa-chevron-up');
                        toggleAllIconEdit.classList.add('fa-chevron-down');
                    }
                    allExpandedEdit = false;
                }
            };
        }

        // Toggle individual subactividades (index)
        const tableBodyIndex = document.querySelector('#main-activities-table tbody');
        if (tableBodyIndex) {
            tableBodyIndex.onclick = function (e) {
                let btn = e.target;
                if (!btn.classList.contains('toggle-subactivities')) {
                    btn = btn.closest('.toggle-subactivities');
                }
                if (!btn) return;

                const parentId = btn.getAttribute('data-activity-id');
                if (!parentId) return;
                const icon = btn.querySelector('i');
                const subRows = tableBodyIndex.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`);

                const isExpanded = btn.classList.contains('expanded');
                if (!isExpanded) {
                    btn.classList.add('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    }
                    subRows.forEach(row => {
                        row.style.display = 'table-row';
                    });
                } else {
                    btn.classList.remove('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                    function hideSubtree(parentId) {
                        tableBodyIndex.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`).forEach(row => {
                            row.style.display = 'none';
                            const subBtn = row.querySelector('.toggle-subactivities.expanded');
                            if (subBtn) {
                                subBtn.classList.remove('expanded');
                                const subIcon = subBtn.querySelector('i');
                                if (subIcon) {
                                    subIcon.classList.remove('fa-chevron-down');
                                    subIcon.classList.add('fa-chevron-right');
                                }
                            }
                            hideSubtree(row.getAttribute('data-activity-id'));
                        });
                    }
                    hideSubtree(parentId);
                }
            };
        }

        // Toggle individual subactividades (edit)
        const tableBodyEdit = document.querySelector('#subactivitiesTableContainer table tbody');
        if (tableBodyEdit) {
            tableBodyEdit.onclick = function (e) {
                let btn = e.target;
                if (!btn.classList.contains('toggle-subactivities')) {
                    btn = btn.closest('.toggle-subactivities');
                }
                if (!btn) return;

                const parentId = btn.getAttribute('data-activity-id');
                if (!parentId) return;
                const icon = btn.querySelector('i');
                const subRows = tableBodyEdit.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`);

                const isExpanded = btn.classList.contains('expanded');
                if (!isExpanded) {
                    btn.classList.add('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    }
                    subRows.forEach(row => {
                        row.style.display = 'table-row';
                    });
                } else {
                    btn.classList.remove('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                    function hideSubtree(parentId) {
                        tableBodyEdit.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`).forEach(row => {
                            row.style.display = 'none';
                            const subBtn = row.querySelector('.toggle-subactivities.expanded');
                            if (subBtn) {
                                subBtn.classList.remove('expanded');
                                const subIcon = subBtn.querySelector('i');
                                if (subIcon) {
                                    subIcon.classList.remove('fa-chevron-down');
                                    subIcon.classList.add('fa-chevron-right');
                                }
                            }
                            hideSubtree(row.getAttribute('data-activity-id'));
                        });
                    }
                    hideSubtree(parentId);
                }
            };
        }
    }

    // Función para inicializar la expansión/colapso de subactividades
    function initExpandCollapseSubactivities() {
        // Botón de expandir/colapsar todas las subactividades (en el header)
        const toggleAllBtn = document.getElementById('toggleAllSubactivitiesBtn');
        const toggleAllIcon = document.getElementById('toggleAllSubactivitiesIcon');
        let allExpanded = false;

        if (toggleAllBtn) {
            toggleAllBtn.onclick = function () {
                const tableBody = document.querySelector('#main-activities-table tbody');
                if (!tableBody) return;

                // Todas las filas de subactividad
                const subRows = tableBody.querySelectorAll('tr.subactivity-row');
                // Todos los toggles de actividades padre
                const toggles = tableBody.querySelectorAll('.toggle-subactivities');

                if (!allExpanded) {
                    // Expandir todas
                    subRows.forEach(row => row.style.display = 'table-row');
                    toggles.forEach(toggle => {
                        toggle.classList.add('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-right');
                            icon.classList.add('fa-chevron-down');
                        }
                    });
                    if (toggleAllIcon) {
                        toggleAllIcon.classList.remove('fa-chevron-down');
                        toggleAllIcon.classList.add('fa-chevron-up');
                    }
                    allExpanded = true;
                } else {
                    // Colapsar todas
                    subRows.forEach(row => row.style.display = 'none');
                    toggles.forEach(toggle => {
                        toggle.classList.remove('expanded');
                        const icon = toggle.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-right');
                        }
                    });
                    if (toggleAllIcon) {
                        toggleAllIcon.classList.remove('fa-chevron-up');
                        toggleAllIcon.classList.add('fa-chevron-down');
                    }
                    allExpanded = false;
                }
            };
        }

        // Toggle subactividades usando event delegation sobre el tbody
        const tableBody = document.querySelector('#main-activities-table tbody');
        if (tableBody) {
            tableBody.onclick = function (e) {
                // Asegura que el click fue en el toggle o en su icono
                let btn = e.target;
                if (!btn.classList.contains('toggle-subactivities')) {
                    btn = btn.closest('.toggle-subactivities');
                }
                if (!btn) return;

                // El id de la actividad está en el data-activity-id del span
                const parentId = btn.getAttribute('data-activity-id');
                if (!parentId) {
                    return;
                }
                const icon = btn.querySelector('i');
                const subRows = tableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`);

                const isExpanded = btn.classList.contains('expanded');
                if (!isExpanded) {
                    btn.classList.add('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-down');
                    }
                    // Mostrar subactividades directas
                    subRows.forEach(row => {
                        row.style.display = 'table-row';
                    });
                } else {
                    btn.classList.remove('expanded');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-right');
                    }
                    // Ocultar subactividades directas y sus descendientes recursivamente
                    function hideSubtree(parentId) {
                        tableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`).forEach(row => {
                            row.style.display = 'none';
                            // Si la subactividad tiene su propio toggle expandido, colapsar también
                            const subBtn = row.querySelector('.toggle-subactivities.expanded');
                            if (subBtn) {
                                subBtn.classList.remove('expanded');
                                const subIcon = subBtn.querySelector('i');
                                if (subIcon) {
                                    subIcon.classList.remove('fa-chevron-down');
                                    subIcon.classList.add('fa-chevron-right');
                                }
                            }
                            hideSubtree(row.getAttribute('data-activity-id'));
                        });
                    }
                    hideSubtree(parentId);
                }
            };
        }
    }

    // Función para inicializar filtros de columna y sortables
    function initColumnFiltersAndSort() {
        setupSortHandlersForAllTables && setupSortHandlersForAllTables();
        setupColumnFilters && setupColumnFilters();
    }

    // Función para inicializar los botones de edición de analistas (llámala tras cada búsqueda)
    function initEditAnalystsButtons() {
        document.querySelectorAll('.edit-analysts-btn').forEach(function (btn) {
            // Evita duplicar listeners
            btn.removeEventListener('click', handleEditAnalystsClick);
            btn.addEventListener('click', handleEditAnalystsClick);
        });
    }

    function handleEditAnalystsClick(event) {
        var btn = event.currentTarget;
        var activityId = btn.getAttribute('data-activity-id');
        var analysts = [];
        btn.closest('td').querySelectorAll('.badge').forEach(function (badge) {
            analysts.push(badge.textContent.trim());
        });

        var select = document.getElementById('modalAnalystsSelect');
        for (var i = 0; i < select.options.length; i++) {
            select.options[i].selected = false;
            if (analysts.includes(select.options[i].text.trim())) {
                select.options[i].selected = true;
            }
        }

        var form = document.getElementById('analystsEditForm');
        form.action = '/activities/' + activityId + '/analysts';
        document.getElementById('modalAnalystsActivityId').value = activityId;

        $('#analystsEditModal').modal('show');
    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', function () {
        initEditAnalystsButtons();
    });

    document.addEventListener('keydown', function (e) {

        // Ctrl+K o Ctrl+F: enfocar el input de búsqueda
        if ((e.ctrlKey && (e.key === 'k' || e.key === 'K' || e.key === 'f' || e.key === 'F'))) {
            if (searchInput) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
            }
        }
        // Esc: limpiar búsqueda si está activa o si el input tiene texto
        if (e.key === 'Escape') {
            if (isSearchActive || (searchInput && searchInput.value.length > 0)) {
                clearSearch();
                // No quitar el foco, así puedes seguir escribiendo
            }
        }
    });
});

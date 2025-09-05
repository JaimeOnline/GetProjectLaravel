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
    let currentSort = { column: null, direction: 'asc' };
    let originalRows = null;
    let activeFilters = {
        status: [],
        analistas: [],
        fechaDesde: null,
        fechaHasta: null
    };

    // Formatear etiquetas de estado al cargar
    formatStatusLabels();

    // Inicializar
    setupSortHandlers();
    setupColumnFilters();

    // --- Expandir/Colapsar todas las subactividades ---
    const toggleAllBtn = document.getElementById('toggleAllSubactivitiesBtn');
    const toggleAllIcon = document.getElementById('toggleAllSubactivitiesIcon');
    let allExpanded = false;

    if (toggleAllBtn) {
        toggleAllBtn.addEventListener('click', function () {
            const tableBody = document.querySelector('#tableContainer tbody');
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
    const tableBody = document.querySelector('#tableContainer tbody');
    if (tableBody) {
        tableBody.addEventListener('click', function (e) {
            // Asegura que el click fue en el toggle o en su icono
            let btn = e.target;
            if (!btn.classList.contains('toggle-subactivities')) {
                btn = btn.closest('.toggle-subactivities');
            }
            if (!btn) return;

            // DEPURACIÓN: Verifica si el evento se dispara y qué botón es
            console.log('Toggle click:', btn);

            // El id de la actividad está en el data-activity-id del span
            const parentId = btn.getAttribute('data-activity-id');
            if (!parentId) {
                console.log('No data-activity-id en el toggle');
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

    /**
     * Formatear todas las etiquetas de estado para mostrar nombres legibles
     */
    function formatStatusLabels() {
        document.querySelectorAll('.status-filter').forEach(updateStatusCheckboxLabel);
    }

    /**
     * Configurar manejadores de ordenamiento
     */
    function setupSortHandlers() {
        const sortableHeaders = document.querySelectorAll('.sortable');
        sortableHeaders.forEach(header => {
            // Eliminar manejadores de eventos anteriores
            header.removeEventListener('click', handleSortClick);

            // Agregar nuevo event listener
            header.addEventListener('click', handleSortClick);
        });
    }

    /**
     * Manejador de eventos para el clic en encabezados ordenables
     */

    function handleSortClick(event) {
        const header = event.currentTarget;
        const column = header.getAttribute('data-sort');

        // Guardar las filas originales la primera vez
        if (!originalRows) {
            const table = document.querySelector('#tableContainer table');
            originalRows = Array.from(table.tBodies[0].rows).map(row => row.cloneNode(true));
        }

        if (currentSort.column === column) {
            if (currentSort.direction === 'asc') {
                currentSort.direction = 'desc';
            } else if (currentSort.direction === 'desc') {
                // Tercer clic: volver a neutro
                currentSort = { column: null, direction: null };
                restoreOriginalOrder();
                updateSortIcons(null, null);
                return;
            } else {
                currentSort.direction = 'asc';
            }
        } else {
            currentSort = { column: column, direction: 'asc' };
        }

        sortTable(column);
        updateSortIcons(currentSort.column, currentSort.direction);
    }

    function restoreOriginalOrder() {
        const table = document.querySelector('#tableContainer table');
        const tbody = table.tBodies[0];
        tbody.innerHTML = '';
        originalRows.forEach(row => {
            tbody.appendChild(row.cloneNode(true));
        });
    }

    /**
     * Ordenar tabla por columna
     */
    function sortTable(column) {
        console.log('Ordenando por columna:', column);

        // Obtener filas de la tabla
        const tableBody = document.querySelector('#tableContainer tbody');
        if (!tableBody) {
            console.error('No se encontró el cuerpo de la tabla');
            return;
        }

        const rows = Array.from(tableBody.querySelectorAll('tr.parent-activity'));

        // Ordenar filas
        rows.sort((a, b) => {
            let aValue = getSortValue(a, column);
            let bValue = getSortValue(b, column);

            // Ordenar por fecha
            if (column === 'fecha_recepcion') {
                // Si alguna fecha está vacía, ponla al final
                if (!aValue && !bValue) return 0;
                if (!aValue) return 1;
                if (!bValue) return -1;

                // Convertir formato DD/MM/YYYY a objeto Date
                const aParts = aValue.split('/');
                const bParts = bValue.split('/');
                const aDate = aParts.length === 3 ? new Date(aParts[2], aParts[1] - 1, aParts[0]) : null;
                const bDate = bParts.length === 3 ? new Date(bParts[2], bParts[1] - 1, bParts[0]) : null;

                if (!aDate || isNaN(aDate)) return 1;
                if (!bDate || isNaN(bDate)) return -1;

                if (aDate < bDate) return currentSort.direction === 'asc' ? -1 : 1;
                if (aDate > bDate) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            }

            // Ordenar por número si ambos son numéricos
            const aNum = parseFloat(aValue);
            const bNum = parseFloat(bValue);
            if (!isNaN(aNum) && !isNaN(bNum)) {
                if (aNum < bNum) return currentSort.direction === 'asc' ? -1 : 1;
                if (aNum > bNum) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            }

            // Ordenar por texto
            if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
            if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        // Reordenar filas en el DOM
        rows.forEach(row => {
            tableBody.appendChild(row);
            // También mover las subactividades si existen
            const activityId = row.getAttribute('data-activity-id');
            const subRows = tableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${activityId}"]`);
            subRows.forEach(subRow => {
                tableBody.appendChild(subRow);
            });
        });
    }

    /**
     * Actualizar iconos de ordenamiento
     */
    function updateSortIcons(activeColumn, direction) {
        document.querySelectorAll('.sortable').forEach(header => {
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

    /**
     * Obtener valor para ordenamiento
     */
    function getSortValue(row, column) {
        const cells = row.querySelectorAll('td');
        let value = '';

        switch (column) {
            case 'caso':
                value = cells[0]?.textContent?.trim() || '';
                break;
            case 'nombre':
                value = cells[1]?.textContent?.trim() || '';
                break;
            case 'descripcion':
                value = cells[2]?.textContent?.trim() || '';
                break;
            case 'status':
                value = cells[3]?.textContent?.trim() || '';
                break;
            case 'analistas':
                value = cells[4]?.textContent?.trim() || '';
                break;
            case 'fecha_recepcion':
                // Extraer solo la fecha del formato "DD/MM/YYYY"
                const dateText = cells[6]?.textContent?.trim() || '';
                const dateMatch = dateText.match(/\d{2}\/\d{2}\/\d{4}/);
                value = dateMatch ? dateMatch[0] : '';
                return value; // <-- DEVUELVE LA FECHA TAL CUAL
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
            'pausada': 'Pausada'
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
     * Aplicar todos los filtros activos
     */
    function applyFilters() {
        const rows = document.querySelectorAll('#tableContainer tbody tr');
        let visibleCount = 0;

        // Asegurar que el contenedor de la tabla mantenga su altura mínima
        const tableContainer = document.querySelector('#tableContainer');
        if (tableContainer) {
            // Guardar la altura actual si es la primera vez
            if (!tableContainer.getAttribute('data-original-height') && tableContainer.offsetHeight > 300) {
                tableContainer.setAttribute('data-original-height', tableContainer.offsetHeight + 'px');
                tableContainer.style.minHeight = tableContainer.offsetHeight + 'px';
            }
        }

        rows.forEach(row => {
            // Ignorar filas que no son actividades ni subactividades
            if (!row.classList.contains('activity-row')) return;

            let shouldShow = true;
            const cells = row.querySelectorAll('td');

            // Filtro por estado
            if (activeFilters.status.length > 0 && cells[3]) {
                const statusText = cells[3].textContent.trim().toLowerCase();
                const statusMatch = activeFilters.status.some(status => {
                    // Mapeo de códigos internos a textos legibles
                    switch (status) {
                        case 'no_iniciada':
                            return statusText.includes('no iniciada');
                        case 'en_ejecucion':
                            return statusText.includes('en ejecución') || statusText.includes('ejecutando');
                        case 'en_espera_de_insumos':
                            return statusText.includes('en espera') || statusText.includes('insumos');
                        case 'en_certificacion_por_cliente':
                            return statusText.includes('certificación') || statusText.includes('certificando');
                        case 'pases_enviados':
                            return statusText.includes('pases enviados');
                        case 'culminada':
                            return statusText.includes('culminada') || statusText.includes('completada');
                        case 'pausada':
                            return statusText.includes('pausada');
                        default:
                            return statusText.includes(status.replace(/_/g, ' ').toLowerCase());
                    }
                });

                if (!statusMatch) shouldShow = false;
            }

            // Filtro por analista
            if (shouldShow && activeFilters.analistas.length > 0 && cells[4]) {
                const analistaText = cells[4].textContent.trim().toLowerCase();
                const analistaMatch = activeFilters.analistas.some(analistaId => {
                    // Busca el nombre del analista por su ID
                    const analistaLabel = document.querySelector(`.analista-filter[value="${analistaId}"] + label`);
                    if (!analistaLabel) return false;
                    const nombre = analistaLabel.textContent.trim().toLowerCase();
                    return analistaText.includes(nombre);
                });
                if (!analistaMatch) shouldShow = false;
            }

            // Filtro por fecha
            if (shouldShow && (activeFilters.fechaDesde || activeFilters.fechaHasta) && cells[7]) {
                const fechaText = cells[6].textContent.trim(); // Columna 7: Fecha de Recepción, 
                const fechaMatch = fechaText.match(/(\d{2})\/(\d{2})\/(\d{4})/);

                if (fechaMatch) {
                    // Convertir a formato YYYY-MM-DD para comparación
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
                    // Si no se puede extraer la fecha pero hay filtro de fecha, ocultar
                    shouldShow = false;
                }
            }

            // Aplicar visibilidad
            if (shouldShow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }

            // Si es una actividad padre y está oculta, ocultar también sus subactividades
            if (row.classList.contains('parent-activity') && !shouldShow) {
                const activityId = row.getAttribute('data-activity-id');
                document.querySelectorAll(`tr.subactivity-row[data-parent-id="${activityId}"]`).forEach(subRow => {
                    subRow.style.display = 'none';
                });
            }
        });

        // Actualizar contador de resultados
        updateResultsCount(visibleCount);
    }

    /**
     * Actualizar contador de resultados
     */
    function updateResultsCount(visibleCount) {
        const totalRows = document.querySelectorAll('#tableContainer tbody tr.activity-row').length;

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
            if (cb.value === '') {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
        });

        // Limpiar filtros de analistas
        document.querySelectorAll('.analista-filter').forEach(cb => {
            if (cb.value === '') {
                cb.checked = true;
            } else {
                cb.checked = false;
            }
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
    const estadoSelect = document.getElementById('filterEstado');
    const analistaSelect = document.getElementById('filterAnalista');
    const fechaInput = document.getElementById('filterFecha');

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
        // Fecha
        if (fechaInput && fechaInput.value) {
            activeFilters.fechaDesde = fechaInput.value;
            activeFilters.fechaHasta = fechaInput.value;
        } else {
            activeFilters.fechaDesde = null;
            activeFilters.fechaHasta = null;
        }
        applyFilters();
        updateFilterIndicators();
    }

    if (estadoSelect) estadoSelect.addEventListener('change', filtrarAvanzado);
    if (analistaSelect) analistaSelect.addEventListener('change', filtrarAvanzado);
    if (fechaInput) fechaInput.addEventListener('change', filtrarAvanzado);
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
            analista_id: activeFilters.analistas.length > 0 ? activeFilters.analistas[0] : '',
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
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
                displaySearchResults(data, query);
                searchSpinner.style.display = 'none';
            },
            error: function () {
                searchSpinner.style.display = 'none';
                showErrorMessage('Error al realizar la búsqueda. Inténtalo de nuevo.');
            }
        });
    }

    function displaySearchResults(data, query) {
        resultsNumber.textContent = data.total_results;
        searchResultsCount.style.display = 'inline-block';

        if (data.total_results > 0) {
            searchResultsText.textContent = `Se encontraron ${data.total_results} resultado(s) para "${query}"`;
            searchResultsAlert.style.display = 'block';
            tableTitle.textContent = `Resultados de búsqueda (${data.total_results})`;
        } else {
            searchResultsText.textContent = `No se encontraron resultados para "${query}"`;
            searchResultsAlert.style.display = 'block';
            tableTitle.textContent = 'Sin resultados';
        }

        // Renderizar resultados en la tabla
        let html = `
            <table class="table table-hover mb-0 modern-table">
                <thead class="thead-light">
                    <tr>
                        <th class="border-0"><i class="fas fa-hashtag text-primary"></i> Caso</th>
                        <th class="border-0"><i class="fas fa-file-alt text-primary"></i> Nombre</th>
                        <th class="border-0"><i class="fas fa-align-left text-primary"></i> Descripción</th>
                        <th class="border-0"><i class="fas fa-flag text-primary"></i> Estado</th>
                        <th class="border-0"><i class="fas fa-users text-primary"></i> Analistas</th>
                        <th class="border-0"><i class="fas fa-clipboard-list text-primary"></i> Requerimientos</th>
                        <th class="border-0"><i class="fas fa-calendar text-primary"></i> Fecha</th>
                        <th class="border-0 text-center"><i class="fas fa-cogs text-primary"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
        `;

        // Renderizar actividades principales
        data.activities.forEach(activity => {
            html += renderActivityRow(activity);
        });

        // Renderizar subactividades (si las hay)
        if (data.subactivities && data.subactivities.length > 0) {
            data.subactivities.forEach(subactivity => {
                html += renderActivityRow(subactivity, true);
            });
        }

        html += `
                </tbody>
            </table>
        `;

        document.getElementById('tableContainer').innerHTML = html;
    }

    // Renderiza una fila de actividad (puedes mejorar el HTML según tu diseño)
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

        // Acciones
        let actionsHtml = `
            <div class="action-buttons">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="/activities/${activity.id}/edit" class="btn btn-warning btn-sm action-btn" title="Ver/Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/activities/create?parentId=${activity.id}" class="btn btn-secondary btn-sm action-btn" title="Crear Subactividad">
                        <i class="fas fa-plus"></i>
                    </a>
                    <form action="/activities/${activity.id}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm action-btn" title="Eliminar"
                            onclick="return confirm('¿Estás seguro de eliminar esta actividad y todas sus subactividades?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        `;

        return `
            <tr class="${isSub ? 'subactivity-row' : 'parent-activity activity-row'}">
                <td class="align-middle">${activity.caso || ''}</td>
                <td class="align-middle">${activity.name || ''}</td>
                <td class="align-middle">${activity.description ? activity.description.substring(0, 30) : ''}</td>
                <td class="align-middle">${statusHtml}</td>
                <td class="align-middle">${analistasHtml}</td>
                <td class="align-middle">${reqHtml}</td>
                <td class="align-middle">${fechaHtml}</td>
                <td class="align-middle text-center">${actionsHtml}</td>
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
    }

    // Puedes agregar eventos para limpiar búsqueda con el botón o ESC
    const clearSearchBtn = document.getElementById('clearSearch');
    if (clearSearchBtn) clearSearchBtn.addEventListener('click', clearSearch);

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

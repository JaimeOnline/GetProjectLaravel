// Funcionalidad optimizada del modal de edición de estados
let rowsCache = [];

// Función para inicializar la edición de estados
function initializeStatusEditing() {
    console.log('Inicializando edición de estados optimizada...');
    
    // Verificar que el modal existe
    const modal = document.getElementById('statusEditModal');
    if (!modal) {
        console.error('Modal statusEditModal no encontrado');
        return;
    }
    
    console.log('Modal encontrado correctamente');
    
    // Manejar clics en botones de editar estado
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-status-btn')) {
            console.log('Click en botón de editar estado detectado');
            const button = e.target.closest('.edit-status-btn');
            const activityId = button.getAttribute('data-activity-id');
            console.log('ID de actividad:', activityId);
            openStatusEditModal(activityId);
        }
    });

    // Manejar guardado de cambios de estado
    const saveButton = document.getElementById('saveStatusChanges');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            saveStatusChanges();
        });
    }
    
    console.log('Event listeners configurados');
}

// Función para abrir el modal de edición de estados
function openStatusEditModal(activityId) {
    console.log('Abriendo modal para actividad:', activityId);
    
    // Buscar la actividad en los datos cargados
    const activity = rowsCache.find(row => row.id == activityId);
    
    if (!activity) {
        console.error('Actividad no encontrada:', activityId);
        console.log('Actividades disponibles:', rowsCache.map(r => r.id));
        
        // Si no encontramos en rowsCache, intentar obtener datos del DOM
        const activityRow = document.querySelector(`tr[data-activity-id="${activityId}"]`);
        if (activityRow) {
            console.log('Encontrada fila de actividad en DOM');
            // Extraer datos básicos del DOM
            const cells = activityRow.querySelectorAll('td');
            const basicActivity = {
                id: activityId,
                caso: cells[0] ? cells[0].textContent.trim() : 'Sin caso',
                nombre: cells[1] ? cells[1].textContent.trim() : 'Sin nombre',
                statuses: []
            };
            
            fillModalWithActivity(basicActivity);
            $('#statusEditModal').modal('show');
        }
        return;
    }
    
    console.log('Actividad encontrada:', activity);
    fillModalWithActivity(activity);
    $('#statusEditModal').modal('show');
}

// Función para llenar el modal con datos de la actividad
function fillModalWithActivity(activity) {
    // Llenar información de la actividad
    document.getElementById('modalActivityCaso').textContent = activity.caso || 'Sin caso';
    document.getElementById('modalActivityNombre').textContent = activity.nombre || 'Sin nombre';
    
    // Mostrar estados actuales
    const currentStatusesDiv = document.getElementById('modalCurrentStatuses');
    currentStatusesDiv.innerHTML = '';
    
    if (activity.statuses && activity.statuses.length > 0) {
        activity.statuses.forEach(status => {
            const badge = document.createElement('span');
            badge.className = 'badge badge-pill mr-1 mb-1';
            badge.style.backgroundColor = status.color;
            badge.style.color = 'white';
            badge.innerHTML = `<i class="fas fa-${status.icon}"></i> ${status.label}`;
            currentStatusesDiv.appendChild(badge);
        });
    } else {
        currentStatusesDiv.innerHTML = '<span class="text-muted">Sin estados asignados</span>';
    }

    // Marcar checkboxes de estados actuales
    const checkboxes = document.querySelectorAll('.status-checkbox');
    checkboxes.forEach(checkbox => {
        const statusId = parseInt(checkbox.value);
        const hasStatus = activity.statuses && activity.statuses.some(s => s.id === statusId);
        checkbox.checked = hasStatus;
    });

    // Guardar el ID de la actividad en el modal
    document.getElementById('statusEditModal').setAttribute('data-activity-id', activity.id);
}

// Función para guardar cambios de estado (optimizada)
function saveStatusChanges() {
    const modal = document.getElementById('statusEditModal');
    const activityId = modal.getAttribute('data-activity-id');
    
    // Obtener IDs de estados seleccionados
    const selectedStatusIds = [];
    document.querySelectorAll('.status-checkbox:checked').forEach(checkbox => {
        selectedStatusIds.push(parseInt(checkbox.value));
    });

    console.log('Guardando estados (IDs):', selectedStatusIds, 'para actividad:', activityId);

    // Mostrar loading
    const saveButton = document.getElementById('saveStatusChanges');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    saveButton.disabled = true;

    // Enviar petición AJAX
    fetch(`/activities/${activityId}/statuses`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status_ids: selectedStatusIds
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            // Cerrar modal
            $('#statusEditModal').modal('hide');
            
            // Mostrar mensaje de éxito
            showAlert('Estados actualizados correctamente', 'success');
            
            // Actualizar la fila de la tabla sin recargar la página
            updateActivityRowInTable(activityId, data.statuses);
            
            // Actualizar el cache local
            updateActivityInCache(activityId, data.statuses);
            
        } else {
            showAlert(data.message || 'Error al actualizar estados', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al actualizar estados', 'danger');
    })
    .finally(() => {
        // Restaurar botón
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

// Función para actualizar la fila de la tabla sin recargar la página
function updateActivityRowInTable(activityId, newStatuses) {
    console.log('Actualizando fila de actividad:', activityId, 'con estados:', newStatuses);
    
    // Buscar la fila de la actividad en la tabla
    let activityRow = document.querySelector(`tr[data-activity-id="${activityId}"]`);
    
    if (!activityRow) {
        console.log('Fila no encontrada por data-activity-id, buscando por otros métodos...');
        
        // Buscar por el contenedor de status-cell que tiene data-activity-id
        const statusCell = document.querySelector(`.status-cell[data-activity-id="${activityId}"]`);
        if (statusCell) {
            activityRow = statusCell.closest('tr');
            console.log('Fila encontrada por status-cell');
        }
        
        // Si aún no se encuentra, buscar por contenido de caso
        if (!activityRow) {
            const activity = rowsCache.find(a => a.id == activityId);
            if (activity) {
                const allRows = document.querySelectorAll('tbody tr');
                for (let row of allRows) {
                    const casoCell = row.querySelector('td:first-child');
                    if (casoCell && casoCell.textContent.trim() === activity.caso) {
                        activityRow = row;
                        console.log('Fila encontrada por contenido de caso');
                        break;
                    }
                }
            }
        }
        
        // Si encontramos la fila, agregar el atributo data-activity-id
        if (activityRow) {
            activityRow.setAttribute('data-activity-id', activityId);
            console.log('Agregado data-activity-id a la fila');
        }
    }
    
    if (!activityRow) {
        console.error('No se pudo encontrar la fila de la actividad:', activityId);
        return;
    }
    
    updateStatusCell(activityRow, newStatuses);
}

// Función para actualizar la celda de estados
function updateStatusCell(row, newStatuses) {
    // Buscar la celda de estados (generalmente la 4ta columna)
    const statusCell = row.querySelector('td:nth-child(4)');
    if (!statusCell) {
        console.log('Celda de estados no encontrada');
        return;
    }
    
    // Buscar el contenedor de estados y el botón de editar
    let statusContainer = statusCell.querySelector('.status-display');
    let editButton = statusCell.querySelector('.edit-status-btn');
    
    // Si no existe la estructura, crearla
    if (!statusContainer) {
        // Crear la estructura completa
        statusCell.innerHTML = `
            <div class="status-cell" data-activity-id="${row.getAttribute('data-activity-id') || ''}">
                <div class="status-display"></div>
                <div class="status-edit-btn">
                    <button class="btn btn-sm btn-outline-secondary edit-status-btn" 
                            data-activity-id="${row.getAttribute('data-activity-id') || ''}" 
                            title="Editar estados">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>
        `;
        statusContainer = statusCell.querySelector('.status-display');
        editButton = statusCell.querySelector('.edit-status-btn');
    }
    
    // Limpiar solo el contenedor de badges, no el botón
    statusContainer.innerHTML = '';
    
    // Agregar nuevos badges de estados
    if (newStatuses && newStatuses.length > 0) {
        newStatuses.forEach(status => {
            const badge = document.createElement('span');
            badge.className = 'badge badge-pill mr-1 mb-1';
            badge.style.backgroundColor = status.color;
            badge.style.color = 'white';
            badge.innerHTML = `<i class="fas fa-${status.icon}"></i> ${status.label}`;
            statusContainer.appendChild(badge);
        });
    } else {
        statusContainer.innerHTML = '<span class="text-muted">Sin estados</span>';
    }
    
    // Asegurar que el botón tenga el ID correcto
    if (editButton) {
        const activityId = row.getAttribute('data-activity-id') || 
                          row.querySelector('[data-activity-id]')?.getAttribute('data-activity-id');
        if (activityId) {
            editButton.setAttribute('data-activity-id', activityId);
        }
    }
    
    // Agregar efecto visual de actualización
    statusCell.style.backgroundColor = '#d4edda';
    setTimeout(() => {
        statusCell.style.backgroundColor = '';
    }, 2000);
    
    console.log('Celda de estados actualizada correctamente con botón preservado');
}

// Función para actualizar el cache local
function updateActivityInCache(activityId, newStatuses) {
    const activityIndex = rowsCache.findIndex(a => a.id == activityId);
    if (activityIndex !== -1) {
        rowsCache[activityIndex].statuses = newStatuses;
        console.log('Cache local actualizado para actividad:', activityId);
    }
}

// Función para mostrar alertas
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    // Insertar al inicio del contenedor
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    // Auto-remover después de 3 segundos (reducido para mejor UX)
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

// Función para procesar datos de actividades
function processActivitiesData(activitiesData) {
    rowsCache = [];
    
    function processActivity(activity, isSubactivity = false) {
        const processedActivity = {
            id: activity.id,
            caso: activity.caso,
            nombre: activity.name,
            description: activity.description,
            statuses: activity.statuses || [],
            analistas: activity.analistas || [],
            comments: activity.comments || [],
            emails: activity.emails || [],
            requirements: activity.requirements || [],
            fecha_recepcion: activity.fecha_recepcion,
            isSubactivity: isSubactivity,
            parent_id: activity.parent_id
        };
        
        rowsCache.push(processedActivity);
        
        // Procesar subactividades recursivamente
        if (activity.subactivities && activity.subactivities.length > 0) {
            activity.subactivities.forEach(subactivity => {
                processActivity(subactivity, true);
            });
        }
    }
    
    // Procesar todas las actividades
    activitiesData.forEach(activity => {
        processActivity(activity);
    });
    
    console.log('Datos de actividades procesados:', rowsCache.length, 'actividades');
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM listo - Inicializando modal de estados optimizado');
    initializeStatusEditing();
});
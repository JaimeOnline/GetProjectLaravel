// ===== SISTEMA DE ESTADOS MÚLTIPLES =====

// Variables globales para estados
let availableStatuses = [];
let currentEditingActivity = null;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Cargar estados disponibles al inicializar
    loadAvailableStatuses();
    
    // Event listeners para botones de editar estado
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-status-btn')) {
            const button = e.target.closest('.edit-status-btn');
            const activityId = button.getAttribute('data-activity-id');
            openStatusEditModal(activityId);
        }
    });
    
    console.log('Sistema de estados múltiples inicializado');
});

function loadAvailableStatuses() {
    fetch('/statuses')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                availableStatuses = data.statuses;
                console.log('Estados disponibles cargados:', availableStatuses);
            } else {
                console.error('Error en respuesta:', data);
            }
        })
        .catch(error => {
            console.error('Error al cargar estados:', error);
        });
}

function openStatusEditModal(activityId) {
    currentEditingActivity = activityId;
    
    // Cargar estados actuales de la actividad
    fetch(`/activities/${activityId}/statuses`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateStatusModal(data.statuses);
                $('#statusEditModal').modal('show');
            } else {
                console.error('Error en respuesta:', data);
                showAlert('Error al cargar los estados de la actividad', 'danger');
            }
        })
        .catch(error => {
            console.error('Error al cargar estados de la actividad:', error);
            showAlert('Error al cargar los estados de la actividad', 'danger');
        });
}

function populateStatusModal(currentStatuses) {
    const modalBody = document.getElementById('statusModalBody');
    const currentStatusIds = currentStatuses.map(status => status.id);
    
    let html = '<div class="form-group">';
    html += '<label class="font-weight-bold mb-3">Selecciona los estados para esta actividad:</label>';
    html += '<div class="status-checkboxes">';
    
    availableStatuses.forEach(status => {
        const isChecked = currentStatusIds.includes(status.id);
        html += `
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" 
                       id="status_${status.id}" 
                       value="${status.id}" 
                       ${isChecked ? 'checked' : ''}>
                <label class="custom-control-label" for="status_${status.id}">
                    <span class="badge mr-2" style="background-color: ${status.color}; color: white;">
                        <i class="${status.icon || 'fas fa-circle'}"></i> ${status.label}
                    </span>
                </label>
            </div>
        `;
    });
    
    html += '</div></div>';
    modalBody.innerHTML = html;
}

function saveStatusChanges() {
    const checkedBoxes = document.querySelectorAll('#statusModalBody input[type="checkbox"]:checked');
    const statusIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value));
    
    console.log('Estados seleccionados:', statusIds);
    
    if (statusIds.length === 0) {
        showAlert('Debes seleccionar al menos un estado', 'warning');
        return;
    }
    
    // Mostrar loading
    const saveBtn = document.getElementById('saveStatusBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    saveBtn.disabled = true;
    
    // Obtener token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('Token CSRF no encontrado');
        showAlert('Error: Token CSRF no encontrado', 'danger');
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        return;
    }
    
    console.log('Enviando petición a:', `/activities/${currentEditingActivity}/statuses`);
    console.log('Datos:', { status_ids: statusIds });
    
    fetch(`/activities/${currentEditingActivity}/statuses`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status_ids: statusIds
        })
    })
    .then(response => {
        console.log('Respuesta recibida:', response);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos de respuesta:', data);
        if (data.success) {
            // Actualizar la vista de estados en la tabla
            updateStatusDisplay(currentEditingActivity, data.statuses);
            $('#statusEditModal').modal('hide');
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message || 'Error al actualizar los estados', 'danger');
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error);
        showAlert('Error al actualizar los estados: ' + error.message, 'danger');
    })
    .finally(() => {
        // Restaurar botón
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function updateStatusDisplay(activityId, statuses) {
    const statusCell = document.querySelector(`tr[data-activity-id="${activityId}"] .status-display`);
    if (!statusCell) {
        console.log('No se encontró la celda de estado para actividad:', activityId);
        return;
    }
    
    let html = '';
    statuses.forEach(status => {
        const contrastColor = getContrastColor(status.color);
        html += `
            <span class="badge badge-pill mr-1 mb-1" 
                  style="background-color: ${status.color}; color: ${contrastColor};">
                <i class="${status.icon || 'fas fa-circle'}"></i> ${status.label}
            </span>
        `;
    });
    
    statusCell.innerHTML = html;
    console.log('Estado actualizado en la tabla para actividad:', activityId);
}

function getContrastColor(hexColor) {
    // Remover # si existe
    const hex = hexColor.replace('#', '');
    
    // Convertir a RGB
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    
    // Calcular brillo
    const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    
    return brightness > 155 ? '#000000' : '#ffffff';
}

function showAlert(message, type = 'info') {
    // Crear alerta temporal
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
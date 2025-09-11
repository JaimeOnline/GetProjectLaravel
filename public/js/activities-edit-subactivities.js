document.addEventListener('DOMContentLoaded', function () {
    // --- Expandir/Colapsar todas las subactividades en la página de edición ---
    const toggleAllBtnEdit = document.getElementById('toggleAllSubactivitiesBtnEdit');
    const toggleAllIconEdit = document.getElementById('toggleAllSubactivitiesIconEdit');
    let allExpandedEdit = false;

    if (toggleAllBtnEdit) {
        toggleAllBtnEdit.addEventListener('click', function () {
            const tableBody = document.querySelector('#subactivitiesTableContainer table tbody');
            if (!tableBody) return;

            const subRows = tableBody.querySelectorAll('tr.subactivity-row');
            const toggles = tableBody.querySelectorAll('.toggle-subactivities');

            if (!allExpandedEdit) {
                // Mostrar todas las subactividades y expandir todos los toggles
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
                // Ocultar todas las subactividades excepto las de nivel 0
                subRows.forEach(row => {
                    if (row.classList.contains('level-0')) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
                // Cerrar todos los toggles y poner el icono cerrado
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
        });
    }

    // Toggle individual subactividades (igual que en el index)
    const editTableBody = document.querySelector('#subactivitiesTableContainer table tbody');
    if (editTableBody) {
        editTableBody.addEventListener('click', function (e) {
            let btn = e.target;
            if (!btn.classList.contains('toggle-subactivities')) {
                btn = btn.closest('.toggle-subactivities');
            }
            if (!btn) return;

            const parentId = btn.getAttribute('data-activity-id');
            if (!parentId) return;
            const icon = btn.querySelector('i');
            const subRows = editTableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`);

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
                    editTableBody.querySelectorAll(`tr.subactivity-row[data-parent-id="${parentId}"]`).forEach(row => {
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
        });
    }
});

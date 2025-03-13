// Admin JavaScript functions

document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });

    // Auto-hide alert messages after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-persistent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });

    // Confirm delete actions
    const confirmDeleteForms = document.querySelectorAll('.confirm-delete');
    confirmDeleteForms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
});
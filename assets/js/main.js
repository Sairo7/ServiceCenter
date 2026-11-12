/**
 * Main JavaScript Interactivity
 * TKJ Service Business Web App
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('TKJ Service Business Web App initialized.');

    // Auto dismiss alert notifications after 6 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 6000);
    });
});

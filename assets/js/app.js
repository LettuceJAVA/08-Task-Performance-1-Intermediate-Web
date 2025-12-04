// assets/js/app.js

document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Reminder Poll
    checkUpcomingAppointments();
    setInterval(checkUpcomingAppointments, 60000); // Check every minute
});

function checkUpcomingAppointments() {
    // Simple polling stub
    // In a real app, you would check the user's role and ID
    // fetch('/hospital-scheduler/api/appointments.php?action=upcoming')
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.length > 0) {
    //             // Show toast or alert
    //             console.log("Upcoming appointments:", data);
    //         }
    //     });
}

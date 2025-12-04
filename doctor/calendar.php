<?php
require_once '../includes/auth.php';
requireRole('doctor');
require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="h2 mb-4">My Calendar</h1>
            <div class="card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/hospital-scheduler/api/appointments.php',
        eventClick: function(info) {
            alert('Event: ' + info.event.title + '\nStatus: ' + info.event.extendedProps.status);
        }
    });
    calendar.render();
});
</script>

<?php require_once '../includes/footer.php'; ?>

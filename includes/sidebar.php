<?php
$role = getCurrentRole();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="sidebar-brand mb-3">
            <i class="bi bi-hospital-fill"></i> Scheduler
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="/hospital-scheduler/<?php echo $role; ?>/dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            
            <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'doctors.php' ? 'active' : ''; ?>" href="/hospital-scheduler/admin/doctors.php">
                        <i class="bi bi-person-badge me-2"></i> Doctors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="/hospital-scheduler/admin/users.php">
                        <i class="bi bi-people me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'rooms.php' ? 'active' : ''; ?>" href="/hospital-scheduler/admin/rooms.php">
                        <i class="bi bi-door-open me-2"></i> Rooms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="/hospital-scheduler/admin/settings.php">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
            <?php elseif ($role === 'doctor'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'calendar.php' ? 'active' : ''; ?>" href="/hospital-scheduler/doctor/calendar.php">
                        <i class="bi bi-calendar-week me-2"></i> Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>" href="/hospital-scheduler/doctor/appointments.php">
                        <i class="bi bi-list-check me-2"></i> Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" href="/hospital-scheduler/doctor/profile.php">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                </li>
            <?php elseif ($role === 'patient'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'book.php' ? 'active' : ''; ?>" href="/hospital-scheduler/patient/book.php">
                        <i class="bi bi-plus-circle me-2"></i> Book Appointment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'appointments.php' ? 'active' : ''; ?>" href="/hospital-scheduler/patient/appointments.php">
                        <i class="bi bi-calendar-check me-2"></i> My Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" href="/hospital-scheduler/patient/profile.php">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="/hospital-scheduler/auth/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

# Hospital Scheduling System

A complete web application for managing hospital appointments, built with PHP, MySQL, and Bootstrap 5.

## Setup Instructions

1.  **Copy Files**: Ensure this folder is located at `C:\xampp\htdocs\hospital-scheduler`.
2.  **Start XAMPP**: Open XAMPP Control Panel and start **Apache** and **MySQL**.
3.  **Database Setup**:
    *   Open your browser and go to `http://localhost/phpmyadmin`.
    *   Create a new database named `hospital_scheduler`.
    *   Import the `schema.sql` file located in the root of this project.
    *   *Alternatively*, run via CLI: `mysql -u root -p hospital_scheduler < schema.sql`
4.  **Configuration**:
    *   Check `includes/db.php`. The default settings are configured for XAMPP (User: `root`, Password: ``). Update if necessary.

## Usage

1.  **Access the App**: Go to `http://localhost/hospital-scheduler/index.php`.
2.  **Login**:
    *   **Admin**: `admin@hospital.local` / `Admin123!`
    *   **Doctor**: `house@hospital.local` / `Admin123!`
    *   **Patient**: `john@example.com` / `Admin123!`

## Features

*   **Role-based Access**: Admin, Doctor, Patient dashboards.
*   **Appointments**: Booking, rescheduling, cancellation.
*   **Calendar**: Interactive calendar for doctors and admins.
*   **Rooms & Services**: Management of hospital resources.

## Developer Notes
*   **Security**: Passwords are hashed using `password_hash()`. CSRF protection is implemented on forms.

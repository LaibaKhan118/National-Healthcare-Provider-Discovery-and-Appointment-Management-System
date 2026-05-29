# National Healthcare Provider Discovery & Appointment Management System

> A full-stack web application for discovering doctors, booking appointments, and managing healthcare workflows — built with Laravel 12 and MySQL.

---

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Database Design](#database-design)
- [Getting Started](#getting-started)
- [Demo Credentials](#demo-credentials)
- [Project Structure](#project-structure)
- [Team](#team)

---

## About the Project

This system was developed as a **Database Systems (CS 2005) course project** at FAST-NUCES Karachi. It addresses the challenge patients face when trying to find qualified doctors — no centralized platform exists to compare specialists by experience, fees, location, and verified patient reviews.

The system supports three user roles:

- **Admin** — manages doctor verifications, reviews, specializations, and hospitals
- **Doctor** — manages profile, availability, appointments, and medical notes
- **Patient** — searches for doctors, books appointments, and leaves reviews

---

## Features

### 👤 Authentication

- Separate registration for doctors and patients
- Role-based login with redirection
- Bcrypt password hashing
- Account suspension management

### 🛡️ Admin Portal

- Approve or reject doctor registration requests
- Suspend and reactivate doctor accounts
- Moderate patient reviews
- Manage specializations and hospital directory
- Verify new hospitals submitted by doctors
- System-wide statistics dashboard

### 🩺 Doctor Portal

- Complete profile with bio, experience, fees, and specializations
- Select from existing hospitals or submit a new one for admin verification
- Manage weekly availability time slots (Mon–Sun)
- View and filter appointments by status
- Mark appointments as completed, cancelled, or no-show
- Add medical notes after completing an appointment
- Pending verification redirect — must complete profile before accessing dashboard

### 🧑‍💼 Patient Portal

- Search doctors by specialization, city, fee range, and minimum rating
- View doctor profiles with average rating and patient reviews
- Book appointments from available time slots
- Cancel pending appointments (slot is automatically released)
- Leave a review only after a completed appointment
- View full appointment history with medical notes

---

## Tech Stack

| Layer         | Technology                        |
| ------------- | --------------------------------- |
| Backend       | PHP 8.2, Laravel 12               |
| Database      | MySQL 8.0                         |
| Frontend      | Blade Templates, Bootstrap 5.3.3  |
| Auth          | Laravel Session Auth (custom)     |
| Query Builder | `DB::table()` (raw query builder) |
| Server        | Apache via Laragon                |

---

## Database Design

### Normalization

All 12 tables are normalized to **Boyce-Codd Normal Form (BCNF)**.

### Tables (12 total)

| Table                    | Description                                       |
| ------------------------ | ------------------------------------------------- |
| `users`                  | Stores login credentials for all roles            |
| `admins`                 | Admin profile data                                |
| `doctors`                | Doctor profile and verification status            |
| `patients`               | Patient demographics                              |
| `specializations`        | Master list of medical specialties                |
| `doctor_specializations` | M:N junction — doctor ↔ specialization            |
| `hospitals`              | Hospital directory with pending verification flag |
| `doctor_hospitals`       | M:N junction — doctor ↔ hospital                  |
| `availability`           | Doctor weekly time slots                          |
| `appointments`           | Appointment bookings and status                   |
| `appointment_notes`      | Medical notes written by doctors                  |
| `reviews`                | Patient ratings and comments                      |

### Key Constraints

- **Soft Deletes** — All tables have `deleted_at` for data retention
- **ON DELETE CASCADE** — Used for most relationships
- **ON DELETE SET NULL** — Used for `patient_id` in appointments and reviews (preserves history when patient is deleted)
- **Unique** — `users.email`, `doctors.license_number`, `reviews.appointment_id`, `appointment_notes.appointment_id`

---

## Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL 8.0+
- [Laragon](https://laragon.org/) (recommended) or any Apache/Nginx server

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/your-username/national-healthcare-system.git
cd national-healthcare-system
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Set up environment file**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure your database in `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=national_healthcare_system
DB_USERNAME=root
DB_PASSWORD=
```

**5. Create the database**

```sql
CREATE DATABASE national_healthcare_system;
```

**6. Run migrations**

```bash
php artisan migrate
```

**7. Seed demo data**

```bash
php artisan db:seed
```

**8. Start the development server**

```bash
php artisan serve
```

**9. Visit the application**

```
http://127.0.0.1:8000
```

---

## Demo Credentials

| Role    | Email                   | Password |
| ------- | ----------------------- | -------- |
| Admin   | admin@healthcare.com    | password |
| Doctor  | doctor1@healthcare.com  | password |
| Doctor  | doctor2@healthcare.com  | password |
| Patient | patient1@healthcare.com | password |
| Patient | patient2@healthcare.com | password |

---

## Project Structure

```
national-healthcare-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── AdminDoctorController.php
│   │   │   │   ├── AdminReviewController.php
│   │   │   │   ├── AdminSpecializationController.php
│   │   │   │   └── AdminHospitalController.php
│   │   │   ├── Doctor/
│   │   │   │   ├── DoctorDashboardController.php
│   │   │   │   ├── DoctorProfileController.php
│   │   │   │   ├── DoctorAvailabilityController.php
│   │   │   │   └── DoctorAppointmentController.php
│   │   │   ├── Patient/
│   │   │   │   ├── PatientDashboardController.php
│   │   │   │   ├── PatientSearchController.php
│   │   │   │   ├── PatientAppointmentController.php
│   │   │   │   ├── PatientReviewController.php
│   │   │   │   └── PatientProfileController.php
│   │   │   └── Auth/
│   │   │       ├── LoginController.php
│   │   │       └── RegisterController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       ├── DoctorMiddleware.php
│   │       └── PatientMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Admin.php
│       ├── Doctor.php
│       ├── Patient.php
│       ├── Appointment.php
│       ├── Review.php
│       ├── Availability.php
│       ├── Specialization.php
│       ├── Hospital.php
│       └── AppointmentNote.php
├── database/
│   ├── migrations/          # 13 migration files
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── admin/
│       ├── doctor/
│       ├── patient/
│       └── auth/
├── routes/
│   └── web.php
├── .env.example
└── README.md
```

---

## Team

| Name               | Role                     |
| ------------------ | ------------------------ |
| Laiba Khan         | Team Lead / Backend      |
| Abeeha Binte Aamer | Backend / Database       |
| Aamna Rizwan       | Frontend / Documentation |

---

## Academic Context

This project demonstrates the following database concepts:

- ✅ **Normalization** up to BCNF across all 12 tables
- ✅ **Database Views** — 5 views for optimized reporting queries
- ✅ **Triggers** — 5 triggers for data integrity and automation
- ✅ **Transactions** — `BEGIN / COMMIT / ROLLBACK` on all critical operations
- ✅ **Joins** — INNER JOIN and LEFT JOIN across multiple tables
- ✅ **Aggregate Functions** — `COUNT`, `AVG`, `SUM`, `GROUP_CONCAT`
- ✅ **Soft Deletes** — `deleted_at` on all tables for data retention
- ✅ **Role-based programs** — Separate controllers and logic per user role

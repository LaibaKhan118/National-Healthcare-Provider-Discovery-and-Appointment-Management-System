<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminSpecializationController;
use App\Http\Controllers\Admin\AdminHospitalController;

use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Doctor\DoctorProfileController;
use App\Http\Controllers\Doctor\DoctorAvailabilityController;
use App\Http\Controllers\Doctor\DoctorAppointmentController;

use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\Patient\PatientSearchController;
use App\Http\Controllers\Patient\PatientAppointmentController;
use App\Http\Controllers\Patient\PatientReviewController;
use App\Http\Controllers\Patient\PatientProfileController;

// Public Routes
Route::get('/', function () {
    return redirect()->route('welcome');
});

Route::get('/welcome', [PatientSearchController::class, 'welcome'])->name('welcome');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post')->middleware('guest');

// Admin Routes
Route::middleware(['auth', 'admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/doctors', [AdminDoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/{doctor}', [AdminDoctorController::class, 'show'])->name('doctors.show');
    Route::post('/doctors/{doctor}/approve', [AdminDoctorController::class, 'approve'])->name('doctors.approve');
    Route::post('/doctors/{doctor}/suspend', [AdminDoctorController::class, 'suspend'])->name('doctors.suspend');
    Route::post('/doctors/{doctor}/reactivate', [AdminDoctorController::class, 'reactivate'])->name('doctors.reactivate');
    Route::delete('/doctors/{doctor}', [AdminDoctorController::class, 'destroy'])->name('doctors.destroy');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/specializations', [AdminSpecializationController::class, 'index'])->name('specializations.index');
    Route::post('/specializations', [AdminSpecializationController::class, 'store'])->name('specializations.store');
    Route::put('/specializations/{specialization}', [AdminSpecializationController::class, 'update'])->name('specializations.update');
    Route::delete('/specializations/{specialization}', [AdminSpecializationController::class, 'destroy'])->name('specializations.destroy');

    Route::get('/hospitals', [AdminHospitalController::class, 'index'])->name('hospitals.index');
    Route::post('/hospitals', [AdminHospitalController::class, 'store'])->name('hospitals.store');
    Route::put('/hospitals/{hospital}', [AdminHospitalController::class, 'update'])->name('hospitals.update');
    Route::delete('/hospitals/{hospital}', [AdminHospitalController::class, 'destroy'])->name('hospitals.destroy');

    Route::post('/hospitals/{id}/verify', [AdminHospitalController::class, 'verify'])->name('admin.hospitals.verify');
});

// Doctor Routes
Route::middleware(['auth', 'doctor'])
     ->prefix('doctor')
     ->name('doctor.')
     ->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/edit', [DoctorProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');

    Route::get('/availability', [DoctorAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [DoctorAvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{slot}', [DoctorAvailabilityController::class, 'destroy'])->name('availability.destroy');

    Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/mark', [DoctorAppointmentController::class, 'mark'])->name('appointments.mark');
    Route::post('/appointments/{appointment}/note', [DoctorAppointmentController::class, 'addNote'])->name('appointments.addNote');
});

// Patient Routes
Route::middleware(['auth', 'patient'])
     ->prefix('patient')
     ->name('patient.')
     ->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

    Route::get('/search', [PatientSearchController::class, 'search'])->name('search');
    Route::get('/doctors/{doctor}', [PatientSearchController::class, 'showDoctor'])->name('doctors.show');

    Route::post('/appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');

    Route::get('/appointments/{appointment}/review/create', [PatientReviewController::class, 'create'])->name('reviews.create');
    Route::post('/appointments/{appointment}/review', [PatientReviewController::class, 'store'])->name('reviews.store');

    Route::get('/profile/edit', [PatientProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [PatientProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [PatientProfileController::class, 'destroy'])->name('profile.destroy');
});

// Redirect authenticated users to dashboard
Route::redirect('/home', '/admin/dashboard');

<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles (if not using migrations)
        DB::table('users')->insert([
            // Admin User
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@healthcare.com',
                'password_hash' => password_hash('password', PASSWORD_BCRYPT),
                'role_id' => 1,
                'account_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Doctor 1
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Khan',
                'email' => 'doctor1@healthcare.com',
                'password_hash' => password_hash('password', PASSWORD_BCRYPT),
                'role_id' => 2,
                'account_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Doctor 2
            [
                'first_name' => 'Fatima',
                'last_name' => 'Ali',
                'email' => 'doctor2@healthcare.com',
                'password_hash' => password_hash('password', PASSWORD_BCRYPT),
                'role_id' => 2,
                'account_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Patient 1
            [
                'first_name' => 'Hassan',
                'last_name' => 'Ahmed',
                'email' => 'patient1@healthcare.com',
                'password_hash' => password_hash('password', PASSWORD_BCRYPT),
                'role_id' => 3,
                'account_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Patient 2
            [
                'first_name' => 'Sara',
                'last_name' => 'Hassan',
                'email' => 'patient2@healthcare.com',
                'password_hash' => password_hash('password', PASSWORD_BCRYPT),
                'role_id' => 3,
                'account_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Admin record
        DB::table('admins')->insert([
            'user_id' => 1,
        ]);

        // Create Doctor records
        DB::table('doctors')->insert([
            [
                'user_id' => 2,
                'license_number' => 'DL-001-2024',
                'specialization' => 'Cardiology',
                'experience_years' => 15,
                'consultation_fee' => 2000,
                'bio' => 'Experienced cardiologist with over 15 years of practice.',
                'city' => 'Karachi',
                'hospital_affiliation' => 'National Hospital',
                'is_verified' => 1,
            ],
            [
                'user_id' => 3,
                'license_number' => 'DL-002-2024',
                'specialization' => 'General Medicine',
                'experience_years' => 12,
                'consultation_fee' => 1500,
                'bio' => 'General physician with expertise in chronic disease management.',
                'city' => 'Lahore',
                'hospital_affiliation' => 'City Hospital',
                'is_verified' => 1,
            ],
        ]);

        // Create Patient records
        DB::table('patients')->insert([
            [
                'user_id' => 4,
                'phone' => '03001234567',
                'address' => '123 Main Street, Karachi',
                'date_of_birth' => '1990-05-15',
            ],
            [
                'user_id' => 5,
                'phone' => '03009876543',
                'address' => '456 Park Road, Lahore',
                'date_of_birth' => '1995-03-20',
            ],
        ]);

        // Create Specializations
        DB::table('specializations')->insert([
            ['specialization_name' => 'Cardiology'],
            ['specialization_name' => 'Dermatology'],
            ['specialization_name' => 'Orthopedics'],
            ['specialization_name' => 'Neurology'],
            ['specialization_name' => 'General Medicine'],
            ['specialization_name' => 'Pediatrics'],
            ['specialization_name' => 'Psychiatry'],
            ['specialization_name' => 'Ophthalmology'],
        ]);

        // Link doctor specializations
        DB::table('doctor_specializations')->insert([
            ['doctor_id' => 1, 'specialization_id' => 1], // Ahmed - Cardiology
            ['doctor_id' => 2, 'specialization_id' => 5], // Fatima - General Medicine
        ]);

        // Create Hospitals
        DB::table('hospitals')->insert([
            [
                'hospital_name' => 'National Hospital',
                'city' => 'Karachi',
                'address' => '10 Hospital Road, Karachi',
            ],
            [
                'hospital_name' => 'City Hospital',
                'city' => 'Lahore',
                'address' => '20 Medical Avenue, Lahore',
            ],
            [
                'hospital_name' => 'Central Medical Center',
                'city' => 'Islamabad',
                'address' => '30 Health Street, Islamabad',
            ],
        ]);

        // Link doctor hospitals
        DB::table('doctor_hospitals')->insert([
            ['doctor_id' => 1, 'hospital_id' => 1],
            ['doctor_id' => 2, 'hospital_id' => 2],
        ]);

        // Create Availability (Weekly slots)
        $daysWeek = [1, 2, 3, 4, 5]; // Mon-Fri
        foreach ($daysWeek as $day) {
            // Doctor 1 slots
            DB::table('availability')->insert([
                'doctor_id' => 1,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '12:00:00',
                'is_booked' => 0,
            ]);
            DB::table('availability')->insert([
                'doctor_id' => 1,
                'day_of_week' => $day,
                'start_time' => '14:00:00',
                'end_time' => '17:00:00',
                'is_booked' => 0,
            ]);

            // Doctor 2 slots
            DB::table('availability')->insert([
                'doctor_id' => 2,
                'day_of_week' => $day,
                'start_time' => '10:00:00',
                'end_time' => '13:00:00',
                'is_booked' => 0,
            ]);
            DB::table('availability')->insert([
                'doctor_id' => 2,
                'day_of_week' => $day,
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'is_booked' => 0,
            ]);
        }

        // Create Sample Appointments
        DB::table('appointments')->insert([
            [
                'doctor_id' => 1,
                'patient_id' => 1,
                'appointment_date' => now()->subDays(5)->toDateString(),
                'appointment_time' => '10:00:00',
                'appointment_status' => 'completed',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(5),
            ],
            [
                'doctor_id' => 2,
                'patient_id' => 2,
                'appointment_date' => now()->addDays(3)->toDateString(),
                'appointment_time' => '15:00:00',
                'appointment_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create Sample Reviews
        DB::table('reviews')->insert([
            [
                'appointment_id' => 1,
                'patient_id' => 1,
                'doctor_id' => 1,
                'rating' => 5,
                'comment' => 'Excellent doctor with great bedside manner. Highly recommended!',
                'created_at' => now()->subDays(4),
            ],
        ]);

        // Create Sample Notes
        DB::table('appointment_notes')->insert([
            [
                'appointment_id' => 1,
                'note_content' => 'Patient presenting with chest pain. EKG normal. Recommended rest and follow-up in 2 weeks. Prescribed aspirin 100mg daily.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ]);
    }
}

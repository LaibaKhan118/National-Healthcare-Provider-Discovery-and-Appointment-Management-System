<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to users table
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to admins table
        Schema::table('admins', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to specializations table
        Schema::table('specializations', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to hospitals table
        Schema::table('hospitals', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to availability table
        Schema::table('availability', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add to appointment_notes table
        Schema::table('appointment_notes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('availability', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('appointment_notes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
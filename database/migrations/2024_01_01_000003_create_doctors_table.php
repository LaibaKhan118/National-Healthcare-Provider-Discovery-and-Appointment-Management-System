<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id('doctor_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('license_number', 50)->nullable();
            $table->string('specialization', 100)->nullable();
            $table->integer('experience_years')->nullable();
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->longText('bio')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('hospital_affiliation', 200)->nullable();
            $table->boolean('is_verified')->default(0);
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
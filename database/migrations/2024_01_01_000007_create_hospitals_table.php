<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id('hospital_id');
            $table->string('hospital_name', 200);
            $table->string('city', 100)->nullable();
            $table->longText('address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
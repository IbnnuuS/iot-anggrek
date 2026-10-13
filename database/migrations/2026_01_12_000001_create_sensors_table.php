<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Identitas perangkat
            $table->string('name', 100);
            $table->string('model', 50)->nullable();
            $table->string('serial_no', 80)->nullable();
            $table->string('mac_address', 32)->nullable()->unique();

            // Lokasi
            $table->string('site_name', 100)->nullable();
            $table->string('area_name', 100)->nullable();
            $table->string('zone_label', 60)->nullable();
            $table->string('rack_label', 60)->nullable();
            $table->string('notes', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};

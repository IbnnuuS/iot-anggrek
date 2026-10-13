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
        Schema::create('pengukuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained()->cascadeOnDelete();
            $table->dateTime('recorded_at');

            $table->decimal('temperature_c', 5, 2);
            $table->decimal('humidity_pct', 5, 2);

            $table->index(['sensor_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengukuran');
    }
};

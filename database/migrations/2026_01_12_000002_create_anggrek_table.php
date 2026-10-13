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
        Schema::create('anggrek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sensor_id')->nullable()->constrained()->nullOnDelete();

            $table->string('jenis_anggrek', 100);
            $table->string('nama_anggrek', 100)->nullable();

            $table->decimal('temp_min_c', 5, 2)->nullable();
            $table->decimal('temp_max_c', 5, 2)->nullable();
            $table->decimal('hum_min_pct', 5, 2)->nullable();
            $table->decimal('hum_max_pct', 5, 2)->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('sensor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggrek');
    }
};

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
        Schema::table('pengukuran', function (Blueprint $table) {
            $table->decimal('soil_moisture_pct', 5, 2)->nullable()->after('humidity_pct');
            $table->decimal('soil_temperature_c', 5, 2)->nullable()->after('soil_moisture_pct');
            $table->integer('soil_ec')->nullable()->after('soil_temperature_c');
            $table->decimal('soil_ph', 4, 2)->nullable()->after('soil_ec');
            $table->integer('nitrogen')->nullable()->after('soil_ph');
            $table->integer('phosphorus')->nullable()->after('nitrogen');
            $table->integer('potassium')->nullable()->after('phosphorus');
            $table->string('pump_status')->nullable()->after('potassium');
            $table->string('kondisi_lahan')->nullable()->after('pump_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengukuran', function (Blueprint $table) {
            $table->dropColumn([
                'soil_moisture_pct',
                'soil_temperature_c',
                'soil_ec',
                'soil_ph',
                'nitrogen',
                'phosphorus',
                'potassium',
                'pump_status',
                'kondisi_lahan',
            ]);
        });
    }
};

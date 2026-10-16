<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengukuran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengukuran';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'sensor_id',
        'recorded_at',
        'temperature_c',
        'humidity_pct',
        'lux',
        'soil_moisture_pct',
        'soil_temperature_c',
        'soil_ec',
        'soil_ph',
        'nitrogen',
        'phosphorus',
        'potassium',
        'pump_status',
        'kondisi_lahan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'temperature_c' => 'decimal:2',
            'humidity_pct' => 'decimal:2',
            'lux' => 'decimal:1',
            'soil_moisture_pct' => 'decimal:2',
            'soil_temperature_c' => 'decimal:2',
            'soil_ec' => 'integer',
            'soil_ph' => 'decimal:2',
            'nitrogen' => 'integer',
            'phosphorus' => 'integer',
            'potassium' => 'integer',
        ];
    }

    /**
     * Get the sensor that owns the pengukuran.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'model',
        'serial_no',
        'mac_address',
        'site_name',
        'area_name',
        'zone_label',
        'rack_label',
        'notes',
    ];

    /**
     * Get the user that owns the sensor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the anggrek records associated with the sensor.
     */
    public function anggrek(): HasMany
    {
        return $this->hasMany(Anggrek::class);
    }

    /**
     * Get the pengukuran (measurements) for the sensor.
     */
    public function pengukuran(): HasMany
    {
        return $this->hasMany(Pengukuran::class);
    }
}

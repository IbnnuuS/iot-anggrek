<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggrek extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anggrek';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'sensor_id',
        'jenis_anggrek',
        'nama_anggrek',
        'temp_min_c',
        'temp_max_c',
        'hum_min_pct',
        'hum_max_pct',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'temp_min_c' => 'decimal:2',
            'temp_max_c' => 'decimal:2',
            'hum_min_pct' => 'decimal:2',
            'hum_max_pct' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the anggrek.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sensor associated with the anggrek.
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}

<?php

namespace App\Models;

use App\Contracts\Models\PrizeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class Prize extends Model implements PrizeContract
{
    protected $fillable = [
        'campaign_id',
        'name',
        'tile_image',
        'description',
        'level',
        'weight',
        'daily_volume',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::query()->whereRaw('name LIKE %?%', [$query]);
    }

    public function getCacheKey(): string
    {
        return $this->attributes['id'] . '-' . date('Y-m-d');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}

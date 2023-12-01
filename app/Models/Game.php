<?php

namespace App\Models;

use App\Contracts\Models\GameContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model implements GameContract
{
    use HasFactory;

    protected $fillable = ['campaign_id', 'prize_id', 'account', 'revealed_tiles', 'revealed_at'];

    protected $casts = [
        'revealed_at' => 'datetime',
        'revealed_tiles' => 'json'
    ];

    public static function filter(?string $account = null, ?int $prizeId = null, ?string $fromDate = null, ?string $tillDate = null, ?int $campaignId = null): Builder
    {
        $query = self::query();
        $campaign = Campaign::query()->find($campaignId);

        // When filtering by dates, keep in mind `revealed_at` should be stored in Campaign timezone

        return $query;
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }
}

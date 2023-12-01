<?php

namespace App\Models;

use App\Actions\Games\ConvertsTimestampToTimezone;
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
        /** @var Campaign $campaign */
        $campaign = Campaign::query()->select('timezone')->find($campaignId);

        /** @var ConvertsTimestampToTimezone $timezoneConverter */
        $timezoneConverter = resolve(ConvertsTimestampToTimezone::class);

        return self::query()
            ->when(
                $account,
                fn (Builder $q, $account) => $q->whereRaw("account LIKE ?", ["%{$account}%"])
            )
            ->when(
                $prizeId,
                fn (Builder $q, $prizeId) => $q->where('prize_id', '=', $prizeId)
            )
            ->when(
                $fromDate,
                fn (Builder $q, $fromDate) => $q->where('revealed_at', '>=', $timezoneConverter->execute($fromDate, $campaign->timezone))
            )
            ->when(
                $tillDate,
                fn (Builder $q, $tillDate) => $q->where('revealed_at', '<=', $timezoneConverter->execute($tillDate, $campaign->timezone))
            );
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

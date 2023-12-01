<?php

namespace App\Actions\Games;

use App\Actions\Prizes\GetRandomPrize;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FindUnfinishedGameOrInitiateOne
{
    public function __construct(public GetRandomPrize $getRandomPrize)
    {}

    public function execute(string $account, int $campaignId, string $segment = 'low'): Game|Model|null
    {
        $game = Game::query()
            ->when($segment, fn (Builder $q, $segment) => $q->whereRelation('prize', 'level', $segment))
            ->where([
                'account' => $account,
                'campaign_id' => $campaignId
            ])
            ->with('prize')
            ->whereNull('revealed_at')
            ->first();

        if (!empty($game)) {
            return $game;
        }

        $prize = $this->getRandomPrize->execute($segment, ['id', 'daily_volume']);

        $game = Game::query()
            ->create([
                'account' => $account,
                'campaign_id' => $campaignId,
                'prize_id' => $prize->id
            ]);

        /*
         * Performance TIP:
         * No need to load again the prize model
         * Just by assigning, it will be accessible
         * exactly like ->load('prize')
         */
        $game->prize = $prize;

        return $game;
    }
}

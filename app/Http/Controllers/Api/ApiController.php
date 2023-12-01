<?php

namespace App\Http\Controllers\Api;

use App\Actions\Games\CheckGameCompletion;
use App\Actions\Prizes\CheckDailyVolume;
use App\Http\Controllers\Controller;
use App\Models\Game;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function flip(CheckDailyVolume $checkDailyVolume, CheckGameCompletion $gameCompletion)
    {
        $data = request()->validate([
            'gameId' => 'required',
            'tileIndex' => 'required|integer|min:0|max:24'
        ]);

        /** @var Game $game */
        $game = Game::query()
            ->with('campaign:id,timezone')
            ->with('prize:id,tile_image,daily_volume')
            ->select('id', 'campaign_id', 'prize_id', 'revealed_tiles')
            ->findOrFail($data['gameId']);

        $prize = $game->prize;
        $campaign = $game->campaign;

        if (! empty($prize->daily_volume)) {

            $dailyVolume = $checkDailyVolume->execute($prize->getCacheKey(), $prize->daily_volume);

            if ($dailyVolume->isInvalid()) {
                return [
                    'message' => $dailyVolume->message()
                ];
            }

            $dailyVolume->increaseVolume();
        }

        $revealedTiles = $game->revealed_tiles ?? [];

        $revealedTiles[] = [
            'index' => $data['tileIndex'],
            'image' => $prize->tile_image
        ];

        $game->revealed_tiles = $revealedTiles;

        $gameCompletion = $gameCompletion->execute($revealedTiles);

        if ($gameCompletion->isComplete()) {
            $game->revealed_at = Carbon::now()->setTimezone($campaign->timezone)->format('d-m-Y H:i:s');
        }

        $game->save();

        return [
            'tileImage' => $prize->tile_image,
            'message' => $gameCompletion->message()
        ];
    }
}

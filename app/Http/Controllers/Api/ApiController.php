<?php

namespace App\Http\Controllers\Api;

use App\Actions\Prizes\CheckDailyVolume;
use App\Http\Controllers\Controller;
use App\Models\Game;

class ApiController extends Controller
{
    public function flip(CheckDailyVolume $checkDailyVolume)
    {
        $data = request()->validate([
            'gameId' => 'required',
            'tileIndex' => 'required|number|min:0|max:24'
        ]);

        /** @var Game $game */
        $game = Game::query()
            ->with('prize:id,tile_image,daily_volume')
            ->select('id', 'prize_id', 'revealed_tiles')
            ->findOrFail($data['gameId']);

        $prize = $game->prize;

        if (! empty($prize->daily_volume)) {

            $dailyVolume = $checkDailyVolume->execute($prize->getCacheKey(), $prize->daily_volume);

            if ($dailyVolume->isInvalid()) {
                return [
                    'message' => $dailyVolume->message()
                ];
            }
        }

        $revealedTiles = $game->revealed_tiles ?? [];

        $revealedTiles[] = [
            'index' => $data['tileIndex'],
            'image' => $prize->tile_image
        ];

        $game->update([
            'revealed_tiles' => $revealedTiles
        ]);

        $currentMove = count($revealedTiles);

        return [
            'tileImage' => $prize->tile_image,
        ] + ($currentMove >= 3 ? ['message' => $prize->description ?? trans('prize.default_win_message')] : []);
    }
}

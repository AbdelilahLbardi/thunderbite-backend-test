<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CheckIfCampaignTimeIsInvalid;
use App\Actions\Games\CheckGameCompletion;
use App\Actions\Games\FindUnfinishedGameOrInitiateOne;
use App\Actions\Prizes\CheckDailyVolume;
use App\Models\Campaign;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function __construct(
        public CheckIfCampaignTimeIsInvalid $campaignTimeIsInvalid,
        public FindUnfinishedGameOrInitiateOne $findUnfinishedGameOrInitiateOne,
        public CheckDailyVolume $checkDailyVolume,
        public CheckGameCompletion $checkGameCompletion
    ){}

    public function loadCampaign(Campaign $campaign): View
    {
        if ($this->campaignTimeIsInvalid->execute($campaign->starts_at, $campaign->ends_at, $campaign->timezone)) {
            return view('frontend.index', ['config' => json_encode([
                'message' => trans('campaign.expired')
            ])]);
        }

        $data = request()->validate([
            'a' => 'required',
            'segment' => 'nullable|in:low,med,high'
        ]);

        $game = $this->findUnfinishedGameOrInitiateOne->execute($data['a'], $campaign->id, $data['segment'] ?? 'low');

        $prize = $game->prize;

        if (!empty($prize->daily_volume) ) {

            $dailyVolume = $this->checkDailyVolume->execute($prize->getCacheKey(), $prize->daily_volume);

            if ($dailyVolume->isInvalid()) {
                return view('frontend.index', ['config' => json_encode([
                    'message' => $dailyVolume->message()
                ])]);
            }
        }

        $revealedTiles = $game->revealed_tiles ?? [];

        $gameCompletion = $this->checkGameCompletion->execute($revealedTiles);

        $jsonConfig = [
            'apiPath' => '/api/flip',
            'gameId' => $game->id,
            'reveledTiles' => $revealedTiles,
            'message' => $gameCompletion->message()
        ];

        return view('frontend.index', ['config' => json_encode($jsonConfig)]);
    }

    public function placeholder(): View
    {
        return view('frontend.placeholder');
    }
}

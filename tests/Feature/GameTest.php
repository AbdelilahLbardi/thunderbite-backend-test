<?php

namespace Tests\Feature;

use App\Actions\Games\CheckGameCompletion;
use App\Models\Campaign;
use Database\Factories\CampaignFactory;
use Database\Factories\PrizeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\Mocks;

class GameTest extends TestCase
{
    use RefreshDatabase, Mocks;

    protected Campaign $campaign;
    protected CheckGameCompletion $checkGameCompletion;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2023-12-01 01:30:00');

        $this->campaign = CampaignFactory::new()->create();
        $this->checkGameCompletion = resolve(CheckGameCompletion::class);
    }

    public function test_account_is_required()
    {
        $this->loadCampaignEndpoint($this->campaign->slug, '')
            ->assertSessionHasErrors('a');
    }

    public function test_loading_campaign_with_invalid_time_returns_errors_message()
    {
        $this->campaign->update([
            'starts_at' => now()->addHour()->toDateTimeString()
        ]);

        $this->loadCampaignEndpoint($this->campaign->slug, 'test-account')
            ->assertViewIs('frontend.index')
            ->assertViewHas('config', json_encode([
                'message' => trans('campaign.expired')
            ]));
    }

    public function test_game_cannot_be_played_when_prize_daily_volume_has_exceeded()
    {
        $prize = PrizeFactory::new()
            ->campaign($this->campaign)
            ->create([
                'daily_volume' => 2
            ]);

        cache([$prize->getCacheKey() => 2]);

        $this->mockRandomPrizeAction();

        $this->loadCampaignEndpoint($this->campaign->slug, 'test-account')
            ->assertViewIs('frontend.index')
            ->assertViewHas('config', json_encode([
                'message' => trans('prize.daily_volume_exceeded', ['hours' => 22, 'minutes' => 30])
            ]));
    }

    private function loadCampaignEndpoint(string $campaignSlug, string $a, string $segment = 'low'): TestResponse
    {
        return $this->get(
            $campaignSlug . '/?' . http_build_query(compact('a', 'segment'))
        );
    }
}

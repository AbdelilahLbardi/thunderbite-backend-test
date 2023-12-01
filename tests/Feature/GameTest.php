<?php

use App\Actions\Games\CheckGameCompletion;
use App\Models\Game;
use Database\Factories\CampaignFactory;
use Database\Factories\PrizeFactory;
use Illuminate\Support\Carbon;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

beforeEach(function () {
    Carbon::setTestNow('2023-12-01 01:30:00');

    $this->campaign = CampaignFactory::new()->create();
    $this->checkGameCompletion = resolve(CheckGameCompletion::class);
});

test('account is required', function () {
    loadCampaignEndpoint($this->campaign->slug, '')->assertSessionHasErrors('a');
});

test('loading campaign with invalid time returns error message', function () {
    $this->campaign->update([
        'starts_at' => now()->addHour()->toDateTimeString()
    ]);

    loadCampaignEndpoint($this->campaign->slug, 'test-account')
        ->assertViewIs('frontend.index')
        ->assertViewHas('config', json_encode([
            'message' => trans('campaign.expired')
        ]));
});

test('game cannot be played when price daily volume has exceeded', function () {
    $prize = PrizeFactory::new()
        ->campaign($this->campaign)
        ->create([
            'daily_volume' => 2
        ]);

    cache([$prize->getCacheKey() => 2]);

    $this->mockRandomPrizeAction();

    loadCampaignEndpoint($this->campaign->slug, 'test-account')
        ->assertViewIs('frontend.index')
        ->assertViewHas('config', json_encode([
            'message' => trans('prize.daily_volume_exceeded', ['hours' => 22, 'minutes' => 30])
        ]));
});

test('game can be played', function () {
    assertDatabaseCount('games', 0);

    $prize = PrizeFactory::new()
        ->campaign($this->campaign)
        ->create([
            'tile_image' => 'fake_tile_image.png',
            'daily_volume' => 100
        ]);

    $this->mockRandomPrizeAction();

    $response = loadCampaignEndpoint($this->campaign->slug, 'test-account');

    $game = Game::query()->first();

    $response->assertViewIs('frontend.index')
        ->assertViewHas('config', json_encode([
            'apiPath' => $endpoint = '/api/flip',
            'gameId' => $game->id,
            'reveledTiles' => [],
            'message' => null
        ]));

    assertFalse(cache()->offsetExists($prize->getCacheKey()));

    flipTile($endpoint, $game->id, rand(0, 24));
    assertNull($game->fresh()->revealed_at);
    assertSame(1, cache($prize->getCacheKey()));

    flipTile($endpoint, $game->id, rand(0, 24));
    assertNull($game->fresh()->revealed_at);
    assertSame(2, cache($prize->getCacheKey()));

    //Checking if the homepage returns progress

    loadCampaignEndpoint($this->campaign->slug, 'test-account')
        ->assertViewHas('config', json_encode([
            'apiPath' => '/api/flip',
            'gameId' => $game->id,
            'reveledTiles' => $game->fresh()->revealed_tiles,
            'message' => null
        ]));

    flipTile($endpoint, $game->id, rand(0, 24))
        ->assertExactJson([
            'tileImage' => $prize->tile_image,
            'message' => trans('prize.default_win_message')
        ]);
    assertNotNull($game->fresh()->revealed_at);
    assertSame(3, cache($prize->getCacheKey()));
});

<?php

use App\Actions\Games\FindUnfinishedGameOrInitiateOne;
use Database\Factories\CampaignFactory;
use Database\Factories\GameFactory;
use Database\Factories\PrizeFactory;
use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    $this->findUnfinishedGameOrInitiateOne = resolve(FindUnfinishedGameOrInitiateOne::class);

    $this->campaign = CampaignFactory::new()->create();
    $this->prize = PrizeFactory::new()
        ->campaign($this->campaign)
        ->highSegment()
        ->create();
});

test('new account game is created when no previous one exists', function () {

    assertDatabaseCount('games', 0);

    $this->mockRandomPrizeAction();

    resolve(FindUnfinishedGameOrInitiateOne::class)->execute('test-account', $this->campaign->id, 'high');

    assertDatabaseCount('games', 1);

});

test('old account game is retrieved when it is not revealed', function () {

    GameFactory::new()
        ->campaign($this->campaign)
        ->prize($this->prize)
        ->unrevealed()
        ->create([
            'account' => 'test-account'
        ]);

    $this->assertDatabaseCount('games', 1);

    $this->findUnfinishedGameOrInitiateOne->execute('test-account', $this->campaign->id, 'high');

    $this->assertDatabaseCount('games', 1);

});

test('new account game created when previous one is revealed', function () {

    GameFactory::new()->create();

    $this->assertDatabaseCount('games', 1);

    $this->mockRandomPrizeAction();

    resolve(FindUnfinishedGameOrInitiateOne::class)->execute('test-account', $this->campaign->id, 'high');

    $this->assertDatabaseCount('games', 2);

});

test('new game is created when the existing account game prize level is different', function () {

    $this->assertDatabaseCount('prizes', 1);

    GameFactory::new()
        ->prize(
            PrizeFactory::new()
                ->campaign($this->campaign)
                ->lowSegment()
                ->create()
        )
        ->create();

    $this->assertDatabaseCount('prizes', 2);

    $this->assertDatabaseCount('games', 1);

    $this->mockRandomPrizeAction();

    resolve(FindUnfinishedGameOrInitiateOne::class)->execute('test-account', $this->campaign->id, 'high');

    $this->assertDatabaseCount('games', 2);

});

test('new game is created when the existing account game prize level is same but revealed', function () {
    $this->assertDatabaseCount('prizes', 1);

    GameFactory::new()
        ->prize(
            PrizeFactory::new()
                ->campaign($this->campaign)
                ->highSegment()
                ->create()
        )
        ->create();

    $this->assertDatabaseCount('prizes', 2);

    $this->assertDatabaseCount('games', 1);

    $this->mockRandomPrizeAction();

    resolve(FindUnfinishedGameOrInitiateOne::class)->execute('test-account', $this->campaign->id, 'high');

    $this->assertDatabaseCount('games', 2);
});

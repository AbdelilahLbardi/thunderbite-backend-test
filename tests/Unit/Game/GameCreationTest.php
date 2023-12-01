<?php

namespace Tests\Unit\Game;


use App\Actions\Games\FindUnfinishedGameOrInitiateOne;
use App\Models\Campaign;
use App\Models\Prize;
use Database\Factories\CampaignFactory;
use Database\Factories\GameFactory;
use Database\Factories\PrizeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Mocks;

class GameCreationTest extends TestCase
{
    use RefreshDatabase, Mocks;

    protected FindUnfinishedGameOrInitiateOne $findUnfinishedGameOrInitiateOne;
    protected Campaign $campaign;
    protected Prize $prize;

    protected function setUp():void
    {
        parent::setUp();

        $this->findUnfinishedGameOrInitiateOne = resolve(FindUnfinishedGameOrInitiateOne::class);

        $this->campaign = CampaignFactory::new()->create();
        $this->prize = PrizeFactory::new()
            ->campaign($this->campaign)
            ->highSegment()
            ->create();
    }

    public function test_new_account_game_is_created_when_no_previous_one_exists(): void
    {
        $this->assertDatabaseCount('games', 0);

        $this->mockRandomPrizeAction();

        resolve(FindUnfinishedGameOrInitiateOne::class)->execute('test-account', $this->campaign->id, 'high');

        $this->assertDatabaseCount('games', 1);
    }

    public function test_old_account_game_is_retrieved_when_it_is_not_revealed(): void
    {
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
    }

    public function test_new_account_game_created_when_previous_one_is_revealed(): void
    {
        GameFactory::new()->create();

        $this->assertDatabaseCount('games', 1);

        $this->mockRandomPrizeAction();

        resolve(FindUnfinishedGameOrInitiateOne::class)->execute('test-account', $this->campaign->id, 'high');

        $this->assertDatabaseCount('games', 2);
    }

    public function test_new_game_is_created_when_the_existing_account_game_prize_level_is_different(): void
    {
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
    }

    public function test_new_game_is_created_when_the_existing_account_game_prize_level_is_same_but_revealed(): void
    {
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
    }
}

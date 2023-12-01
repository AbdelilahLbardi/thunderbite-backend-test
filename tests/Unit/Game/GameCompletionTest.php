<?php

namespace Tests\Unit\Game;

use App\Actions\Games\CheckGameCompletion;
use Tests\TestCase;

class GameCompletionTest extends TestCase
{
    protected CheckGameCompletion $checkGameCompletion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkGameCompletion = resolve(CheckGameCompletion::class);
    }


    /**
     * @dataProvider dataProvider
     */
    public function test_game_marked_as_completed_when_revealed_titles_matches_the_configuration($revealedTiles, $configuredTries, $isCompleted)
    {
        config(['games.prize.tries' => $configuredTries]);

        $gameCompletion = $this->checkGameCompletion->execute($revealedTiles);

        $this->assertSame(
            $isCompleted,
            $gameCompletion->isComplete()
        );
    }

    public function dataProvider(): array
    {
        return [
            '0 revealed tiles, 3 tries' => [
                'revealed_tiles' => [],
                'configured_win_tries' => 3,
                'is_completed' => false
            ],
            '1 revealed tile, 3 tries' =>[
                'revealed_tiles' => [
                    []
                ],
                'configured_win_tries' => 3,
                'is_completed' => false
            ],
            '2 revealed tile, 3 tries' => [
                'revealed_tiles' => [
                    [], []
                ],
                'configured_win_tries' => 3,
                'is_completed' => false
            ],
            '3 revealed tile, 3 tries' => [
                'revealed_tiles' => [
                    [], [], []
                ],
                'configured_win_tries' => 3,
                'is_completed' => true
            ],
            '4 revealed tile, 3 tries' => [
                'revealed_tiles' => [
                    [], [], [], []
                ],
                'configured_win_tries' => 3,
                'is_completed' => true
            ],
            '4 revealed tile, 5 tries' => [
                'revealed_tiles' => [
                    [], [], [], []
                ],
                'configured_win_tries' => 5,
                'is_completed' => false
            ],
        ];
    }
}

<?php

use App\Actions\Games\CheckGameCompletion;

beforeEach(function () {
    $this->checkGameCompletion = resolve(CheckGameCompletion::class);
});

test('game is marked as completed when revealed tiles flips exceeds maximum tries', function ($revealedTiles, $configuredTries, $isCompleted) {

    config(['games.prize.tries' => $configuredTries]);

    $gameCompletion = $this->checkGameCompletion->execute($revealedTiles);

    $this->assertSame(
        $isCompleted,
        $gameCompletion->isComplete()
    );

})->with([
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
]);

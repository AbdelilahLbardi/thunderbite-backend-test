<?php

namespace App\Actions\Games;

class CheckGameCompletion
{
    private bool $isCompleted = false;
    private ?string $message = null;

    public function execute(array $revealedTiles): CheckGameCompletion
    {
        $this->isCompleted = count($revealedTiles) >= config('games.prize.tries');

        if ($this->isCompleted) {
            $this->message = trans('prize.default_win_message');
        }

        return $this;
    }

    public function isComplete(): bool
    {
        return $this->isCompleted;
    }

    public function isIncomplete(): bool
    {
        return ! $this->isComplete();
    }

    public function message(): string|null
    {
        return $this->message;
    }


}

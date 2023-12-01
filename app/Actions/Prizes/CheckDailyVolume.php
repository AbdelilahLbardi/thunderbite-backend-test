<?php

namespace App\Actions\Prizes;

use Carbon\Carbon;

class CheckDailyVolume
{
    private bool $isValid = true;
    private ?bool $message = null;

    public function execute(string $cacheKey, int $prizeDailyVolume): self
    {
        if (cache($cacheKey) >= $prizeDailyVolume) {
            $this->isValid = false;

            $time = Carbon::tomorrow()->diff(Carbon::now());

            $this->message = trans('prize.daily_volume_exceeded', $time->h, $time->i);

            return $this;
        }

        $this->isValid = true;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isInvalid(): bool
    {
        return ! $this->isValid();
    }

    public function message(): ?string
    {
        return $this->message;
    }
}

<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;

class ConvertsTimestampToTimezone
{
    public function execute(string|Carbon $dateTime, string $timezone): string
    {
        if (is_string($dateTime)) {
            $dateTime = Carbon::createFromTimeString($dateTime, $timezone);
        }

        return $dateTime->format('Y-m-d H:i:s');
    }
}

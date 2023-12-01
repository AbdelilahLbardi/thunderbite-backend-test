<?php

namespace App\Actions\Campaign;

use Carbon\Carbon;

class CheckIfCampaignTimeIsInvalid
{
    public function execute(Carbon $startAt, Carbon $endsAt, string $timezone): bool
    {
        return $startAt->setTimezone($timezone)->isFuture() || $endsAt->setTimezone($timezone)->isPast();
    }

}

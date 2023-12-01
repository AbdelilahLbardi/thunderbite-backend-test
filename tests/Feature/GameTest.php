<?php

namespace Tests\Feature;

use App\Actions\Games\CheckGameCompletion;
use App\Models\Campaign;
use Database\Factories\CampaignFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
}

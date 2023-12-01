<?php

namespace Database\Factories;

use App\Contracts\Models\GameContract;
use App\Models\Campaign;
use App\Models\Prize;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory implements GameContract
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function definition(): array
    {
        $campaign = Campaign::query()->inRandomOrder()->first();

        return [
            'campaign_id' => $campaign->id,
            'prize_id' => Prize::query()->where('campaign_id', $campaign->id)->inRandomOrder()->first()->id,
            'account' => $this->faker->userName(),
            'revealed_at' => now()->subDays(random_int(1, 10)),
        ];
    }

    public function campaign(Campaign $campaign): self
    {
        return $this->state(['campaign_id' => $campaign->id]);
    }

    public function prize(Prize $prize): self
    {
        return $this->state(['prize_id' => $prize->id]);
    }

    public function unrevealed(): self
    {
        return $this->state(['revealed_at' => null]);
    }
}

<?php

namespace Database\Factories;

use App\Contracts\Models\PrizeContract;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prize>
 */
class PrizeFactory extends Factory implements PrizeContract
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Low 1',
            'level' => 'low',
            'weight' => '25.00',
            'starts_at' => now()->subDays(10)->startOfDay(),
            'ends_at' => now()->addDays(7)->endOfDay(),
        ];
    }

    public function campaign(Campaign $campaign): self
    {
        return $this->state(['campaign_id' => $campaign->id]);
    }

    public function segment(string $segment): self
    {
        throw_unless(
            in_array($segment, ['low', 'med', 'high']),
            new \InvalidArgumentException('Invalid segment.')
        );

        return $this->state(['level' => $segment]);
    }

    public function lowSegment(): self
    {
        return $this->segment('low');
    }

    public function medSegment(): self
    {
        return $this->segment('med');
    }

    public function highSegment(): self
    {
        return $this->segment('high');
    }
}

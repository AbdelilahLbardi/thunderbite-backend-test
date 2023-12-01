<?php

namespace Database\Factories;

use App\Contracts\Models\CampaignContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory implements CampaignContract
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $n = rand(1, 100);

        return [
            'timezone' => 'Europe/London',
            'name' => 'Test Campaign ' . $n,
            'slug' => 'test-campaign-' . $n,
            'starts_at' => now()->startOfDay(),
            'ends_at' => now()->addDays(7)->endOfDay(),
        ];
    }
}

<?php

namespace Tests\Traits;

use App\Actions\Prizes\GetRandomPrize;
use App\Models\Prize;
use Mockery;
use Mockery\MockInterface;

trait Mocks
{
    /*
     * SQLITE does not support 'RAND' function in MYSQL.
     * Hence, mocking the 'execute' from GetRandomPrize to return a normal rand prize.
     */
    private function mockRandomPrizeAction(): void
    {
        $this->instance(
            GetRandomPrize::class,
            Mockery::mock(GetRandomPrize::class, function (MockInterface $mock) {
                $mock->shouldReceive('execute')->andReturn(
                    Prize::query()->inRandomOrder()->first()
                );
            })
        );
    }
}

<?php

namespace App\Actions\Prizes;

use App\Models\Prize;
use Illuminate\Database\Eloquent\Builder;

class GetRandomPrize
{
    public function execute(string $segment = 'low', array $selection = ['*']): Prize
    {
        return Prize::query()
            ->select(...$selection)
            ->when($segment, fn (Builder $q, $segment) => $q->where('level', '=', $segment))
            ->orderByRaw('-LOG(1.0 - RAND()) / weight')
            ->first();
    }
}

<?php

namespace App\Http\Livewire\Backstage;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GameTable extends TableComponent
{
    private array $columns = [
        [
            'title' => 'account',
            'sort' => true,
        ],

        [
            'title' => 'prize_id',
            'attribute' => 'prize_id',
            'sort' => true,
        ],

        [
            'title' => 'revealed at',
            'attribute' => 'revealed_at',
            'sort' => true,
        ],
    ];

    public array $action = [
        [
            'label' => 'Export csv',
            'method' => 'export'
        ],
    ];

    public $sortField = 'revealed_at';

    public $extraFilters = 'games-filters';

    public int|string|null $prizeId = null;

    public ?string $account = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            echo collect($this->columns)->implode('title', ',');

            Game::filter($this->account, $this->prizeId, $this->startDate, $this->endDate, session('activeCampaign'))
                ->select('account', 'prize_id', 'revealed_at')
                ->orderBy($this->sortField, $this->sortDesc ? 'DESC' : 'ASC')
                ->chunk(100, function (Collection $games) {
                    $games->each(function (Game $game) {
                        echo "\n{$game->account},{$game->prize_id},{$game->revealed_at}";
                    });
                });
        }, 'games.csv');
    }

    public function render()
    {


        return view('livewire.backstage.table', [
            'columns' => $this->columns,
            'actions' => $this->action,
            'resource' => 'games',
            'rows' => Game::filter($this->account, $this->prizeId, $this->startDate, $this->endDate, session('activeCampaign'))
                ->orderBy($this->sortField, $this->sortDesc ? 'DESC' : 'ASC')
                ->paginate($this->perPage),
        ]);
    }
}

<div class="w-full flex justify-between">
    <div></div>
    <div class="flex">
        @foreach($actions as $action)
            <div class="col-span-6 text-right pb-8">
                <form wire:submit.prevent="{{ $action['method'] }}">
                    <div class="grid grid-cols-4 gap-4 items-start pt-5">
                        <div class="col-start-2 col-span-3">
                            <button type="submit" class="submit-button">
                                {{ $action['label'] }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>


<div class="flex justify-between pb-4 h-14">
    <h1 class="m-0 p-0">{{ ucfirst($resource) }}</h1>
    @if(isset($crmHeader))
        <div class="flex justify-between text-sm text-current pt-2 w-2/5">
            <div class="mr-2">Total Games in period: <span class="font-semibold nav-header">{{ $playerTotals->count ?? 0 }}</span></div>
            <div>Total cost for period: <span class="font-semibold nav-header">{{ $playerTotals->cost ?? 0 }}</span></div>
        </div>
    @endif
    <div class="flex justify-between ">
        <select wire:model="perPage" class="form-select border border-gray-300 bg-gray-100 rounded-full text-gray-400">
            <option>10</option>
            <option>15</option>
            <option>25</option>
        </select>

        {{-- @if($hasSearch)
            <input wire:model="search" type="text" placeholder="Search..." class="bg-gray-100 border border-gray-300 rounded-full px-4 text-gray-400 ml-4">
        @endif --}}


    </div>
</div>

@isset($extraFilters)
    <div class="pb-4">
        @include('backstage.partials.filters.' . $extraFilters)
    </div>
@endisset

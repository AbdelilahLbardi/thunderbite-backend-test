@csrf

@include('backstage.partials.forms.text', [
    'field' => 'name',
    'label' => 'Name',
    'value' => old('name') ?? $prize->name,
])

@include('backstage.partials.forms.images', [
    'field' => 'tile_image',
    'label' => 'Tile image',
    'value' => old('tile_image') ?? $prize->tile_image,
])

@include('backstage.partials.forms.text', [
    'field' => 'description',
    'label' => 'Description',
    'value' => old('description') ?? $prize->description,
])

@include('backstage.partials.forms.number', [
    'field' => 'weight',
    'label' => 'Weight',
    'step' => 1,
    'value' => old('weight') ?? $prize->weight,
])

@include('backstage.partials.forms.number', [
    'field' => 'daily_volume',
    'label' => 'Daily Volume',
    'step' => 1,
    'value' => old('daily_volume') ?? $prize->daily_volume,
])

@include('backstage.partials.forms.select', [
    'field' => 'level',
    'label' => 'Level',
    'value' => old('level') ?? $prize->level,
    'options' => ['low' => 'Low', 'med' => 'Medium', 'high' => 'High'],
])

@include('backstage.partials.forms.starts-ends', [
    'starts_at' => old('starts_at') ?? ($prize->starts_at === null ? $prize->starts_at : $prize->starts_at->format('d-m-Y H:i:s')),
    'ends_at' => old('ends_at') ?? ($prize->ends_at === null ? $prize->ends_at : $prize->ends_at->format('d-m-Y H:i:s')),
    'minDate' => $activeCampaign->starts_at,
    'maxDate' => $activeCampaign->ends_at,
])


@includeWhen(empty($disabled), 'backstage.partials.forms.submit')

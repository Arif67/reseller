@foreach (($selectedAttributes ?? []) as $selectedAttribute)
    @if (!empty($selectedAttribute['attribute']) && !empty($selectedAttribute['value']))
        <div>{{ $selectedAttribute['attribute'] }}: {{ $selectedAttribute['value'] }}</div>
    @endif
@endforeach

@php
    $visibleAttributeIds = collect($selectedAttributeIds ?? [])->map(fn ($value) => (int) $value)->all();
@endphp

@foreach ($attributes as $attribute)
    @php
        $isVisible = in_array((int) $attribute->id, $visibleAttributeIds, true);
    @endphp
    <div class="col-lg-3 col-md-4 col-sm-6 variable-attribute-field"
        data-attribute-id="{{ $attribute->id }}"
        @if (! $isVisible) style="display:none;" @endif>
        <div class="form-group">
            <label class="form-label">{{ $attribute->title }}</label>
            <select class="form-control attribute-value-select"
                data-attribute-id="{{ $attribute->id }}"
                name="{{ $inputNamePrefix }}[{{ $attribute->id }}]"
                @disabled(! $isVisible)>
                <option value="">Select</option>
                @foreach ($attribute->values as $value)
                    <option value="{{ $value->id }}"
                        @selected((string) ($selectedValues[$attribute->id] ?? '') === (string) $value->id)>
                        {{ $value->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endforeach

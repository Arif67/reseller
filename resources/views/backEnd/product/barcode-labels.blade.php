<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Labels - {{ $product->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .print-btn { border: 0; background: #111827; color: #fff; padding: 10px 16px; cursor: pointer; }
        .label-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .barcode-label { border: 1px solid #d1d5db; padding: 14px; background: #fff; }
        .barcode-label h4 { font-size: 15px; line-height: 1.4; margin: 0 0 6px; }
        .barcode-meta { font-size: 12px; color: #475569; margin-bottom: 8px; min-height: 18px; }
        .barcode-number { margin-top: 8px; font-size: 12px; letter-spacing: 0.08em; text-align: center; }
        .barcode-svg { width: 100%; height: 72px; }
        @media print { .toolbar { display: none; } body { margin: 0; padding: 12px; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h2 style="margin:0 0 4px;">Barcode Labels</h2>
            <div style="font-size:13px;color:#64748b;">{{ $product->name }}</div>
        </div>
        <button type="button" class="print-btn" onclick="window.print()">Print</button>
    </div>

    <div class="label-grid">
        @if($product->pro_barcode)
        <div class="barcode-label">
            <h4>{{ $product->name }}</h4>
            <div class="barcode-meta">Product Barcode</div>
            <svg class="barcode-svg" data-barcode-value="{{ $product->pro_barcode }}"></svg>
            <div class="barcode-number">{{ $product->pro_barcode }}</div>
        </div>
        @endif

        @foreach($product->variables as $variable)
            @if($variable->barcode)
            <div class="barcode-label">
                <h4>{{ $product->name }}</h4>
                <div class="barcode-meta">
                    @php
                        $parts = collect([
                            $variable->size ? 'Size: ' . $variable->size : null,
                            $variable->color ? 'Color: ' . $variable->color : null,
                            $variable->weight ? 'Weight: ' . $variable->weight : null,
                            $variable->model ? 'Model: ' . $variable->model : null,
                        ])->filter()->implode(' | ');
                    @endphp
                    {{ $parts ?: 'Variation Barcode' }}
                </div>
                <svg class="barcode-svg" data-barcode-value="{{ $variable->barcode }}"></svg>
                <div class="barcode-number">{{ $variable->barcode }}</div>
            </div>
            @endif
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script>
        document.querySelectorAll('.barcode-svg').forEach(function(node) {
            var value = node.getAttribute('data-barcode-value');
            if (!value) {
                return;
            }
            JsBarcode(node, value, {
                format: 'CODE128',
                displayValue: false,
                margin: 0,
                width: 1.7,
                height: 58
            });
        });
    </script>
</body>
</html>

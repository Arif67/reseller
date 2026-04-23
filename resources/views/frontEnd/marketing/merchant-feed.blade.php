<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
    <title>{{ $config->merchant_store_name ?: config('app.name') }}</title>
    <link>{{ route('home') }}</link>
    <description>Google Merchant Center product feed</description>
@foreach ($products as $product)
    <item>
        <g:id>{{ $product->id }}</g:id>
        <title><![CDATA[{{ $product->name }}]]></title>
        <description><![CDATA[{{ strip_tags($product->meta_description ?: $product->name) }}]]></description>
        <link>{{ route('product', $product->slug) }}</link>
        <g:image_link>{{ asset($product->image?->image ?: 'uploads/logo.png') }}</g:image_link>
        <g:availability>{{ (float) ($product->stock ?? 0) > 0 ? 'in stock' : 'out of stock' }}</g:availability>
        <g:price>{{ number_format((float) ($product->new_price ?? 0), 2, '.', '') }} BDT</g:price>
        <g:condition>new</g:condition>
        <g:brand><![CDATA[{{ $product->brand?->name ?: ($config->merchant_store_name ?: config('app.name')) }}]]></g:brand>
    </item>
@endforeach
</channel>
</rss>

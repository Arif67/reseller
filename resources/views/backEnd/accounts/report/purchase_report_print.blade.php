<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Report Print</title>
    <style>body{font-family:Arial,sans-serif;font-size:14px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px;text-align:left}h2{margin-bottom:16px}</style>
</head>
<body onload="window.print()">
    <h2>Purchase Report</h2>
    <table>
        <thead>
            <tr><th>No</th><th>Date</th><th>Supplier</th><th>Total</th><th>Paid</th><th>Due</th></tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->purchase_no }}</td>
                    <td>{{ $purchase->purchase_date?->format('d M Y') ?: '-' }}</td>
                    <td>{{ $purchase->supplier?->name ?: $purchase->supplier_name }}</td>
                    <td>{{ number_format((float) $purchase->grand_total, 2, '.', '') }}</td>
                    <td>{{ number_format((float) $purchase->paid_amount, 2, '.', '') }}</td>
                    <td>{{ number_format((float) $purchase->due_amount, 2, '.', '') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

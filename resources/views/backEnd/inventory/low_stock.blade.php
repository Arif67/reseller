@extends('backEnd.layouts.master')
@section('title','Low Stock Report')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><h4 class="page-title">Low Stock Report</h4></div></div></div><div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-striped align-middle"><thead><tr><th>Product</th><th>Variant</th><th>Current Stock</th><th>Alert Level</th><th>Movement</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row['product_name'] }}</td><td>{{ $row['variant_name'] ?: '-' }}</td><td><span class="badge bg-danger">{{ $row['stock'] }}</span></td><td>{{ $row['threshold'] }}</td><td><a href="{{ route('inventory.product_movements', $row['product_id']) }}" class="btn btn-primary btn-sm">Details</a></td></tr>@empty<tr><td colspan="5" class="text-center">No low stock item found.</td></tr>@endforelse</tbody></table></div></div></div></div>
@endsection

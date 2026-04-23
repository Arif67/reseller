@extends('backEnd.layouts.master')
@section('title','Purchase Returns')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><h4 class="page-title">Purchase Returns</h4></div></div></div><div class="card"><div class="card-body"><table class="table table-striped align-middle"><thead><tr><th>Return No</th><th>Date</th><th>Purchase</th><th>Total</th><th>Reason</th></tr></thead><tbody>@forelse($data as $value)<tr><td>{{ $value->return_no }}</td><td>{{ $value->return_date?->format('d M Y') ?: '-' }}</td><td>{{ $value->purchase?->purchase_no ?: '-' }}</td><td>{{ number_format((float) $value->total_amount, 2, '.', '') }}</td><td>{{ $value->reason ?: '-' }}</td></tr>@empty<tr><td colspan="5" class="text-center">No purchase return found.</td></tr>@endforelse</tbody></table>{{ $data->links('pagination::bootstrap-4') }}</div></div></div>
@endsection

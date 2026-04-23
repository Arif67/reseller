@extends('backEnd.layouts.master')
@section('title','Fund Transfers')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.fund_transfers.create') }}" class="btn btn-primary rounded-pill">Create</a></div><h4 class="page-title">Fund Transfers</h4></div></div></div><div class="card"><div class="card-body"><table class="table table-striped align-middle"><thead><tr><th>Date</th><th>From</th><th>To</th><th>Amount</th><th>Reference</th></tr></thead><tbody>@foreach($data as $value)<tr><td>{{ $value->transfer_date?->format('d M Y') ?: '-' }}</td><td>{{ $value->fromAccount?->name ?: '-' }}</td><td>{{ $value->toAccount?->name ?: '-' }}</td><td>{{ number_format((float)$value->amount,2,'.','') }}</td><td>{{ $value->reference_no ?: '-' }}</td></tr>@endforeach</tbody></table>{{ $data->links('pagination::bootstrap-4') }}</div></div></div>
@endsection

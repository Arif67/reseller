@extends('backEnd.layouts.master')
@section('title','Journal Entries')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.journal_entries.create') }}" class="btn btn-primary rounded-pill">Create</a></div><h4 class="page-title">Journal Entries</h4></div></div></div><div class="card"><div class="card-body"><table class="table table-striped align-middle"><thead><tr><th>Date</th><th>Reference</th><th>Description</th><th>Lines</th></tr></thead><tbody>@foreach($data as $value)<tr><td>{{ $value->entry_date?->format('d M Y') }}</td><td>{{ $value->reference_no }}</td><td>{{ $value->description ?: '-' }}</td><td>{{ $value->lines_count }}</td></tr>@endforeach</tbody></table>{{ $data->links('pagination::bootstrap-4') }}</div></div></div>
@endsection

@extends('backEnd.layouts.master')
@section('title','Income Categories')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.income_categories.create') }}" class="btn btn-primary rounded-pill">Create</a></div><h4 class="page-title">Income Categories</h4></div></div></div><div class="card"><div class="card-body"><table class="table table-striped align-middle"><thead><tr><th>Name</th><th>Note</th><th>Status</th><th>Action</th></tr></thead><tbody>@foreach($data as $value)<tr><td>{{ $value->name }}</td><td>{{ $value->note ?: '-' }}</td><td>{!! $value->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td><td><a href="{{ route('accounts.income_categories.edit', $value->id) }}" class="btn btn-primary btn-sm">Edit</a></td></tr>@endforeach</tbody></table></div></div></div>
@endsection

@extends('backEnd.layouts.master')
@section('title','Supplier Ledger Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.suppliers.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Supplier Ledger Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('accounts.suppliers.store') }}" method="POST" class="row">@csrf@include('backEnd.accounts.supplier.form',['row'=>null])<div class="col-12"><button class="btn btn-success">Submit</button></div></form></div></div></div>
@endsection

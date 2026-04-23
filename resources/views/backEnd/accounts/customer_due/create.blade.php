@extends('backEnd.layouts.master')
@section('title','Customer Due Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.customer_dues.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Customer Due Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('accounts.customer_dues.store') }}" method="POST" class="row">@csrf@include('backEnd.accounts.customer_due.form',['row'=>null])<div class="col-12"><button class="btn btn-success">Submit</button></div></form></div></div></div>
@endsection

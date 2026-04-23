@extends('backEnd.layouts.master')
@section('title','Customer Due Edit')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('accounts.customer_dues.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Customer Due Edit</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('accounts.customer_dues.update') }}" method="POST" class="row">@csrf<input type="hidden" name="id" value="{{ $edit_data->id }}">@include('backEnd.accounts.customer_due.form',['row'=>$edit_data])<div class="col-12"><button class="btn btn-success">Update</button></div></form></div></div></div>
@endsection

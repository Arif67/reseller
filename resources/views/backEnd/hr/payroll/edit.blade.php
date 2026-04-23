@extends('backEnd.layouts.master')
@section('title','Payroll Edit')

@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.payroll.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Payroll Edit</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.payroll.update') }}" method="POST" class="row">@csrf<input type="hidden" name="id" value="{{ $edit_data->id }}">@include('backEnd.hr.payroll.partials.form', ['payroll' => $edit_data])<div class="col-12"><button type="submit" class="btn btn-success">Update</button></div></form></div></div></div>
@endsection

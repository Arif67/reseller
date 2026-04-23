@extends('backEnd.layouts.master')
@section('title','Payroll Create')

@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.payroll.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Payroll Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.payroll.store') }}" method="POST" class="row">@csrf @include('backEnd.hr.payroll.partials.form', ['payroll' => null])<div class="col-12"><button type="submit" class="btn btn-success">Submit</button></div></form></div></div></div>
@endsection

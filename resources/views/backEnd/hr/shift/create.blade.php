@extends('backEnd.layouts.master')
@section('title','Shift Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.shifts.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Shift Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.shifts.store') }}" method="POST" class="row">@csrf @include('backEnd.hr.shift.form',['shift'=>null])<div class="col-12"><button class="btn btn-success">Submit</button></div></form></div></div></div>
@endsection

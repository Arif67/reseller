@extends('backEnd.layouts.master')
@section('title','Leave Type Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.leave_types.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Leave Type Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.leave_types.store') }}" method="POST" class="row">@csrf @include('backEnd.hr.leave_type.form',['leaveType'=>null])<div class="col-12"><button class="btn btn-success">Submit</button></div></form></div></div></div>
@endsection

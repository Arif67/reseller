@extends('backEnd.layouts.master')
@section('title','Attendance Edit')

@section('content')
<div class="container-fluid">
    <div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.attendance.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Attendance Edit</h4></div></div></div>
    <div class="card"><div class="card-body"><form action="{{ route('hr.attendance.update') }}" method="POST" class="row">@csrf<input type="hidden" name="id" value="{{ $edit_data->id }}">@include('backEnd.hr.attendance.partials.form', ['attendance' => $edit_data]) <div class="col-12"><button type="submit" class="btn btn-success">Update</button></div></form></div></div>
</div>
@endsection

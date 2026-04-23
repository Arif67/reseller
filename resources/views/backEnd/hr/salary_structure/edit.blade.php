@extends('backEnd.layouts.master')
@section('title','Salary Structure Edit')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.salary_structures.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Salary Structure Edit</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.salary_structures.update') }}" method="POST" class="row">@csrf<input type="hidden" name="id" value="{{ $edit_data->id }}">@include('backEnd.hr.salary_structure.form',['salaryStructure'=>$edit_data])<div class="col-12"><button class="btn btn-success">Update</button></div></form></div></div></div>
@endsection

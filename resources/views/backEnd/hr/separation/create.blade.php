@extends('backEnd.layouts.master')
@section('title','Separation Create')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.separations.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Resignation / Termination Create</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.separations.store') }}" method="POST" class="row">@csrf @include('backEnd.hr.separation.form',['separation'=>null])<div class="col-12"><button class="btn btn-success">Submit</button></div></form></div></div></div>
@endsection

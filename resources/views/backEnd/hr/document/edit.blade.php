@extends('backEnd.layouts.master')
@section('title','Document Edit')
@section('content')
<div class="container-fluid"><div class="row"><div class="col-12"><div class="page-title-box"><div class="page-title-right"><a href="{{ route('hr.documents.index') }}" class="btn btn-primary rounded-pill">Manage</a></div><h4 class="page-title">Document Edit</h4></div></div></div><div class="card"><div class="card-body"><form action="{{ route('hr.documents.update') }}" method="POST" enctype="multipart/form-data" class="row">@csrf<input type="hidden" name="id" value="{{ $edit_data->id }}">@include('backEnd.hr.document.form',['document'=>$edit_data])<div class="col-12"><button class="btn btn-success">Update</button></div></form></div></div></div>
@endsection

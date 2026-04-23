@extends('backEnd.layouts.master')
@section('title','Expense Category Edit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('expensecategories.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Expense Category Edit</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('expensecategories.update') }}" method="POST" class="row">
                @csrf
                <input type="hidden" name="id" value="{{ $edit_data->id }}">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $edit_data->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="d-block form-label">Status</label>
                        <label class="switch">
                            <input type="checkbox" name="status" value="1" @if($edit_data->status) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

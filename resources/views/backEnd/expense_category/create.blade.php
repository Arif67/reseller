@extends('backEnd.layouts.master')
@section('title','Expense Category Create')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('expensecategories.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Expense Category Create</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('expensecategories.store') }}" method="POST" class="row">
                @csrf
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="d-block form-label">Status</label>
                        <label class="switch">
                            <input type="checkbox" name="status" value="1" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

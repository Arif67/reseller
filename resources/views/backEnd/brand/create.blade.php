@extends('backEnd.layouts.master')
@section('title','Brand Create')
@section('css')
<link href="{{ asset('backEnd/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid p-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="brand-editor-shell">
                @include('backEnd.brand.partials.editor-header', [
                    'kicker' => 'Brand Setup',
                    'title' => 'Add New Brand',
                ])

                <div class="card brand-editor-card">
                    <div class="card-body">
                        <form action="{{ route('brands.store') }}" method="POST" class="row" data-parsley-validate enctype="multipart/form-data">
                            @csrf
                            @include('backEnd.brand.partials.form', [
                                'row' => null,
                            ])

                            <div class="col-12">
                                <input type="submit" class="btn btn-success" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('backEnd/assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('backEnd/assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('backEnd/assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('backEnd/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection

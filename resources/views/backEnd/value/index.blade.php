@extends('backEnd.layouts.master')
@section('title','Brand Manage')
@section('css')
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/')}}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right d-flex gap-2">
                    <button type="button" id="bulk-delete-btn" class="btn btn-danger rounded-pill">Delete Selected</button>
                    <a href="{{route('value.create')}}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">value List </h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="bulk-delete-form" method="POST" action="{{ route('value.bulk_destroy') }}">
                    @csrf
                </form>
                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:2%"><input type="checkbox" class="form-check-input checkall" value=""></th>
                            <th>SL</th>
                            <th> Title </th>
                            <th> Attribute </th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                
                
                    <tbody>
                        @foreach($data as $key=>$value)
                        <tr>
                            <td><input type="checkbox" class="form-check-input checkbox" value="{{$value->id}}"></td>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$value->title}}</td>
                            <td>{{ optional($value->attribute)->title ?? 'N/A' }} </td>
                            <td>@if($value->status==1)<span class="badge bg-soft-success text-success">Active</span> @else <span class="badge bg-soft-danger text-danger">Inactive</span> @endif</td>
                            <td>
                                <div class="button-list">
                                    @if($value->status == 1)
                                    <form method="post" action="{{route('value.inactive')}}" class="d-inline"> 
                                    @csrf
                                    <input type="hidden" value="{{$value->id}}" name="hidden_id">       
                                    <button type="button" class="btn btn-xs  btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button></form>
                                    @else
                                    <form method="post" action="{{route('value.active')}}" class="d-inline">
                                        @csrf
                                    <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                    <button type="button" class="btn btn-xs  btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button></form>
                                    @endif

                                    <a href="{{route('value.edit',$value->id)}}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>

                                    <form method="post" action="{{route('value.destroy')}}" class="d-inline">        
                                        @csrf
                                    <input type="hidden" value="{{$value->id}}" name="hidden_id">
                                    <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button></form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
 
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>
@endsection


@section('script')
<!-- third party js -->
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/datatables.init.js"></script>
<!-- third party js ends -->

<script>
$(document).ready(function () {
    $('.checkall').on('change', function () {
        $('.checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#bulk-delete-btn').on('click', function () {
        const valueIds = $('.checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (valueIds.length === 0) {
            toastr.error('Please select at least one value first');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected values?')) {
            return;
        }

        const form = $('#bulk-delete-form');
        form.find('input[name="value_ids[]"]').remove();

        valueIds.forEach(function (id) {
            form.append('<input type="hidden" name="value_ids[]" value="' + id + '">');
        });

        form.trigger('submit');
    });
});
</script>
@endsection
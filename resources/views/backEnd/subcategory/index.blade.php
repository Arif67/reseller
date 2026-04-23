@extends('backEnd.layouts.master')
@section('title','Subcategory Manage')
@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('subcategories.create')}}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Subcategory Manage</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('subcategories.destroy') }}" method="POST" id="bulkSubcategoryDeleteForm" class="d-none">
                    @csrf
                </form>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="subcategoryCheckAll">
                        <label class="form-check-label" for="subcategoryCheckAll">Select All</label>
                    </div>
                    <button type="submit" class="btn btn-danger rounded-pill" form="bulkSubcategoryDeleteForm">Bulk Delete</button>
                </div>

                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:2%;"><input type="checkbox" class="form-check-input" id="subcategoryCheckAllTable"></th>
                            <th>SL</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>               
                
                    <tbody>
                        @foreach($data as $key=>$value)
                        <tr>
                            <td><input type="checkbox" class="form-check-input subcategory-checkbox" name="hidden_ids[]" value="{{$value->id}}" form="bulkSubcategoryDeleteForm"></td>
                            <td>
                                <span>{{$loop->iteration}}</span>                               
                            </td>                            
                            <td>
                                <span>{{ $value->category->name ?? 'No Category' }}</span>
                            </td>

                            <td>
                                <span>{{$value->subcategoryName}}</span>
                            </td>
                          
                            <td>
                                @if($value->status==1)
                                <span class="badge bg-soft-success text-success">Active</span> 
                                @else 
                                <span class="badge bg-soft-danger text-danger">Inactive</span> 
                                @endif
                            </td>
                            <td>
                                <div class="button-list">
                                    @if($value->status == 1)
                                    <form method="post" action="{{route('subcategories.inactive')}}" class="d-inline"> 
                                    @csrf
                                    <input type="hidden" value="{{$value->id}}" name="hidden_id">       
                                    <button type="button" class="btn btn-xs  btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button></form>
                                    @else
                                    <form method="post" action="{{route('subcategories.active')}}" class="d-inline">
                                        @csrf
                                    <input type="hidden" value="{{$value->id}}" name="hidden_id">        
                                    <button type="button" class="btn btn-xs  btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button></form>
                                    @endif

                                    <a href="{{route('subcategories.edit',$value->id)}}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
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
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('subcategoryCheckAll');
        const checkAllTable = document.getElementById('subcategoryCheckAllTable');
        const bulkDeleteForm = document.getElementById('bulkSubcategoryDeleteForm');

        function getCheckboxes() {
            return Array.from(document.querySelectorAll('.subcategory-checkbox'));
        }

        function syncCheckAllState() {
            const checkboxes = getCheckboxes();
            const allChecked = checkboxes.length > 0 && checkboxes.every(function (checkbox) {
                return checkbox.checked;
            });

            if (checkAll) {
                checkAll.checked = allChecked;
            }

            if (checkAllTable) {
                checkAllTable.checked = allChecked;
            }
        }

        function toggleAll(checked) {
            getCheckboxes().forEach(function (checkbox) {
                checkbox.checked = checked;
            });
            syncCheckAllState();
        }

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                toggleAll(this.checked);
            });
        }

        if (checkAllTable) {
            checkAllTable.addEventListener('change', function () {
                toggleAll(this.checked);
            });
        }

        document.addEventListener('change', function (event) {
            if (event.target.classList.contains('subcategory-checkbox')) {
                syncCheckAllState();
            }
        });

        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function (event) {
                const checkedCount = getCheckboxes().filter(function (checkbox) {
                    return checkbox.checked;
                }).length;

                if (!checkedCount) {
                    event.preventDefault();
                    toastr.error('Please select subcategory first');
                    return;
                }

                if (!window.confirm('Selected subcategories delete korte chan?')) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
<!-- third party js ends -->
@endsection

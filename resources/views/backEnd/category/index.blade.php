@extends('backEnd.layouts.master')
@section('title','Category Manage')
@section('css')
<link href="{{asset('backEnd/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right d-flex flex-wrap gap-2">
                    <a href="{{route('categories.create')}}" class="btn btn-primary rounded-pill">Create</a>
                </div>
                <h4 class="page-title">Category Manage</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('categories.destroy') }}" method="POST" id="bulkCategoryDeleteForm" class="d-none">
                        @csrf
                        <div id="bulkCategoryDeleteInputs"></div>
                    </form>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <div class="form-check mb-0">
                            <input type="checkbox" class="form-check-input" id="categoryCheckAll">
                            <label class="form-check-label" for="categoryCheckAll">Select All</label>
                        </div>
                        <button type="submit" class="btn btn-danger rounded-pill" form="bulkCategoryDeleteForm">Bulk Delete</button>
                    </div>

                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th style="width:2%;"><input type="checkbox" class="form-check-input" id="categoryCheckAllTable"></th>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Serial</th>
                                    <th>Image</th>
                                    <th>Icon</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>
@endsection


@section('script')
<!-- third party js -->
<script src="{{asset('backEnd/assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/datatables.net-select/js/dataTables.select.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
<script src="{{asset('backEnd/assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
<script>
    $(function () {
        const $table = $('#datatable-buttons');
        const $checkAll = $('#categoryCheckAll');
        const $checkAllTable = $('#categoryCheckAllTable');
        const $bulkDeleteForm = $('#bulkCategoryDeleteForm');
        const $bulkDeleteInputs = $('#bulkCategoryDeleteInputs');
        const selectedCategoryIds = new Set();

        const table = $table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            lengthMenu: [[25, 50, 100], [25, 50, 100]],
            order: [],
            ajax: '{{ route('categories.index') }}',
            columns: [
                {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'serial', name: 'serial'},
                {data: 'image_preview', name: 'image', orderable: false, searchable: false},
                {data: 'icon_preview', name: 'icon', orderable: false, searchable: false},
                {data: 'status_badge', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            buttons: [
                {extend: 'copy', className: 'btn-light'},
                {extend: 'print', className: 'btn-light'},
                {extend: 'pdf', className: 'btn-light'}
            ],
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                }
            },
            drawCallback: function () {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                syncVisibleSelections();
                syncCheckAllState();
            }
        });

        table.buttons().container().appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

        function getVisibleCheckboxes() {
            return $table.find('.category-checkbox');
        }

        function syncVisibleSelections() {
            getVisibleCheckboxes().each(function () {
                $(this).prop('checked', selectedCategoryIds.has(String($(this).val())));
            });
        }

        function syncCheckAllState() {
            const $checkboxes = getVisibleCheckboxes();
            const allChecked = $checkboxes.length > 0 && $checkboxes.toArray().every(function (checkbox) {
                return checkbox.checked;
            });

            $checkAll.prop('checked', allChecked);
            $checkAllTable.prop('checked', allChecked);
        }

        function toggleVisible(checked) {
            getVisibleCheckboxes().each(function () {
                const id = String($(this).val());

                $(this).prop('checked', checked);

                if (checked) {
                    selectedCategoryIds.add(id);
                } else {
                    selectedCategoryIds.delete(id);
                }
            });

            syncCheckAllState();
        }

        $checkAll.on('change', function () {
            toggleVisible(this.checked);
        });

        $checkAllTable.on('change', function () {
            toggleVisible(this.checked);
        });

        $table.on('change', '.category-checkbox', function () {
            const id = String($(this).val());

            if (this.checked) {
                selectedCategoryIds.add(id);
            } else {
                selectedCategoryIds.delete(id);
            }

            syncCheckAllState();
        });

        $table.on('click', '.change-confirm', function (event) {
            const form = $(this).closest('form');

            event.preventDefault();

            swal({
                title: 'Are you sure you want to change this record?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(function (willChange) {
                if (willChange) {
                    form.submit();
                }
            });
        });

        $bulkDeleteForm.on('submit', function (event) {
            $bulkDeleteInputs.empty();

            if (!selectedCategoryIds.size) {
                event.preventDefault();
                toastr.error('Please select category first');
                return;
            }

            if (!window.confirm('Selected categories delete korte chan?')) {
                event.preventDefault();
                return;
            }

            Array.from(selectedCategoryIds).forEach(function (id) {
                $('<input>', {
                    type: 'hidden',
                    name: 'hidden_ids[]',
                    value: id
                }).appendTo($bulkDeleteInputs);
            });
        });
    });
</script>
<!-- third party js ends -->
@endsection

@extends('backEnd.layouts.master')
@section('title','Product Manage')
@section('css')
<link href="{{ asset('backEnd/') }}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('backEnd/') }}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<style>
    .product-admin-toolbar {
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .product-admin-toolbar .form-control {
        min-height: 42px;
        border-radius: 12px;
        border-color: #dbe4f0;
        box-shadow: none;
    }

    .product-admin-toolbar .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.08);
    }

    .product-admin-toolbar .toolbar-label {
        display: block;
        margin-bottom: 6px;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .action2-btn {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
    }

    .product-table-wrap {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .product-table-wrap table {
        margin-bottom: 0;
    }

    .product-table-wrap thead th {
        background: #f8fafc;
        color: #0f172a;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .product-table-wrap tbody td {
        vertical-align: middle;
    }

    .backend-image {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .product-table-wrap .button-list a,
    .product-table-wrap .button-list button {
        margin-right: 6px;
    }

    .product-table-wrap .button-list i {
        font-size: 15px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add Product</a>
                </div>
                <h4 class="page-title">Product Manage</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <ul class="action2-btn mb-3">
                            <li><a href="{{route('products.update_deals',['status'=>1])}}" class="btn rounded-pill btn-success hotdeal_update"><i class="fe-thumbs-up"></i> Deal</a></li>
                            <li><a href="{{route('products.update_deals',['status'=>0])}}" class="btn rounded-pill btn-danger hotdeal_update"><i class="fe-thumbs-down"></i> Deal</a></li>
                            <li><a href="{{route('products.update_status',['status'=>1])}}" class="btn rounded-pill btn-primary update_status"><i class="fe-thumbs-up"></i> Active</a></li>
                            <li><a href="{{route('products.update_status',['status'=>0])}}" class="btn rounded-pill btn-warning update_status"><i class="fe-thumbs-down"></i> Inactive</a></li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <div class="product-admin-toolbar d-flex flex-wrap align-items-end justify-content-start gap-3 mb-3">
                            <div style="min-width: 170px;">
                                <label class="toolbar-label" for="admin_product_category_id">Category</label>
                                <select name="category_id" id="admin_product_category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="min-width: 170px;">
                                <label class="toolbar-label" for="admin_product_brand_id">Brand</label>
                                <select name="brand_id" id="admin_product_brand_id" class="form-control">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected((string) request('brand_id') === (string) $brand->id)>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="min-width: 190px;">
                                <label class="toolbar-label" for="admin_product_subcategory_id">Subcategory</label>
                                <select name="subcategory_id" id="admin_product_subcategory_id" class="form-control" data-selected-subcategory="{{ request('subcategory_id') }}">
                                    <option value="">All Subcategories</option>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" @selected((string) request('subcategory_id') === (string) $subcategory->id)>{{ $subcategory->subcategoryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="min-width: 190px;">
                                <label class="toolbar-label" for="admin_product_childcategory_id">Child Category</label>
                                <select name="childcategory_id" id="admin_product_childcategory_id" class="form-control" data-selected-childcategory="{{ request('childcategory_id') }}">
                                    <option value="">All Child Categories</option>
                                    @foreach($childcategories as $childcategory)
                                        <option value="{{ $childcategory->id }}" @selected((string) request('childcategory_id') === (string) $childcategory->id)>{{ $childcategory->childcategoryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive product-table-wrap">
                    <table id="productTable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:2%">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkall" value="">
                                </div>
                            </th>
                            <th style="width:2%">SL</th>
                            <th style="width:10%">Action</th>
                            <th style="width:20%">Name</th>
                            <th style="width:10%">Category</th>
                            <th style="width:10%">Image</th>
                            <th style="width:10%">Price</th>
                            <th style="width:8%">Stock</th>
                            <th style="width:14%">Deal & Feature</th>
                            <th style="width:8%">Status</th>
                        </tr>
                    </thead>               
                    </table>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>

@endsection

@section('script')
<script src="{{ asset('backEnd/') }}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('backEnd/') }}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('backEnd/') }}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('backEnd/') }}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    var table = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[1, 'desc']],
        ajax: {
            url: "{{ route('products.index') }}",
            data: function (d) {
                d.category_id = $('#admin_product_category_id').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.subcategory_id = $('#admin_product_subcategory_id').val();
                d.childcategory_id = $('#admin_product_childcategory_id').val();
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'category_name', name: 'category.name', searchable: false },
            { data: 'image_preview', orderable: false, searchable: false },
            { data: 'price', name: 'new_price', orderable: false, searchable: false },
            { data: 'stock_value', name: 'stock', orderable: false, searchable: false },
            { data: 'deal_feature', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
        ]
    });

    $(".checkall").on('change', function(){
        $("#productTable .checkbox").prop('checked', $(this).is(":checked"));
    });

    $('#productTable').on('draw.dt', function () {
        $('.checkall').prop('checked', false);
    });

    $(document).on('click', '.hotdeal_update', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var product = $('#productTable input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                table.ajax.reload(null, false);
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });
    $(document).on('click', '.update_status', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var product = $('#productTable input.checkbox:checked').map(function(){
          return $(this).val();
        });
        var product_ids=product.get();
        if(product_ids.length ==0){
            toastr.error('Please Select A Product First !');
            return ;
        }
        $.ajax({
           type:'GET',
           url:url,
           data:{product_ids},
           success:function(res){
               if(res.status=='success'){
                toastr.success(res.message);
                table.ajax.reload(null, false);
            }else{
                toastr.error('Failed something wrong');
            }
           }
        });
    });

    var adminCategoryFilter = $('#admin_product_category_id');
    var adminSubcategoryFilter = $('#admin_product_subcategory_id');
    var adminChildcategoryFilter = $('#admin_product_childcategory_id');

    function resetAdminSubcategoryOptions(selectedValue) {
        adminSubcategoryFilter.empty();
        adminSubcategoryFilter.append('<option value="">All Subcategories</option>');

        if (selectedValue) {
            adminSubcategoryFilter.val(String(selectedValue));
        }
    }

    function resetAdminChildcategoryOptions(selectedValue) {
        adminChildcategoryFilter.empty();
        adminChildcategoryFilter.append('<option value="">All Child Categories</option>');

        if (selectedValue) {
            adminChildcategoryFilter.val(String(selectedValue));
        }
    }

    adminCategoryFilter.on('change', function() {
        var categoryId = $(this).val();
        resetAdminSubcategoryOptions('');
        resetAdminChildcategoryOptions('');

        if (!categoryId) {
            table.ajax.reload(null, false);
            return;
        }

        $.ajax({
            type: 'GET',
            url: "{{ url('ajax-product-subcategory') }}?category_id=" + categoryId,
            success: function(res) {
                $.each(res, function(key, value) {
                    adminSubcategoryFilter.append('<option value="' + key + '">' + value + '</option>');
                });
                table.ajax.reload(null, false);
            }
        });
    });

    adminSubcategoryFilter.on('change', function() {
        var subcategoryId = $(this).val();
        resetAdminChildcategoryOptions('');

        if (!subcategoryId) {
            table.ajax.reload(null, false);
            return;
        }

        $.ajax({
            type: 'GET',
            url: "{{ url('ajax-product-childcategory') }}?subcategory_id=" + subcategoryId,
            success: function(res) {
                $.each(res, function(key, value) {
                    adminChildcategoryFilter.append('<option value="' + key + '">' + value + '</option>');
                });
                table.ajax.reload(null, false);
            }
        });
    });

    var initiallySelectedSubcategory = adminSubcategoryFilter.data('selected-subcategory');
    if (initiallySelectedSubcategory) {
        adminSubcategoryFilter.val(String(initiallySelectedSubcategory));
    }

    var initiallySelectedChildcategory = adminChildcategoryFilter.data('selected-childcategory');
    if (initiallySelectedChildcategory) {
        adminChildcategoryFilter.val(String(initiallySelectedChildcategory));
    }

    $(document).on('click', '.change-confirm', function () {
        if ($(this).closest('form').length) {
            $(this).closest('form').submit();
        }
    });

    $('select[name="brand_id"], #admin_product_childcategory_id').on('change', function () {
        table.ajax.reload(null, false);
    });
});
</script>
@endsection

@extends('backEnd.layouts.master')
@section('title',$order_status->name.' Order')
@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('admin.order.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add New</a>
                </div>
                <h4 class="page-title">{{$order_status->name}} Order ({{$order_status->orders_count}})</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row order_page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="alert alert-light border mb-3" role="alert">
                        <strong>Source Guide:</strong>
                        <span class="ms-2"><span class="badge bg-primary">Facebook</span> Orders from Facebook, Instagram, or Meta.</span>
                        <span class="ms-2"><span class="badge bg-dark">TikTok</span> Orders from TikTok traffic.</span>
                        <span class="ms-2"><span class="badge bg-success">Google</span> Orders from Google Ads or paid Google campaigns.</span>
                        <span class="ms-2"><span class="badge bg-info text-dark">Organic</span> Orders from search engines, direct visits, or external referrals.</span>
                        <span class="ms-2"><span class="badge bg-danger">Admin Panel</span> Orders created manually from the admin panel.</span>
                    </div> --}}
                    <div class="row">
                        <div class="col-sm-8">
                            <ul class="action2-btn">
                                <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a></li>
                                <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a></li>
                                <li><a href="{{route('admin.order.bulk_destroy')}}" class="btn rounded-pill btn-danger order_delete"><i class="fe-plus"></i> Delete All</a></li>
                                <li><a href="{{route('admin.order.order_print')}}" class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i> Print</a></li>

                                 <li><a href="{{route('admin.bulk_courier', 'steadfast')}}" class="btn rounded-pill btn-warning multi_order_courier"><i class="fe-truck"></i> state Courier</a></li>
                         
                            </ul>
                        </div>
                        <div class="col-sm-4">
                            <form class="custom_form">
                                <div class="form-group">
                                    <input type="text" name="keyword" placeholder="Search">
                                    <button class="btn  rounded-pill btn-info">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive ">
                        <table id="datatable-buttons" class="table table-striped   w-100">
                            <thead>
                                <tr>
                                    <th style="width:2%">
                                        <div class="form-check"><label class="form-check-label"><input type="checkbox" class="form-check-input checkall" value=""></label>
                                    <th style="width:2%">SL</th>
                    </div>
                    </th>
                    <th style="width:8%">Action</th>
                    <th style="width:8%">Invoice</th>
                    <th style="width:8%">Ip Address</th>
                    <th style="width:10%">Date</th>
                    <th style="width:10%">Name</th>
                    <th style="width:10%">Phone</th>
                    <th style="width:10%">Assign</th>
                    <th style="width:10%">Amount</th>
                    <th style="width:10%">Source</th>
                    <th style="width:10%">Trak Order  </th>
                    <th style="width:10%">Status</th>
                    <th> Froud Checker </th>
                    </tr>
                    </thead>


                    <tbody>
                        @foreach($show_data as $key=>$value)

                        <tr>
                            <td><input type="checkbox" class="checkbox" value="{{$value->id}}"></td>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <div class="button-list custom-btn-list">
                                    <a href="{{route('admin.order.workspace',['invoice_id'=>$value->invoice_id, 'tab' => 'invoice'])}}" title="Invoice"><i class="fe-eye"></i></a>
                                    <a href="{{route('admin.order.workspace',['invoice_id'=>$value->invoice_id, 'tab' => 'manage'])}}" title="Process"><i class="fe-settings"></i></a>
                                    <a href="{{route('admin.order.workspace',['invoice_id'=>$value->invoice_id, 'tab' => 'manage'])}}" title="Edit"><i class="fe-edit"></i></a>
                                    <form method="post" action="{{route('admin.order.destroy')}}" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="{{$value->id}}" name="id">
                                        <button type="submit" title="Delete" class="delete-confirm"><i class="fe-trash-2"></i></button>
                                    </form>
                                    <a data-bs-toggle="modal" data-bs-target="#pathao{{$value->id}}" class="btn btn-success">pathao</a>
                                        <div class="container mb-5">

                                    </div>
                                </div>
                            </td>

                            <td>{{$value->invoice_id}}</td>

                            <td>{{$value->ip_address}}</td>
                            <td>{{date('d-m-Y', strtotime($value->updated_at))}}<br> {{date('h:i:s a', strtotime($value->updated_at))}}</td>
                            <td><strong>{{$value->shipping?$value->shipping->name:''}}</strong>
                                <p>{{$value->shipping?$value->shipping->address:''}}</p>
                            </td>
                            <td>{{$value->shipping?$value->shipping->phone:''}}</td>
                            <td>{{$value->user?$value->user->name:''}}</td>
                            <td>৳{{$value->amount}}</td>
                            <td>
                                <span class="badge {{ $value->marketing_source_badge_class }}">
                                    {{ $value->marketing_source_label }}
                                </span>
                                @if($value->utm_campaign)
                                    <div class="small text-muted mt-1">{{ $value->utm_campaign }}</div>
                                @endif
                            </td>
                            <td>
                             @if($value->courier == 'pathao' && $value->status->name =='Shipped')
                                <a href="https://merchant.pathao.com/tracking?consignment_id={{ $value->tracking_id }}&phone={{ $value->shipping->phone }}"
                                target="_blank"
                                class="btn btn-sm btn-primary">
                                Track Order
                                </a>
                            @endif
                            </td>

                            </td>
                            <td>{{$value->status?$value->status->name:''}}</td>
                            <td>
                                <button class="fraud-checker btn btn-info" data-phone="{{$value->shipping->phone}}">Check Fraud</button>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                <div class="custom-paginate">
                    {{$show_data->links('pagination::bootstrap-4')}}
                </div>
            </div> <!-- end card body-->

        </div> <!-- end card -->
    </div><!-- end col-->
</div>
</div>
<!-- Assign User End -->
<div class="modal fade" id="asignUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.order.assign')}}" id="order_assign">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Select..</option>
                            @foreach($users as $key=>$value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Assign User End-->

<!-- Assign User End -->
<div class="modal fade" id="changeStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.order.status')}}" id="order_status_form">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="order_status" id="order_status" class="form-control">
                            <option value="">Select..</option>
                            @foreach($orderstatus as $key=>$value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Assign User End-->

<!-- pathao coureir start -->
@foreach($show_data as $key=>$value)
<div class="modal fade" id="pathao{{$value->id}}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pathao Courier - {{$value->invoice_id}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.order.pathao')}}" id="order_sendto_pathao">

                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="id" value="{{$value->id}}">
                        <label for="pathaostore" class="form-label">Store</label>
                        <select name="pathaostore" id="pathaostore" class="pathaostore form-control">
                            <option value="">Select Store...</option>

                            @if(isset($pathaostore['data']['data']))
                            @foreach($pathaostore['data']['data'] as $key=>$store)
                            <option value="{{$store['store_id']}}">{{$store['store_name']}}</option>
                            @endforeach
                            @else
                            @endif
                        </select>
                        @if ($errors->has('pathaostore'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaostore') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                    <div class="form-group mt-3">
                        <label for="pathaocity" class="form-label">City</label>
                        <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%">
                            <option value="">Select City...</option>
                            @if(isset($pathaocities['data']['data']))
                            @foreach($pathaocities['data']['data'] as $key=>$city)
                            <option value="{{$city['city_id']}}">{{$city['city_name']}}</option>
                            @endforeach
                            @else
                            @endif
                        </select>
                        @if ($errors->has('pathaocity'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaocity') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                    <div class="form-group mt-3">
                        <label for="" class="form-label">Zone</label>
                        <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}" value="{{ old('pathaozone') }}" style="width:100%">
                        </select>
                        @if ($errors->has('pathaozone'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaozone') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                    <div class="form-group mt-3">
                        <label for="" class="form-label">Area</label>
                        <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}" value="{{ old('pathaoarea') }}" style="width:100%">
                        </select>
                        @if ($errors->has('pathaoarea'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaoarea') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
<!-- Fraud Checker Modal Start -->
<div id="fraudModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:8px; max-width:760px; width:92%; padding:20px; position:relative; box-shadow:0 4px 15px rgba(0,0,0,0.3);">
        <button id="fraudModalClose" style="position:absolute; top:12px; right:12px; background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        <div style="display:flex; align-items:center; gap:12px; margin-top:0; margin-bottom:12px;">
            <h2 style="margin:0; color:#0f172a;">Fraud Check Result</h2>
            <span id="fraudCacheBadge" style="display:none; background:#dcfce7; color:#166534; border:1px solid #86efac; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">Cached Result</span>
        </div>
        <div id="fraudModalContent" style="max-height:420px; overflow-y:auto; font-family: Arial, sans-serif;"></div>
    </div>
</div>
<style>
    #fraudModal table {
        width: 100%;
        border-collapse: collapse;
    }

    #fraudModal th,
    #fraudModal td {
        border: 1px solid #ddd;
        padding: 8px 12px;
        text-align: left;
    }

    #fraudModal th {
        background-color: #0f172a;
        color: white;
        font-weight: bold;
    }
</style>
<!-- Fraud Checker Modal End -->

<!-- pathao courier  End-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".checkall").on('change', function() {
            $(".checkbox").prop('checked', $(this).is(":checked"));
        });

        const fraudCheckerConfig = @json([
            'enabled' => (bool) $fraudCheckerConfig,
            'endpoint' => route('admin.order.fraud_check'),
        ]);
        const fraudModal = document.getElementById('fraudModal');
        const fraudModalContent = document.getElementById('fraudModalContent');
        const fraudModalClose = document.getElementById('fraudModalClose');
        const fraudCacheBadge = document.getElementById('fraudCacheBadge');

        if (fraudModal && fraudModalClose && fraudModalContent) {
            fraudModalClose.addEventListener('click', function() {
                fraudModal.style.display = 'none';
                fraudModalContent.innerHTML = '';
                if (fraudCacheBadge) {
                    fraudCacheBadge.style.display = 'none';
                }
            });

            window.addEventListener('click', function(e) {
                if (e.target === fraudModal) {
                    fraudModal.style.display = 'none';
                    fraudModalContent.innerHTML = '';
                }
            });

            $(document).on('click', '.fraud-checker', function() {
                const phone = $(this).data('phone');

                if (!phone) {
                    toastr.error('Phone number not found');
                    return;
                }

                if (!fraudCheckerConfig.enabled) {
                    toastr.error('Fraud checker API key not configured');
                    return;
                }

                fraudModalContent.innerHTML = '<p style="padding:20px;">Loading fraud data...</p>';
                fraudModal.style.display = 'flex';

                fetch(fraudCheckerConfig.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        phone: String(phone),
                    }),
                })
                    .then(function(res) {
                        return res.json().then(function(data) {
                            return {
                                ok: res.ok,
                                data: data,
                            };
                        });
                    })
                    .then(function(result) {
                        const data = result.data;

                        if (!result.ok) {
                            if (fraudCacheBadge) {
                                fraudCacheBadge.style.display = 'none';
                            }
                            fraudModalContent.innerHTML = '<p style="padding:20px;">' + (data.message || 'Failed to fetch data.') + '</p>';
                            return;
                        }

                        if (fraudCacheBadge) {
                            fraudCacheBadge.style.display = data.cached ? 'inline-flex' : 'none';
                        }
                        fraudModalContent.innerHTML = '';

                        const courierPayload = data?.courierData || data?.data?.courierData || data?.data || data?.result || null;

                        if (!courierPayload || typeof courierPayload !== 'object') {
                            fraudModalContent.innerHTML = '<p style="padding:20px;">' + (data?.message || 'No data found.') + '</p>';
                            return;
                        }

                        const courierData = courierPayload;
                        const summary = courierData.summary || courierData.Summary || data?.summary || data?.data?.summary || {};
                        const table = document.createElement('table');
                        table.innerHTML = `
                            <tr>
                                <th>Logo</th>
                                <th>Courier Name</th>
                                <th>Total Parcel</th>
                                <th>Successful</th>
                                <th>Cancelled</th>
                                <th>Success Rate</th>
                            </tr>
                        `;

                        for (const key in courierData) {
                            if (key === 'summary') continue;

                            const courier = courierData[key];
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><img src="${courier.logo}" alt="${courier.name}" width="40" /></td>
                                <td>${courier.name}</td>
                                <td>${courier.total_parcel}</td>
                                <td>${courier.success_parcel}</td>
                                <td>${courier.cancelled_parcel}</td>
                                <td>${courier.success_ratio}%</td>
                            `;
                            table.appendChild(tr);
                        }

                        const summaryRow = document.createElement('tr');
                        summaryRow.style.backgroundColor = '#f3f3f3';
                        summaryRow.innerHTML = `
                            <td colspan="2"><strong>Total Summary</strong></td>
                            <td><strong>${summary.total_parcel || 0}</strong></td>
                            <td><strong>${summary.success_parcel || 0}</strong></td>
                            <td><strong>${summary.cancelled_parcel || 0}</strong></td>
                            <td><strong>${summary.success_ratio || 0}%</strong></td>
                        `;
                        table.appendChild(summaryRow);

                        fraudModalContent.appendChild(table);
                    })
                    .catch(function(err) {
                        fraudModalContent.innerHTML = '<p style="padding:20px;">Failed to fetch data.</p>';
                        console.error(err);
                    });
            });
        }

        // order assign
        $(document).on('submit', 'form#order_assign', function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            let user_id = $(document).find('select#user_id').val();

            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    user_id,
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });

        // order status change
        $(document).on('submit', 'form#order_status_form', function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            let order_status = $(document).find('select#order_status').val();

            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    order_status,
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });
        // order delete
        $(document).on('click', '.order_delete', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });

        // multiple print
        $(document).on('click', '.multi_order_print', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select Atleast One Order!');
                return;
            }
            $.ajax({
                type: 'GET',
                url,
                data: {
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        console.log(res.items, res.info);
                        var myWindow = window.open("", "_blank");
                        myWindow.document.write(res.view);
                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });
        });
        // multiple courier
        $(document).on('click', '.multi_order_courier', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });
    })
</script>
@endsection

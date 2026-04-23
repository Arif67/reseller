@extends('backEnd.layouts.master')
@section('title','App Setting')

@section('css')
@endsection

@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">

                </div>
                <h4 class="page-title">App Setting</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row g-3">
        <div class="col-12">
            <ul class="nav nav-pills flex-wrap gap-2 mb-3" id="app-setting-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-general" data-bs-toggle="pill" data-bs-target="#pane-general" type="button" role="tab" aria-controls="pane-general" aria-selected="true">
                        General Settings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-social" data-bs-toggle="pill" data-bs-target="#pane-social" type="button" role="tab" aria-controls="pane-social" aria-selected="false">
                        Social Media
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-contact" data-bs-toggle="pill" data-bs-target="#pane-contact" type="button" role="tab" aria-controls="pane-contact" aria-selected="false">
                        Contact
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pages" data-bs-toggle="pill" data-bs-target="#pane-pages" type="button" role="tab" aria-controls="pane-pages" aria-selected="false">
                        Create Page
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-shipping" data-bs-toggle="pill" data-bs-target="#pane-shipping" type="button" role="tab" aria-controls="pane-shipping" aria-selected="false">
                        Shipping Charge
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-orderstatus" data-bs-toggle="pill" data-bs-target="#pane-orderstatus" type="button" role="tab" aria-controls="pane-orderstatus" aria-selected="false">
                        Order Status
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="app-setting-tab-content">
                <div class="tab-pane fade show active" id="pane-general" role="tabpanel" aria-labelledby="tab-general" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end mb-3">
                                <button type="button" class="btn btn-primary rounded-pill open-create-modal" data-url="{{ route('settings.create', ['modal' => 1]) }}">Create</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Name</th>
                                            <th>White Logo</th>
                                            <th>Dark Logo</th>
                                            <th>Favicon</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($show_data as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->name }}</td>
                                            <td><img src="{{ asset($value->white_logo) }}" class="backend-image" alt=""></td>
                                            <td><img src="{{ asset($value->dark_logo) }}" class="backend-image" alt=""></td>
                                            <td><img src="{{ asset($value->favicon) }}" class="backend-image" alt=""></td>
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
                                                        <form method="post" action="{{ route('settings.inactive') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button>
                                                        </form>
                                                    @else
                                                        <form method="post" action="{{ route('settings.active') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('settings.edit', $value->id) }}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-social" role="tabpanel" aria-labelledby="tab-social" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end mb-3">
                                <button type="button" class="btn btn-primary rounded-pill open-create-modal" data-url="{{ route('socialmedias.create', ['modal' => 1]) }}">Create</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Name</th>
                                            <th>Icon</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($socialmedias as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->title }}</td>
                                            <td>{{ $value->icon }}</td>
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
                                                        <form method="post" action="{{ route('socialmedias.inactive') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button>
                                                        </form>
                                                    @else
                                                        <form method="post" action="{{ route('socialmedias.active') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('socialmedias.edit', $value->id) }}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                                    <form method="post" action="{{ route('socialmedias.destroy') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                        <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end mb-3">
                                <button type="button" class="btn btn-primary rounded-pill open-create-modal" data-url="{{ route('contact.create', ['modal' => 1]) }}">Create</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contacts as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->phone }}</td>
                                            <td>{{ $value->email }}</td>
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
                                                        <form method="post" action="{{ route('contact.inactive') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button>
                                                        </form>
                                                    @else
                                                        <form method="post" action="{{ route('contact.active') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('contact.edit', $value->id) }}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                                    <form method="post" action="{{ route('contact.destroy') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                        <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-pages" role="tabpanel" aria-labelledby="tab-pages" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end mb-3">
                                <button type="button" class="btn btn-primary rounded-pill open-create-modal" data-url="{{ route('pages.create', ['modal' => 1]) }}">Create</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Title</th>
                                            <th>Slug</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pages as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->title }}</td>
                                            <td>{{ $value->slug }}</td>
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
                                                        <form method="post" action="{{ route('pages.inactive') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button>
                                                        </form>
                                                    @else
                                                        <form method="post" action="{{ route('pages.active') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('pages.edit', $value->id) }}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                                    <form method="post" action="{{ route('pages.destroy') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                        <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-shipping" role="tabpanel" aria-labelledby="tab-shipping" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end mb-3">
                                <button type="button" class="btn btn-primary rounded-pill open-create-modal" data-url="{{ route('shippingcharges.create', ['modal' => 1]) }}">Create</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Area</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shippingcharges as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->amount }}</td>
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
                                                        <form method="post" action="{{ route('shippingcharges.inactive') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button>
                                                        </form>
                                                    @else
                                                        <form method="post" action="{{ route('shippingcharges.active') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('shippingcharges.edit', $value->id) }}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                                    <form method="post" action="{{ route('shippingcharges.destroy') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                        <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-orderstatus" role="tabpanel" aria-labelledby="tab-orderstatus" tabindex="0">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end mb-3">
                                <button type="button" class="btn btn-primary rounded-pill open-create-modal" data-url="{{ route('orderstatus.create', ['modal' => 1]) }}">Create</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orderstatuses as $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->slug }}</td>
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
                                                        <form method="post" action="{{ route('orderstatus.inactive') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-secondary waves-effect waves-light change-confirm"><i class="fe-thumbs-down"></i></button>
                                                        </form>
                                                    @else
                                                        <form method="post" action="{{ route('orderstatus.active') }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                            <button type="button" class="btn btn-xs btn-success waves-effect waves-light change-confirm"><i class="fe-thumbs-up"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('orderstatus.edit', $value->id) }}" class="btn btn-xs btn-primary waves-effect waves-light"><i class="fe-edit-1"></i></a>
                                                    <form method="post" action="{{ route('orderstatus.destroy') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                                        <button type="submit" class="btn btn-xs btn-danger waves-effect waves-light delete-confirm"><i class="mdi mdi-close"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="createModalFrame" src="" style="width: 100%; height: 75vh; border: 0;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    (function () {
        var modalEl = document.getElementById('createModal');
        var frame = document.getElementById('createModalFrame');
        if (!modalEl || !frame) {
            return;
        }

        document.querySelectorAll('.open-create-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-url');
                if (url) {
                    frame.setAttribute('src', url);
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            });
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            frame.setAttribute('src', '');
        });
    })();
</script>
@endsection

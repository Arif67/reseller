@extends('backEnd.layouts.master')
@section('title','Courier API')
@section('css')
<style>
  .courier-settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
  }

  .courier-settings-card {
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
  }

  .courier-settings-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 20px 22px;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(135deg, #f8fafc 0%, #eef6ff 100%);
  }

  .courier-settings-head h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
  }

  .courier-settings-body {
    padding: 22px;
  }

  .courier-settings-type {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #64748b;
  }
</style>
@endsection

@section('content')
<div class="container-fluid">
  @php
    $activeFormId = old('id');
    $activeConfigGroup = old('config_group', 'courier');
  @endphp
  <div class="row">
    <div class="col-12">
      <div class="page-title-box">
        <h4 class="page-title">Courier API</h4>
      </div>
    </div>
  </div>

  <div class="courier-settings-grid">
    @foreach($couriers as $courier)
      <div class="courier-settings-card">
        <div class="courier-settings-head">
          <div>
            <div class="courier-settings-type">{{ $courier->type }}</div>
            <h4>{{ $courier->section_title }}</h4>
          </div>
          @if($courier->status == 1)
            <span class="badge bg-soft-success text-success">Active</span>
          @else
            <span class="badge bg-soft-danger text-danger">Inactive</span>
          @endif
        </div>

        <div class="courier-settings-body">
          @if(session('courier_test_type') === $courier->type)
            <div class="alert {{ session('courier_test_status') === 'success' ? 'alert-success' : 'alert-danger' }} py-2 px-3 mb-3">
              <strong>{{ session('courier_test_status') === 'success' ? 'Connection OK:' : 'Connection Failed:' }}</strong>
              {{ session('courier_test_message') }}
            </div>
          @endif
          <form action="{{ route('courierapi.update') }}" method="POST" class="row" data-parsley-validate>
            @csrf
            <input type="hidden" name="id" value="{{ $courier->id }}">
            <input type="hidden" name="config_group" value="courier">

            @foreach($courier->form_fields as $field)
              <div class="col-sm-12">
                <div class="form-group mb-3">
                  <label for="{{ $courier->type }}_{{ $field }}" class="form-label">{{ $courier->field_labels[$field] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $field)) }} *</label>
                  <input
                    type="text"
                    class="form-control @error($field) is-invalid @enderror"
                    name="{{ $field }}"
                    value="{{ $activeConfigGroup === 'courier' && (string) $activeFormId === (string) $courier->id ? old($field, $courier->{$field}) : $courier->{$field} }}"
                    id="{{ $courier->type }}_{{ $field }}"
                    @if(in_array($field, ['url', 'api_key', 'secret_key'])) required @endif
                    @if($courier->type === 'pathao' && $field === 'token') readonly @endif
                  />
                  @if($courier->type === 'pathao' && $field === 'token')
                    <small class="text-muted d-block mt-1">Token auto generate hobe. Pathao credential change korle eta auto refresh hobe.</small>
                  @endif
                  @error($field)
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
            @endforeach

            <div class="col-sm-12 mb-3">
              <div class="form-group">
                <label for="{{ $courier->type }}_status" class="d-block">Status</label>
                <label class="switch">
                  <input type="checkbox" value="1" name="status" id="{{ $courier->type }}_status" @if($courier->status == 1) checked @endif />
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="col-sm-12 d-flex flex-wrap gap-2">
              <button type="submit" class="btn btn-success rounded-pill">Save {{ $courier->section_title }}</button>
              <button type="submit" class="btn btn-outline-primary rounded-pill" formaction="{{ route('courierapi.test_connection') }}">Test Connection</button>
              @if($courier->type === 'pathao')
                <button type="submit" class="btn btn-primary rounded-pill" formaction="{{ route('courierapi.pathao_regenerate_token') }}">Regenerate Access Token</button>
              @endif
            </div>
          </form>
        </div>
      </div>
    @endforeach

    <div class="courier-settings-card">
      <div class="courier-settings-head">
        <div>
          <div class="courier-settings-type">fraud_checker</div>
          <h4>Fraud Checker</h4>
        </div>
        @if($fraudCheckerConfig->status == 1)
          <span class="badge bg-soft-success text-success">Active</span>
        @else
          <span class="badge bg-soft-danger text-danger">Inactive</span>
        @endif
      </div>

      <div class="courier-settings-body">
        @if(session('courier_test_type') === 'fraud_checker')
          <div class="alert {{ session('courier_test_status') === 'success' ? 'alert-success' : 'alert-danger' }} py-2 px-3 mb-3">
            <strong>{{ session('courier_test_status') === 'success' ? 'Connection OK:' : 'Connection Failed:' }}</strong>
            {{ session('courier_test_message') }}
          </div>
        @endif
        <form action="{{ route('courierapi.update') }}" method="POST" class="row" data-parsley-validate>
          @csrf
          <input type="hidden" name="id" value="{{ $fraudCheckerConfig->id }}">
          <input type="hidden" name="config_group" value="fraud_checker">

          <div class="col-sm-12">
            <div class="form-group mb-3">
              <label for="fraud_checker_name" class="form-label">Provider Name *</label>
              <input
                type="text"
                class="form-control"
                name="name"
                value="{{ $activeConfigGroup === 'fraud_checker' && (string) $activeFormId === (string) $fraudCheckerConfig->id ? old('name', $fraudCheckerConfig->name) : $fraudCheckerConfig->name }}"
                id="fraud_checker_name"
                required
              />
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group mb-3">
              <label for="fraud_checker_url" class="form-label">API URL *</label>
              <input
                type="text"
                class="form-control"
                name="url"
                value="{{ $activeConfigGroup === 'fraud_checker' && (string) $activeFormId === (string) $fraudCheckerConfig->id ? old('url', $fraudCheckerConfig->url) : $fraudCheckerConfig->url }}"
                id="fraud_checker_url"
                required
              />
            </div>
          </div>

          <div class="col-sm-12">
            <div class="form-group mb-3">
              <label for="fraud_checker_api_key" class="form-label">API Key *</label>
              <input
                type="text"
                class="form-control"
                name="api_key"
                value="{{ $activeConfigGroup === 'fraud_checker' && (string) $activeFormId === (string) $fraudCheckerConfig->id ? old('api_key', $fraudCheckerConfig->api_key) : $fraudCheckerConfig->api_key }}"
                id="fraud_checker_api_key"
                required
              />
            </div>
          </div>

          <div class="col-sm-12 mb-3">
            <div class="form-group">
              <label for="fraud_checker_status" class="d-block">Status</label>
              <label class="switch">
                <input type="checkbox" value="1" name="status" id="fraud_checker_status" @if($fraudCheckerConfig->status == 1) checked @endif />
                <span class="slider round"></span>
              </label>
            </div>
          </div>

          <div class="col-sm-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success rounded-pill">Save Fraud Checker</button>
            <button type="submit" class="btn btn-outline-primary rounded-pill" formaction="{{ route('courierapi.test_connection') }}">Test Connection</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
@endsection

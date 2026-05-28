@extends('backEnd.layouts.master')
@section('title','POS')
@section('css')
<style>
 .increment_btn,
 .remove_btn {
  margin-top: -17px;
  margin-bottom: 10px;
 }
 .product-browser-wrap {
  max-height: 72vh;
  overflow: auto;
  padding-right: 6px;
 }
 .product-browser-toolbar {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 14px;
 }
 .product-browser-toolbar-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
 }
 .product-browser-count {
  padding: 6px 10px;
  border-radius: 999px;
  background: #e2e8f0;
  color: #0f172a;
  font-size: 0.78rem;
  font-weight: 700;
 }
 .product-browser-mode-list,
 .product-browser-category-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
 }
 .product-browser-mode-btn,
 .product-browser-category-btn {
  border: 1px solid rgba(148, 163, 184, 0.28);
  background: #fff;
  color: #334155;
  border-radius: 999px;
  padding: 8px 12px;
  font-weight: 600;
  font-size: 0.82rem;
  transition: all .15s ease;
 }
 .product-browser-mode-btn.active,
 .product-browser-category-btn.active {
  background: #0f172a;
  color: #fff;
  border-color: #0f172a;
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
 }
 .product-browser-mode-btn[data-barcode-only="1"].active {
  background: #0f766e;
  border-color: #0f766e;
 }
 .product-browser-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
 }
 .product-browser-card {
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 16px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
 }
 .product-browser-thumb {
  aspect-ratio: 1 / 1;
  background: #f7f8fb;
  overflow: hidden;
 }
 .product-browser-thumb img,
 .product-preview-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
 }
 .product-browser-body {
  padding: 12px 14px 14px;
 }
 .product-browser-title,
 .product-preview-title {
  font-weight: 700;
  color: #111827;
  line-height: 1.35;
 }
 .product-browser-title {
  font-size: 0.98rem;
 }
 .product-browser-meta,
 .product-preview-price {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 6px;
 }
 .product-browser-submeta {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  margin-top: 10px;
 }
 .product-preview-panel {
  padding: 6px;
 }
 .product-preview-image {
  border-radius: 22px;
  overflow: hidden;
  background: linear-gradient(180deg, #f7f8fb 0%, #eef2ff 100%);
  aspect-ratio: 1 / 1;
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.10);
 }
 .product-preview-gallery {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 8px;
  margin-top: 10px;
 }
 .product-preview-gallery-item {
  padding: 0;
  border: 0;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 1 / 1;
  background: #f3f4f6;
 }
 .product-preview-gallery-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
 }
 .product-preview-desc {
  color: #4b5563;
  line-height: 1.7;
 }
 .product-preview-section-title {
  font-weight: 700;
  margin-bottom: 10px;
 }
 .preview-variant-list {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
 }
 .preview-variant-item {
  position: relative;
  text-align: left;
  white-space: normal;
  padding: 10px 12px;
  border-radius: 16px;
 }
 .preview-variant-item.is-selected {
  border-color: #2563eb !important;
  background: #eff6ff;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
 }
 .preview-variant-item.is-selected .variant-selected-indicator {
  opacity: 1;
  transform: scale(1);
 }
 .variant-selected-indicator {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 22px;
  height: 22px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: #2563eb;
  color: #fff;
  font-size: 0.72rem;
  box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
  opacity: 0;
  transform: scale(0.85);
  transition: all 0.15s ease;
 }
 .variant-attribute-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin: 8px 0 6px;
 }
 .variant-attr-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 8px;
  border-radius: 999px;
  background: #f8fafc;
  border: 1px solid rgba(148, 163, 184, 0.25);
  color: #475569;
  font-size: 0.76rem;
  font-weight: 600;
 }
 .variant-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
  margin: 2px 0 4px;
 }
 .variant-color-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  margin: 8px 0 4px;
  padding: 4px 8px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.04);
  color: #334155;
  font-size: 0.78rem;
  font-weight: 600;
 }
 .variant-color-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 1px solid rgba(15, 23, 42, 0.14);
  display: inline-block;
 }
 .preview-qty-wrap {
  min-width: 100px;
 }
 .preview-qty-input {
  width: 110px;
  border-radius: 14px;
 }
 .qty-cart.vcart-qty .quantity {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f8fbff;
  border: 1px solid #dce7f5;
  border-radius: 999px;
  padding: 6px;
 }
 .qty-cart.vcart-qty .quantity .minus,
 .qty-cart.vcart-qty .quantity .plus {
  width: 32px;
  height: 32px;
  border: 0;
  border-radius: 999px;
  background: #fff;
  color: #0f172a;
  font-size: 18px;
  font-weight: 800;
  line-height: 1;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
 }
 .qty-cart.vcart-qty .quantity input {
  width: 42px;
  border: 0;
  background: transparent;
  text-align: center;
  font-size: 16px;
  font-weight: 700;
  line-height: 1;
  color: #0f172a;
  padding: 0;
 }
 .product-preview-hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 18px;
  background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
  color: #fff;
  margin-bottom: 16px;
 }
 .product-preview-hero .product-preview-title {
  color: #fff;
  font-size: 1.35rem;
  margin-bottom: 4px;
 }
 .product-preview-hero .product-preview-price {
  color: #fff;
  margin-top: 2px;
 }
 .product-preview-hero del,
 .product-browser-meta del {
  opacity: .72;
 }
 .product-preview-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
 }
 .product-preview-badges .badge {
  padding: 8px 10px;
  border-radius: 999px;
 }
 .product-preview-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 16px;
 }
 .product-preview-actions .btn {
  border-radius: 14px;
  padding: 10px 14px;
 }
 .product-preview-modal .modal-content {
  border: 0;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 30px 90px rgba(15, 23, 42, 0.32);
 }
 .product-preview-modal .modal-header {
  border-bottom: 0;
  padding: 18px 22px 0;
 }
 .product-preview-modal .modal-body {
  padding: 18px 22px 22px;
  background: radial-gradient(circle at top right, rgba(99, 102, 241, .08), transparent 28%),
              radial-gradient(circle at left, rgba(16, 185, 129, .06), transparent 24%),
              #fff;
 }
 .product-preview-sticky {
  position: sticky;
  bottom: 0;
  z-index: 2;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-top: 18px;
  padding: 14px 16px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
 }
 .product-preview-sticky-text {
  color: #334155;
  font-weight: 600;
 }
 .product-preview-modal .modal-title {
  font-weight: 800;
  letter-spacing: -0.02em;
 }
 .catalog-load-more-wrap {
  margin-top: 12px;
 }
 .catalog-load-more-btn {
  border-radius: 16px;
  border-color: rgba(15, 23, 42, 0.14);
  background: #fff;
 }
 @media (max-width: 991.98px) {
  .product-browser-wrap {
   max-height: none;
  }
  .product-browser-grid {
   grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .preview-variant-list {
   grid-template-columns: 1fr;
  }
  .product-preview-gallery {
   grid-template-columns: repeat(4, minmax(0, 1fr));
  }
 }
 @media (max-width: 575.98px) {
  .product-browser-grid {
   grid-template-columns: 1fr;
  }
  .product-preview-sticky {
   flex-direction: column;
   align-items: stretch;
  }
  .product-preview-gallery {
   grid-template-columns: repeat(3, minmax(0, 1fr));
  }
 }
</style>
<link href="{{asset('backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection @section('content')

<div class="container-fluid">
 <div class="row">
  <div class="col-12">
   <div class="page-title-box">
    <div class="page-title-right">
     <form method="get" action="{{route('admin.order.cart_clear')}}" class="d-inline">@csrf
      <button type="submit" class="btn btn-danger rounded-pill delete-confirm" title="Delete"><i class="fas fa-trash-alt"></i> Cart Clear</button>
     </form>
    </div>
    <h4 class="page-title">POS</h4>
   </div>
  </div>
 </div>
 <!-- end page title -->
 <div class="row g-4">
  <div class="col-lg-6">
   <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="mb-0">Product List</h5>
          <small class="text-muted">Quickly preview products, choose variations, and add them to the cart.</small>
        </div>
        <span class="product-browser-count" id="catalogProductCount">{{ method_exists($catalogProducts, 'count') && method_exists($catalogProducts, 'total') ? ($catalogProducts->count() . ' / ' . $catalogProducts->total()) : count($catalogProducts) }}</span>
      </div>
      <div class="product-browser-toolbar">
       <div class="product-browser-mode-list">
        <button type="button" class="product-browser-mode-btn {{ ($catalogFilters['mode'] ?? 'recent') === 'recent' ? 'active' : '' }}" data-browser-mode="recent">Recent</button>
        <button type="button" class="product-browser-mode-btn {{ ($catalogFilters['mode'] ?? 'recent') === 'top' ? 'active' : '' }}" data-browser-mode="top">Best Selling</button>
        <button type="button" class="product-browser-mode-btn {{ !empty($catalogFilters['barcode_only']) ? 'active' : '' }}" data-barcode-only="1">Barcode Only</button>
       </div>
       <div class="product-browser-category-list">
        <button type="button" class="product-browser-category-btn {{ empty($catalogFilters['category_id']) ? 'active' : '' }}" data-category-id="">All</button>
        @foreach($categories as $category)
         <button type="button" class="product-browser-category-btn {{ (int) ($catalogFilters['category_id'] ?? 0) === (int) $category->id ? 'active' : '' }}" data-category-id="{{ $category->id }}">
          {{ $category->name }} <span class="text-muted">({{ $category->product_count ?? 0 }})</span>
         </button>
        @endforeach
       </div>
      </div>
      <div class="form-group mb-3">
        <label for="product_id" class="form-label">Search Product / Barcode</label>
        <div class="pos_search">
            <input type="text" placeholder="Search Product or Scan Barcode ..." value="{{ $catalogFilters['keyword'] ?? '' }}" class="search_click" name="keyword" autofocus/>
            <button type="button"><i data-feather="search"></i></button>
        </div>
        <div class="search_result"></div>
      </div>
      <div class="product-browser-wrap">
        @include('backEnd.order.partials.product-browser', ['catalogProducts' => $catalogProducts, 'filters' => $catalogFilters])
      </div>
    </div>
   </div>
  </div>
  <div class="col-lg-6">
   <div class="card">
    <div class="card-body">
      <form action="{{route('admin.order.store')}}" method="POST" class="row pos_form" data-parsley-validate="" enctype="multipart/form-data">
      @csrf
      <div class="col-sm-12">
       <table class="table table-bordered table-responsive-sm">
        <thead>
         <tr></tr>
         <tr>
          <th style="width: 10%;">Image</th>
          <th style="width: 25%;">Name</th>
          <th style="width: 15%;">Quantity</th>
          <th style="width: 15%;">Sell Price</th>
          <th style="width: 15%;">Discount</th>
          <th style="width: 15%;">Sub Total</th>
          <th style="width: 15%;">Action</th>
         </tr>
        </thead>
        <tbody id="cartTable">
         @php $product_discount = 0; @endphp
         @foreach($cartinfo as $key=>$value)
         <tr data-row-id="{{$value->rowId}}">
           <td><img height="30" src="{{asset($value->options->image)}}"> @php $selectedAttributes = $value->options->selected_attributes ?? []; @endphp
            @if(!empty($selectedAttributes))
              @foreach($selectedAttributes as $selectedAttribute)
                <p>{{ $selectedAttribute['attribute'] ?? '' }}: {{ $selectedAttribute['value'] ?? '' }}</p>
              @endforeach
            @else
              <p>{{$value->options->product_size}}</p>
              <p>{{$value->options->product_color}}</p>
            @endif</td>
          <td>{{$value->name}} </td>
          <td>
           <div class="qty-cart vcart-qty">
            <div class="quantity">
             <button type="button" class="minus cart_decrement" value="{{$value->qty}}" data-id="{{$value->rowId}}">-</button>
             <input type="text" value="{{$value->qty}}" readonly />
             <button type="button" class="plus cart_increment" value="{{$value->qty}}" data-id="{{$value->rowId}}">+</button>
            </div>
           </div>
          </td>
          <td>
           <div class="discount">
            <input type="text" inputmode="decimal" class="product_price" value="{{$value->price}}" placeholder="0.00" data-id="{{$value->rowId}}">
           </div>
          </td>
          <td class="discount"><input type="text" inputmode="decimal" class="product_discount" value="{{$value->options->product_discount ?? 0}}" placeholder="0.00" data-id="{{$value->rowId}}" data-price="{{$value->price}}" data-qty="{{$value->qty}}" /></td>
          <td class="line_subtotal">{{ ($value->price - ($value->options->product_discount ?? 0)) * $value->qty }}</td>
          <td>
           @if($value->options->type == 0)
            <button type="button" class="btn btn-primary btn-xs js-product-preview" data-id="{{$value->id}}" data-update-row-id="{{$value->rowId}}" title="Edit Attribute"><i class="fa fa-edit"></i></button>
           @endif
           <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{$value->rowId}}"><i class="fa fa-times"></i></button>
          </td>
         </tr>
         @php $product_discount += ($value->options->product_discount ?? 0) * $value->qty; Session::put('product_discount',$product_discount); @endphp @endforeach
        </tbody>
       </table>
      </div>
      <!-- custome address -->
      <div class="col-sm-6">
       <div class="form-check mb-2">
          <label class="form-check-label" for="guest_customer" >
            Guest Customer
          </label>
          <input class="form-check-input" type="checkbox" name="guest_customer" value="1" id="guest_customer">
        </div>
       <div class="row new_customer">
        <div class="col-sm-12">
         <div class="form-group mb-2">
          <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Customer Name" name="name" value=""  />
          @error('name')
          <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
          </span>
          @enderror
         </div>
        </div>
        <!-- col-end -->
        <div class="col-sm-12">
         <div class="form-group mb-2">
          <input type="number" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Customer Number" name="phone" value=""  />
          @error('phone')
          <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
          </span>
          @enderror
         </div>
        </div>
        <!-- col-end -->
        <div class="col-sm-12">
         <div class="form-group mb-3">
          <input type="address" placeholder="Address" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value=""  />
          @error('email')
          <span class="invalid-feedback" role="alert">
           <strong>{{ $message }}</strong>
          </span>
          @enderror
         </div>
        </div>
        <div class="col-sm-12">
         <div class="form-group mb-3">
          <label for="marketing_source" class="form-label">Order Source</label>
          <select id="marketing_source" name="marketing_source" class="form-control">
            <option value="admin" {{ old('marketing_source', 'admin') === 'admin' ? 'selected' : '' }}>Admin Panel</option>
            <option value="facebook" {{ old('marketing_source') === 'facebook' ? 'selected' : '' }}>Facebook</option>
            <option value="messenger" {{ old('marketing_source') === 'messenger' ? 'selected' : '' }}>Messenger</option>
            <option value="whatsapp" {{ old('marketing_source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
            <option value="tiktok" {{ old('marketing_source') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
            <option value="google" {{ old('marketing_source') === 'google' ? 'selected' : '' }}>Google</option>
            <option value="organic" {{ old('marketing_source') === 'organic' ? 'selected' : '' }}>Organic</option>
          </select>
         </div>
        </div>
        <div class="col-sm-12">
         <div class="form-group mb-3">
                 <select type="area" id="area" class="form-control @error('area') is-invalid @enderror" name="area"   required>
                    <option value="">Select....</option>
                   @foreach ($shippingcharge as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                   @endforeach

                </select>
                @error('area')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-sm-12">
         <div class="form-group mb-3">
          <label for="payment_method" class="form-label">Payment Method *</label>
          <select id="payment_method" name="payment_method" class="form-control" required>
            <option value="Cash" {{ old('payment_method', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Card" {{ old('payment_method') === 'Card' ? 'selected' : '' }}>Card</option>
            <option value="bKash" {{ old('payment_method') === 'bKash' ? 'selected' : '' }}>bKash</option>
            <option value="Nagad" {{ old('payment_method') === 'Nagad' ? 'selected' : '' }}>Nagad</option>
            <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="COD" {{ old('payment_method') === 'COD' ? 'selected' : '' }}>COD</option>
          </select>
         </div>
        </div>
        <div class="col-sm-12">
         <div class="form-group mb-3">
          <label for="paid_amount" class="form-label">Received Amount</label>
          <input type="number" step="0.01" min="0" id="paid_amount" class="form-control" name="paid_amount" value="{{ old('paid_amount') }}" placeholder="Leave empty to auto-fill full amount" />
         </div>
        </div>
        <div class="col-sm-12">
         <div class="alert alert-light border mb-3">
          <div class="d-flex justify-content-between">
           <span>Due</span>
           <strong id="pos_due_amount">0.00</strong>
          </div>
          <div class="d-flex justify-content-between">
           <span>Change</span>
           <strong id="pos_change_amount">0.00</strong>
          </div>
         </div>
        </div>
        <!-- col-end -->
       </div>
      </div>
      <!-- cart total -->
      <div class="col-sm-6">
       <table class="table table-bordered">
        <tbody id="cart_details">
         @php $subtotal = Cart::instance('pos_shopping')->subtotal(); $subtotal = str_replace(',','',$subtotal); $subtotal = str_replace('.00', '',$subtotal); $shipping = Session::get('pos_shipping'); $total_discount =
         Session::get('pos_discount')+Session::get('product_discount'); @endphp
         <tr>
          <td>Sub Total</td>
          <td>{{$subtotal}}</td>
         </tr>
         <tr>
          <td>Shipping Fee</td>
          <td>{{$shipping}}</td>
         </tr>
         <tr>
          <td>Discount</td>
          <td>{{$total_discount}}</td>
         </tr>
         <tr>
          <td>Total</td>
          <td><span id="pos_grand_total" data-total="{{ ($subtotal + $shipping) - $total_discount }}">{{ number_format((float) (($subtotal + $shipping) - $total_discount), 2, '.', '') }}</span></td>
         </tr>
        </tbody>
       </table>
      </div>
      <div>
       <input type="submit" class="btn btn-success" value="Order Submit" />
      </div>
     </form>
    </div>
    <!-- end card-body-->
   </div>
   <!-- end card-->
  </div>
 </div>
</div>
@endsection @section('script')
<script src="{{asset('backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<!-- Plugins js -->
<script src="{{asset('backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
 $(".summernote").summernote({
  placeholder: "Enter Your Text Here",
 });
</script>

<script type="text/javascript">
 $(document).ready(function () {
  $(".select2").select2();
 });
</script>
<script>
 function cart_content(done) {
  return $.ajax({
   type: "GET",
   url: "{{route('admin.order.cart_content')}}",
   dataType: "html",
   success: function (cartinfo) {
    $("#cartTable").html(cartinfo);

    if (typeof done === "function") {
     done();
    }
   },
  });
 }
 function cart_details() {
  return $.ajax({
   type: "GET",
   url: "{{route('admin.order.cart_details')}}",
   dataType: "html",
   success: function (cartinfo) {
    $("#cart_details").html(cartinfo);
   },
  });
 }
 function restoreDiscountFocus(state) {
  if (!state || !state.rowId) {
   return;
  }

  var $input = state.type === 'price' 
    ? $('.product_price[data-id="' + state.rowId + '"]')
    : $('.product_discount[data-id="' + state.rowId + '"]');

 if (!$input.length) {
   return;
  }

  var scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
  $input.val(state.value);

  if (typeof $input[0].focus === "function") {
   try {
    $input[0].focus({ preventScroll: true });
   } catch (e) {
    $input[0].focus();
   }
  }

 if ($input[0].setSelectionRange) {
   var end = String(state.value ?? "").length;
   setTimeout(function () {
    $input[0].setSelectionRange(end, end);
    window.scrollTo(0, scrollTop);
   }, 0);
  } else {
   window.scrollTo(0, scrollTop);
  }
 }
 function parseMoney(value) {
  var number = parseFloat(String(value ?? "").replace(/,/g, ""));
  return Number.isFinite(number) ? number : 0;
 }
 function updatePaymentSummary() {
  var total = 0;
  var $grandTotal = $("#pos_grand_total");

  if ($grandTotal.length) {
   total = parseMoney($grandTotal.data("total"));

   if (!total) {
    total = parseMoney($grandTotal.text());
   }
  }

  var paid = parseMoney($("#paid_amount").val());
  var due = Math.max(0, total - paid);
  var change = Math.max(0, paid - total);

  $("#pos_due_amount").text(due.toFixed(2));
  $("#pos_change_amount").text(change.toFixed(2));
 }
 function refreshCartUI(focusState) {
  return $.when(cart_content(function () {
   restoreDiscountFocus(focusState);
  }), cart_details()).done(function () {
   updatePaymentSummary();
  });
 }
 function search_clear(){
    var keyword = '';
      $.ajax({
          type: "GET",
          data: { keyword: keyword },
          url: "{{route('admin.livesearch')}}",
          success: function (products) {
              if (products) {
                  $(".search_result").html(products);

              } else {
                  $(".search_result").empty();
              }
          },
      });
    $(".search_click").val("");
  }
 function currentCatalogFilters() {
  return {
   keyword: $(".search_click").val() || "",
   mode: $(".product-browser-mode-btn.active").data("browserMode") || "recent",
   category_id: $(".product-browser-category-btn.active").data("categoryId") || "",
   barcode_only: $(".product-browser-mode-btn[data-barcode-only='1']").hasClass("active") ? 1 : 0,
  };
 }
 function updateCatalogCount($scope) {
  var $grid = ($scope && $scope.find) ? $scope.find("#catalogProductsGrid") : $("#catalogProductsGrid");
  if (!$grid.length) {
   return;
  }

  var current = parseInt($grid.data("current"), 10);
  var total = parseInt($grid.data("total"), 10);

  if (Number.isFinite(total)) {
   $("#catalogProductCount").text(Number.isFinite(current) ? (current + " / " + total) : total);
  }
 }
 function refreshCatalogProducts(appendMode) {
  var filters = currentCatalogFilters();
  var page = appendMode ? (parseInt($("#catalogProductsGrid").data("next-page"), 10) || 2) : 1;
  filters.page = page;

  $.ajax({
   type: "GET",
   url: "{{ route('admin.order.catalog_products') }}",
   data: filters,
   dataType: "html",
   success: function (html) {
    var $response = $("<div></div>").html(html);
    var $newGrid = $response.find("#catalogProductsGrid");
    var $newLoadMore = $response.find(".catalog-load-more-wrap");

    if (appendMode) {
     var $currentGrid = $("#catalogProductsGrid");
     if ($currentGrid.length && $newGrid.length) {
      var existingCurrent = parseInt($currentGrid.data("current"), 10) || $currentGrid.children().length;
      var appendedCurrent = parseInt($newGrid.data("current"), 10) || $newGrid.children().length;
      $currentGrid.append($newGrid.children());
      $currentGrid.data("current", existingCurrent + appendedCurrent);
      $currentGrid.data("total", $newGrid.data("total"));
      $currentGrid.data("next-page", $newGrid.data("next-page"));
     }

     var $loadMoreWrap = $(".catalog-load-more-wrap");
     if ($loadMoreWrap.length) {
      if ($newLoadMore.length) {
       $loadMoreWrap.replaceWith($newLoadMore);
      } else {
       $loadMoreWrap.remove();
      }
     } else if ($newLoadMore.length) {
      $(".product-browser-wrap").append($newLoadMore);
     }
    } else {
     $(".product-browser-wrap").html(html);
    }

    updateCatalogCount();
   },
  });
 }

 $(".cart_add").on("click", function (e) {var id = $(this).data('id');
  if (id) {
   $.ajax({
    cache: "false",
    type: "GET",
    data: { id: id },
    url: "{{route('admin.order.cart_add')}}",
    dataType: "json",
    success: function (cartinfo) {
     refreshCartUI();
     search_clear();
    },
   });
  }
 });
 $(document).on("click", ".cart_increment", function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  var qty = parseInt($(this).closest(".quantity").find("input").val(), 10) || 1;
  if (id) {
   $.ajax({
    cache: false,
    data: { id: id, qty: qty },
    type: "GET",
    url: "{{route('admin.order.cart_increment')}}",
    dataType: "json",
    success: function () {
     refreshCartUI();
    },
   });
  }
 });
 $(document).on("click", ".cart_decrement", function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  var qty = parseInt($(this).closest(".quantity").find("input").val(), 10) || 1;
  if (id) {
   $.ajax({
    cache: false,
    type: "GET",
    data: { id: id, qty: qty },
    url: "{{route('admin.order.cart_decrement')}}",
    dataType: "json",
    success: function () {
     refreshCartUI();
    },
   });
  }
 });
 $(document).on("click", ".cart_remove", function (e) {
  e.preventDefault();
  var id = $(this).data("id");
  if (id) {
   $.ajax({
    cache: false,
    type: "GET",
    data: { id: id },
    url: "{{route('admin.order.cart_remove')}}",
    dataType: "json",
    success: function () {
     refreshCartUI();
    },
   });
  }
 });
 const productDiscountTimers = {};
 const syncedProductDiscounts = {};
 const productDiscountDelay = 3000;

 function syncProductDiscount(input, forceImmediate) {
  var $input = $(input);
  var rowId = $input.data("id");
  var discount = $input.val();
  var caret = input.selectionStart;
  var normalizedDiscount = String(discount ?? "");
  var focusState = {
   rowId: rowId,
   value: discount,
   caret: caret,
   type: 'discount'
  };

  clearTimeout(productDiscountTimers[rowId]);

  if (forceImmediate && syncedProductDiscounts[rowId] === normalizedDiscount) {
   return;
  }

  var request = function () {
   $.ajax({
    cache: false,
    type: "GET",
    data: { id: rowId, discount: discount },
    url: "{{route('admin.order.product_discount')}}",
    dataType: "json",
    success: function (response) {
     var nextRowId = response.rowId || rowId;
     delete syncedProductDiscounts[rowId];
     syncedProductDiscounts[nextRowId] = normalizedDiscount;
     focusState.rowId = nextRowId;
     refreshCartUI(focusState);
    },
   });
  };

  if (forceImmediate) {
   request();
   return;
  }

  productDiscountTimers[rowId] = setTimeout(request, productDiscountDelay);
 }

 $(document).on("input", ".product_discount", function () {
  syncProductDiscount(this, false);
 });

 $(document).on("keydown", ".product_discount", function (e) {
  if (e.key === "Enter") {
   e.preventDefault();
   syncProductDiscount(this, true);
  }
 });

 $(document).on("blur", ".product_discount", function () {
  syncProductDiscount(this, true);
 });

 const productPriceTimers = {};
 const syncedProductPrices = {};

 function syncProductPrice(input, forceImmediate) {
  var $input = $(input);
  var rowId = $input.data("id");
  var price = $input.val();
  var caret = input.selectionStart;
  var normalizedPrice = String(price ?? "");
  var focusState = {
   rowId: rowId,
   value: price,
   caret: caret,
   type: 'price'
  };

  clearTimeout(productPriceTimers[rowId]);

  if (forceImmediate && syncedProductPrices[rowId] === normalizedPrice) {
   return;
  }

  var request = function () {
   $.ajax({
    cache: false,
    type: "GET",
    data: { id: rowId, price: price },
    url: "{{route('admin.order.product_price')}}",
    dataType: "json",
    success: function (response) {
     var nextRowId = response.rowId || rowId;
     delete syncedProductPrices[rowId];
     syncedProductPrices[nextRowId] = normalizedPrice;
     focusState.rowId = nextRowId;
     refreshCartUI(focusState);
    },
   });
  };

  if (forceImmediate) {
   request();
   return;
  }

  productPriceTimers[rowId] = setTimeout(request, productDiscountDelay);
 }

 $(document).on("input", ".product_price", function () {
  syncProductPrice(this, false);
 });

 $(document).on("keydown", ".product_price", function (e) {
  if (e.key === "Enter") {
   e.preventDefault();
   syncProductPrice(this, true);
  }
 });

 $(document).on("blur", ".product_price", function () {
  syncProductPrice(this, true);
 });
 $(".cartclear").click(function (e) {
  $.ajax({
   cache: false,
   type: "GET",
   url: "{{route('admin.order.cart_clear')}}",
   dataType: "json",
   success: function (cartinfo) {
    refreshCartUI();
   },
  });
 }); // pshippingfee from total
 $("#area").on("change", function () {
  var id = $(this).val();
  $.ajax({
   type: "GET",
   data: { id: id },
   url: "{{route('admin.order.cart_shipping')}}",
   dataType: "html",
   success: function (cartinfo) {
    refreshCartUI();
   },
  });
 });
 function closeProductPreviewModal() {
  if (window.bootstrap && bootstrap.Modal) {
   bootstrap.Modal.getOrCreateInstance(document.getElementById("productPreviewModal")).hide();
  } else {
   $("#productPreviewModal").hide();
  }
 }
 function addToCart(payload, closePreview) {
  var qty = parseInt($("#productPreviewBody").find(".preview-qty-input").val(), 10) || 1;
  payload.qty = Math.max(1, qty);
  var updateRowId = $("#productPreviewBody").attr("data-update-row-id") || "";
  if (updateRowId) {
      payload.update_row_id = updateRowId;
  }
  $.ajax({
   cache: false,
   type: "GET",
   data: payload,
   url: "{{route('admin.order.cart_add')}}",
   dataType: "json",
   success: function () {
     refreshCartUI();
     search_clear();
     if (closePreview) {
      closeProductPreviewModal();
      $("#productPreviewBody").removeAttr("data-update-row-id");
     }
   },
  });
 }
 $(document).on("click", ".js-cart-add", function (e) {
  e.preventDefault();
  var $button = $(this);
  var payload = {
   id: $button.data("id")
  };

  if ($button.data("size")) {
   payload.size = $button.data("size");
  }
  if ($button.data("color")) {
   payload.color = $button.data("color");
  }
  if ($button.data("model")) {
   payload.model = $button.data("model");
  }
  if ($button.data("weight")) {
   payload.weight = $button.data("weight");
  }
  if ($button.data("variantBarcode")) {
   payload.variant_barcode = $button.data("variantBarcode");
  }

  var isPreviewVariant = $button.closest("#productPreviewBody").length > 0;
  if (isPreviewVariant) {
   $("#productPreviewBody .js-cart-add").removeClass("is-selected");
   $button.addClass("is-selected");
  }

  addToCart(payload, isPreviewVariant);
 });
 $(document).on("click", ".product-browser-mode-btn", function () {
  var $button = $(this);
  if ($button.data("barcodeOnly") === 1) {
   $button.toggleClass("active");
  } else {
   $(".product-browser-mode-btn[data-barcode-only='1']").removeClass("active");
   $(".product-browser-mode-btn[data-browser-mode]").removeClass("active");
   $button.addClass("active");
  }
  refreshCatalogProducts();
 });
 $(document).on("click", ".product-browser-category-btn", function () {
  $(".product-browser-category-btn").removeClass("active");
  $(this).addClass("active");
  refreshCatalogProducts(false);
 });
 let catalogSearchTimer;
 $(document).on("input", ".search_click", function () {
   clearTimeout(catalogSearchTimer);
   catalogSearchTimer = setTimeout(function () {
   refreshCatalogProducts(false);
   }, 250);
  });
 $(document).on("click", ".catalog-load-more-btn", function () {
  refreshCatalogProducts(true);
 });
 $(document).on("click", ".js-product-preview", function (e) {
  e.preventDefault();
  var productId = $(this).data("id");
  var updateRowId = $(this).data("update-row-id") || "";
  if (!productId) {
   return;
  }

  var previewUrl = "{{ route('admin.order.product_preview') }}" + "?id=" + encodeURIComponent(productId);
  $("#productPreviewBody").html('<div class="text-center py-5 text-muted">Loading preview...</div>');

  $.ajax({
   type: "GET",
   url: previewUrl,
   dataType: "html",
   success: function (html) {
    $("#productPreviewBody").html(html);
    if (updateRowId) {
        $("#productPreviewBody").attr("data-update-row-id", updateRowId);
        $("#productPreviewBody .js-preview-close").text("Cancel Update");
    }
    if (window.bootstrap && bootstrap.Modal) {
     bootstrap.Modal.getOrCreateInstance(document.getElementById("productPreviewModal")).show();
    } else {
     $("#productPreviewModal").show();
    }
   },
   error: function () {
    $("#productPreviewBody").html('<div class="text-danger">Preview load failed.</div>');
   }
  });
 });
 $(document).on("click", ".js-preview-gallery-item", function () {
  var image = $(this).data("previewImage");
  if (!image) {
   return;
  }

  var $mainImage = $("#productPreviewBody").find("[data-preview-main-image]").first();
  if ($mainImage.length) {
   $mainImage.attr("src", image);
  }
 });
 $(document).on("click", ".js-preview-scroll-variants", function () {
  var variantBlock = document.querySelector("#productPreviewBody .preview-variant-list");
  if (variantBlock && typeof variantBlock.scrollIntoView === "function") {
   variantBlock.scrollIntoView({ behavior: "smooth", block: "start" });
  }
 });
 $(document).on("input change", ".preview-qty-input", function () {
  var qty = Math.max(1, parseInt($(this).val(), 10) || 1);
  $(this).val(qty);
 });
 $(document).on("click", ".js-preview-close", function () {
  closeProductPreviewModal();
  $("#productPreviewBody").removeAttr("data-update-row-id");
 });
 $(document).on("input change", "#paid_amount", function () {
  updatePaymentSummary();
 });
 $(document).ready(function() {
        $('.search_click').focus();
        updatePaymentSummary();
    });
</script>
<div class="modal fade product-preview-modal" id="productPreviewModal" tabindex="-1" aria-hidden="true">
 <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title">Quick Preview</h5>
    <button type="button" class="btn-close js-preview-close" aria-label="Close"></button>
   </div>
   <div class="modal-body" id="productPreviewBody">
    <div class="text-center py-5 text-muted">Choose a product from the sidebar.</div>
   </div>
  </div>
 </div>
</div>
<script>
    $(document).ready(function() {
        $('#guest_customer').change(function() {
            if ($(this).is(':checked')) {
                $('.new_customer').hide();
            } else {
                $('.new_customer').show();
            }
        });
    });
</script>
@endsection

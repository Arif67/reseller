@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal=str_replace(',','',$subtotal);
    $subtotal=str_replace('.00', '',$subtotal);
    $shipping = Session::get('shipping')?Session::get('shipping'):0;
    $coupon = Session::get('coupon_amount') ? Session::get('coupon_amount') : 0;
    $discount = Session::get('discount')?Session::get('discount'):0;
@endphp
<table class="cart_table table table-bordered table-striped text-center mb-0">
    <thead>
        <tr>
            <th style="width: 20%;"> Delete </th>
            <th style="width: 40%;"> Product </th>
            <th style="width: 20%;"> QTY </th>
            <th style="width: 20%;"> Price </th>
        </tr>
    </thead>

    <tbody>
        @foreach (Cart::instance('shopping')->content() as $value)
            <tr>
                <td>
                    <a class="cart_remove" data-id="{{ $value->rowId }}">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
                <td class="text-left" style="font-size: 14px;">
                    <a href="{{ route('product', $value->options->slug) }}" class="d-flex align-items-center gap-2">
                        <img src="{{ asset($value->options->image) }}" alt="{{ $value->name }}" 
                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;" />
                        <div>
                            {{ Str::limit($value->name, 20) }}
                            <div class="small text-muted">
                                @php
                                    $selectedAttributes = $value->options->selected_attributes ?? [];
                                @endphp
                                @if (!empty($selectedAttributes))
                                    @include('frontEnd.components.common.selected-attributes', ['selectedAttributes' => $selectedAttributes])
                                @else
                                    @if ($value->options->product_size)
                                        <div>Size: {{ $value->options->product_size }}</div>
                                    @endif
                                    @if ($value->options->product_color)
                                        <div>Color: {{ $value->options->product_color }}</div>
                                    @endif
                                    @if ($value->options->product_model)
                                        <div>Model: {{ $value->options->product_model }}</div>
                                    @endif
                                    @if ($value->options->product_weight)
                                        <div>Weight: {{ $value->options->product_weight }}</div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </a>
                </td>
                <td class="cart_qty">
                    <div class="qty-cart vcart-qty">
                        <div class="quantity">
                            <button class="minus cart_decrement" data-id="{{ $value->rowId }}">-</button>
                            <input type="text" value="{{ $value->qty }}" readonly />
                            <button class="plus cart_increment" data-id="{{ $value->rowId }}">+</button>
                        </div>
                    </div>
                </td>
                <td><span class="text-dark">৳ </span><strong>{{ $value->price * $value->qty }}</strong></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end px-4"> Total </th>
            <td class="px-4">
                <span id="net_total"><span class="text-dark">৳</span><strong>{{ $subtotal }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4"> Delivary Charge </th>
            <td class="px-4">
                <span id="cart_shipping_cost"><span class="text-dark">৳</span><strong>{{ $shipping }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4"> Discount </th>
            <td class="px-4">
                <span id="cart_shipping_cost"><span class="text-dark">৳</span><strong>{{ $discount + $coupon }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4">Sub Total</th>
            <td class="px-4">
                <span id="grand_total"><span class="text-dark">৳</span><strong>{{ $subtotal + $shipping - ($discount + $coupon) }}</strong></span>
            </td>
        </tr>
    </tfoot>
</table>

 <form action="@if(Session::get('coupon_used')) {{ route('customer.coupon_remove') }} @else {{ route('customer.coupon') }} @endif" class="checkout-coupon-form"  method="POST">
    @csrf
    <div class="coupon">
        <input  type="text" name="coupon_code" placeholder=" @if(Session::get('coupon_used')) {{Session::get('coupon_used')}} @else Apply Coupon @endif" class="border-0 shadow-none form-control"  />
        <input type="submit" value="@if(Session::get('coupon_used')) remove @else apply  @endif "   class="border-0 shadow-none btn btn-theme" />
    </div>
</form>

<script src="{{asset('public/frontEnd/js/jquery-3.6.3.min.js')}}"></script>
<!-- cart js start -->
<script>
    $('.cart_store').on('click',function(){
    var id = $(this).data('id'); 
    var qty = $(this).parent().find('input').val();
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id,'qty':qty?qty:1},
           url:"{{route('cart.store')}}",
           success:function(data){               
            if(data){
                return cart_count();
            }
           }
        });
     }  
   });

    $('.cart_remove').on('click',function(){
    var id = $(this).data('id');   
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.remove')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                return cart_count();
            }
           }
        });
     }  
   });

    $('.cart_increment').on('click',function(){
    var id = $(this).data('id');  
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.increment')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                return cart_count();
            }
           }
        });
     }  
   });

    $('.cart_decrement').on('click',function(){
    var id = $(this).data('id');  
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.decrement')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                return cart_count();
            }
           }
        });
     }  
   });

    function cart_count(){
        $.ajax({
           type:"GET",
           url:"{{route('cart.count')}}",
           success:function(data){               
            if(data){
                $("#cart-qty").html(data);
            }else{
               $("#cart-qty").empty();
            }
           }
        }); 
   };
</script>
<!-- cart js end -->
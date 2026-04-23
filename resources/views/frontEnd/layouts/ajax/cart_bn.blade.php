<style> 
.cartlist img {
    width: 50px;
    height:50px;
}
.cart_name {
    max-width: 185px;
}
</style>
@php
    $subtotal = Cart::instance('shopping')->subtotal();
    $subtotal=str_replace(',','',$subtotal);
    $subtotal=str_replace('.00', '',$subtotal);
    $shipping = Session::get('shipping')?Session::get('shipping'):0;
    $discount = Session::get('discount')?Session::get('discount'):0;
@endphp
<table class="cart_table table table-bordered table-striped text-center mb-0">
    <thead>
       <tr>
          <th style="width: 20%;">Remove</th>
          <th style="width: 40%;">Product</th>
          <th style="width: 20%;">Quantity</th>
          <th style="width: 20%;">Price</th>
       </tr>
    </thead>

    <tbody>
        @foreach(Cart::instance('shopping')->content() as $value)
        <tr>
            <td>
                <a href="{{ route('product', $value->options->slug) }}" class="text-danger">
                    <i class="fas fa-trash"></i>
                </a>
            </td>

            <td class="text-left" style="font-size: 14px;">
                <a href="{{ route('product', $value->options->slug) }}" class="text-decoration-none d-flex align-items-center gap-2">
                    <img src="{{ asset($value->options->image) }}" height="30" width="30" style="object-fit: cover; border-radius: 4px;" alt="{{ $value->name }}">
                    <div>
                        {{ Str::limit($value->name, 20) }}
                        <div class="small text-muted">
                            @php
                                $selectedAttributes = $value->options->selected_attributes ?? [];
                            @endphp
                            @if (!empty($selectedAttributes))
                                @include('frontEnd.components.common.selected-attributes', ['selectedAttributes' => $selectedAttributes])
                            @else
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

            <td width="15%" class="cart_qty">
                <div class="qty-cart vcart-qty">
                    <div class="quantity">
                        <button class="minus cart_decrement" data-id="{{ $value->rowId }}">-</button>
                        <input type="text" value="{{ $value->qty }}" readonly />
                        <button class="plus cart_increment" data-id="{{ $value->rowId }}">+</button>
                    </div>
                </div>
            </td>

            <td>৳{{ $value->price * $value->qty }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th colspan="3" class="text-end px-4">Subtotal</th>
            <td>
                <span id="net_total"><span class="alinur">৳ </span><strong>{{ $subtotal }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4">Delivery Charge</th>
            <td>
                <span id="cart_shipping_cost"><span class="alinur">৳ </span><strong>{{ $shipping }}</strong></span>
            </td>
        </tr>
        <tr>
            <th colspan="3" class="text-end px-4">Grand Total</th>
            <td>
                <span id="grand_total"><span class="alinur">৳ </span><strong>{{ $subtotal + $shipping }}</strong></span>
            </td>
        </tr>
    </tfoot>
</table>


<script src="{{asset('public/frontEnd/js/jquery-3.6.3.min.js')}}"></script>
<!-- cart js start -->
<script>

    $('.cart_remove').on('click',function(){
    var id = $(this).data('id');   
    $("#loading").show();
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.remove_bn')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                $("#loading").hide();
            }
           }
        });
     }  
   });

    $('.cart_increment').on('click',function(){
    var id = $(this).data('id');  
    $("#loading").show();
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.increment_bn')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                $("#loading").hide();
            }
           }
        });
     }  
   });

    $('.cart_decrement').on('click',function(){
    var id = $(this).data('id');  
    $("#loading").show();
    if(id){
        $.ajax({
           type:"GET",
           data:{'id':id},
           url:"{{route('cart.decrement_bn')}}",
           success:function(data){               
            if(data){
                $(".cartlist").html(data);
                $("#loading").hide();
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
                $(".cart_header").html(data);
            }else{
               $(".cart_header").empty();
            }
           }
        }); 
   };
</script>
<!-- cart js end -->

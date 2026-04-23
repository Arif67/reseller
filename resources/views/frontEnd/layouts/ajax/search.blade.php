@if($products)
@php
    $fallbackImage = 'uploads/logo.png';
@endphp
<div class="search_product">
		<ul>

		@foreach($products as $value)
		<a href="{{route('product',$value->slug)}}">
			<li>
					<div class="search_img">
						<img src="{{asset($value->primary_media_image ?? optional($value->image)->image ?? $fallbackImage)}}" alt="{{$value->name}}">
					</div>
					<div class="search_content">
						<p class="name">{{$value->name}}</p>   
						 @if($value->variable_count > 0 && $value->type == 0)

						 <p class="price">
						 	 @if($value->display_old_price)
                             <del>৳ {{ $value->display_old_price }}</del>
                             @endif
                          	  ৳ {{ $value->display_new_price }} </p>  
                         @else          
						<p  class="price">৳{{$value->new_price}} @if($value->old_price)<del>৳{{$value->old_price}}</del>@endif</p>
						@endif
					</div>
			</li>
		</a>
		@endforeach
	</ul>
</div>
@endif

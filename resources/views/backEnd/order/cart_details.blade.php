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

You have {{ $full ? 'paid' : 'paid an additional' }} {{ p($total, false) }} for the following item:

{{ $item_name }}

@if ($has_delivery)
You will be contacted again when the item has been dispatched.
@elseif (!$has_address)
You will be contacted again when the seller provides a collection address.
@elseif (!$is_paid)
The collection address will be provided when full payment is received.
@else
You can collect the item from the following address:

{{ seller_name }}
{{ seller_street1 }}
{{ seller_street2 }}
{{ seller_city }}
{{ seller_county }}
{{ seller_postcode }}
{{ seller_country }}
@endif

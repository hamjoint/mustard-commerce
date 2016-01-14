You have received {{ $full ? 'the full' : 'an additional' }} payment of {{ p($total, false) }} for the following item:

{{ $item_name }}

@if ($has_delivery)
Please dispatch the item to the following address as soon as possible:

{{ $buyer_name }}
{{ $buyer_street1 }}
{{ $buyer_street2 }}
{{ $buyer_city }}
{{ $buyer_county }}
{{ $buyer_postcode }}
{{ $buyer_country }}

After dispatch, please sign in to mark the item as dispatched and notify the buyer:

{{ url('inventory/sold') }}
@elseif (!$has_address)
Please provide the buyer with precise details for collection:

{{ url('purchase/collection-address/' . $purchase_id) }}
@endif

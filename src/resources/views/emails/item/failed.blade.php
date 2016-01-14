Your payment of {{ p($total, false) }} for the following item has failed:

{{ $item_name }}

Please re-attempt payment:

{{ url('pay/' . $payment_id) }}

@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    {{ $purchase->item->name }}
@stop

@section('content')
    <div class="purchase-details">
        <div class="row">
            <div class="medium-12 columns">
                <h1>Purchase details</h1>
            </div>
        </div>
        <div class="row">
            <div class="medium-6 columns">
                <table class="table expand">
                    <thead>
                        <tr>
                            <th colspan="2">Transaction</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Item</th>
                            <td>{{ $purchase->item->name }}</td>
                        </tr>
                        <tr>
                            <th>Buyer</th>
                            <td>{!! u($purchase->buyer) !!}</td>
                        </tr>
                        <tr>
                            <th>Seller</th>
                            <td>{!! u($purchase->item->seller) !!}</td>
                        </tr>
                        <tr>
                            <th>Unit total</th>
                            <td>{{ p($purchase->unitPrice) }}</td>
                        </tr>
                        <tr>
                            <th>Quantity</th>
                            <td>{{ $purchase->quantity ?: 1 }}</td>
                        </tr>
                        <tr>
                            <th>Delivery total</th>
                            <td>{{ $purchase->deliveryOption ? p($purchase->deliveryOption->price) : p(0) }}</td>
                        </tr>
                        <tr>
                            <th>Grand total</th>
                            <td>{{ p($purchase->grandTotal) ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Hamjoint commission</th>
                            <td>{{ p($purchase->grandTotal - $purchase->netTotal) }} ({{$purchase->item->commission * 100 }}%)</td>
                        </tr>
                        <tr>
                            <th>Net total</th>
                            <td>{{ p($purchase->netTotal) }}</td>
                        </tr>
                        <tr>
                            <th>Paid</th>
                            @if ($purchase->isPaid())
                            <td><i class="fa fa-check success"></i> ({{ dt($purchase->paid) }})</td>
                            @else
                            <td><i class="fa fa-times alert"></i></td>
                            <td>{{ link_to('/pay/' . $purchase->purchaseId, 'Pay now', ['class' => "button tiny success radius"]) }}</td>
                            @endif
                        </tr>
                        <tr>
                            <th>Dispatched</th>
                            @if ($purchase->isDispatched())
                            <td><i class="fa fa-check success"></i> ({{ dt($purchase->dispatched) }})</td>
                            @elseif (!$purchase->hasDelivery())
                            <td>N/A</td>
                            @else
                            <td><i class="fa fa-times alert"></i></td>
                            @endif
                        </tr>
                        <tr>
                            @if ($purchase->hasDelivery())
                            <th>Delivery address</th>
                            @else
                            <th>Collection address</th>
                            @endif
                            @if (!$purchase->hasAddress())
                            <td>Awaiting collection address from seller</td>
                            @else
                            <td>
                                <ul class="vcard">
                                    <li class="fn">{{ $purchase->name }}</li>
                                    <li class="street-address">{{ $purchase->street1 }}</li>
                                    <li class="street-address">{{ $purchase->street2 }}</li>
                                    <li class="locality">{{ $purchase->city }}</li>
                                    <li class="state">{{ $purchase->county }}</li>
                                    <li class="zip">{{ $purchase->postcode }}</li>
                                    <li class="country">{{ $purchase->country }}</li>
                                </ul>
                            </td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="medium-6 columns">
                <table class="table expand">
                    <thead>
                        <tr>
                            <th colspan="2">Listing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Type</th>
                            <td>{{ $purchase->item->humanType }}</td>
                        </tr>
                        <tr>
                            <th>Duration</th>
                            <td>{{ $purchase->item->humanPeriod }}</td>
                        </tr>
                        <tr>
                            <th>Start date</th>
                            <td>{{ dt($purchase->item->startDate) }}</td>
                        </tr>
                        <tr>
                            <th>End date</th>
                            <td>{{ dt($purchase->item->endDate) }}</td>
                        </tr>
                        <tr>
                            <th>Total bids</th>
                            <td>{{ $purchase->item->auction ? $purchase->item->bids()->count() : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Start price</th>
                            <td>{{ p($purchase->item->startPrice) }}</td>
                        </tr>
                        <tr>
                            <th>Reserve price</th>
                            <td>{{ $purchase->item->hasReserve() ? p($purchase->item->reservePrice) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fixed price</th>
                            <td>{{ $purchase->item->hasFixed() ? p($purchase->item->fixedPrice) : '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

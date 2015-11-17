@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Sold - Inventory
@stop

@section('content')
<div class="row">
    <div class="medium-3 large-2 columns">
        @include('mustard::inventory.nav')
    </div>
    <div class="medium-9 large-10 columns">
        @include('tablelegs::filter')
        @if (!$table->isEmpty())
            <table class="expand">
                @include('tablelegs::header')
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->itemId }}</td>
                            <td><a href="{{ $item->url }}">{{ $item->name }}</a></td>
                            @if ($item->purchases)
                                <td>@include('mustard::user.link', ['user' => $item->purchases->buyer])</td>
                                <td style="white-space:nowrap;">
                                    <strong>Unit price:</strong> {{ mustard_price($item->purchases->unitPrice) }}<br />
                                    <strong>Quantity:</strong> {{ mustard_number($item->purchases->quantity) }}<br />
                                    @if (!$item->purchases->deliveryOption)
                                        <strong>Delivery:</strong> Collection<br />
                                    @else
                                        <strong>Delivery:</strong> {{ mustard_price($item->purchases->deliveryOption->price ?: 'Free') }}<br />
                                    @endif
                                    <strong>Total:</strong> {{ mustard_price($item->purchases->grandTotal) }}<br />
                                    <strong>Received:</strong> {{ mustard_price($item->purchases->received) }}
                                </td>
                                <td>{{ mustard_date($item->purchases->created) }}</td>
                            @else
                                <td>@include('mustard::user.link', ['user' => $item->bids->bidder])</td>
                                <td style="white-space:nowrap;">
                                    <strong>Winning bid:</strong> {{ mustard_price($item->biddingPrice) }}<br />
                                    <strong>Maximum bid:</strong> {{ mustard_price($item->bids->amount) }}<br />
                                    <strong>Paid:</strong> {{ mustard_price(0) }}
                                </td>
                                <td>{{ mustard_date($item->endDate) }}</td>
                            @endif
                            <td>
                                <button href="#" data-dropdown="item-{{ $item->purchases->purchaseId }}-{{ $item->itemId }}-options" aria-controls="item-{{ $item->purchases->purchaseId }}-{{ $item->itemId }}-options" aria-expanded="false" class="button tiny radius dropdown"><i class="fa fa-cog"></i></button>
                                <ul id="item-{{ $item->purchases->purchaseId }}-{{ $item->itemId }}-options" data-dropdown-content class="f-dropdown" aria-hidden="true" tabindex="-1">
                                    @if ($item->purchase && !$item->purchase->isPaid())
                                    <li><a href="/purchase/payment-received/{{ $item->purchases->purchaseId }}">Mark as payment received</a></li>
                                    @endif
                                    @if ($item->purchases->purchaseId && $item->purchases->hasDelivery() && !$item->purchases->isDispatched())
                                    <li><a href="/purchase/dispatched/{{ $item->purchases->purchaseId }}">Mark as dispatched</a></li>
                                    @endif
                                    @if ($item->purchaseId && !$item->purchases->hasDelivery() && !$item->purchases->hasAddress())
                                    <li><a href="/purchase/collection-address/{{ $item->purchases->purchaseId }}">Add collection address</a></li>
                                    @endif
                                    <li><a href="/item/relist/{{ $item->itemId }}">{{ $item->auction ? 'Relist one like this' : 'Relist' }}</a></li>
                                    @if (!$item->purchases->isPaid())
                                    <li><a href="/item/report/{{ $item->purchases->purchaseId }}">Report unpaid item</a></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No items found. <a href="/sell">Sell one now</a>.</p>
        @endif
        <div class="row">
            <div class="medium-12 columns pagination-centered">
                {!! $table->paginator() !!}
            </div>
        </div>
    </div>
</div>
@stop

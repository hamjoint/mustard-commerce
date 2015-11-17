@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Bought - Inventory
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
                            <td>
                                <a href="{{ $item->url }}">{{ $item->name }}</a>
                                @if (!$item->purchases || !$item->purchases->isPaid())
                                    <span class="label alert radius">Unpaid</span>
                                @endif
                            </td>
                            <td>@include('mustard::user.link', ['user' => $item->seller])</td>
                            @if ($item->purchases)
                                <td style="white-space:nowrap;">
                                    <strong>Unit price:</strong> {{ mustard_price($item->purchases->unitPrice) }}<br />
                                    <strong>Quantity:</strong> {{ mustard_number($item->purchases->quantity) }}<br />
                                    @if (!$item->purchases->deliveryOption)
                                        <strong>Delivery:</strong> Collection<br />
                                    @else
                                        <strong>Delivery:</strong> {{ mustard_price($item->purchases->deliveryOption->price ?: 'Free') }}<br />
                                    @endif
                                    <strong>Total:</strong> {{ mustard_price($item->purchases->grandTotal) }}<br />
                                    <strong>Paid:</strong> {{ mustard_price($item->purchases->received) }}
                                </td>
                                <td>{{ mustard_date($item->purchases->created) }}</td>
                            @else
                                <td style="white-space:nowrap;">
                                    <strong>Winning bid:</strong> {{ mustard_price($item->biddingPrice) }}<br />
                                    <strong>Maximum bid:</strong> {{ mustard_price($item->bids->amount) }}<br />
                                    <strong>Paid:</strong> {{ mustard_price(0) }}
                                </td>
                                <td>{{ mustard_date($item->endDate) }}</td>
                            @endif
                            <td>
                                <button href="#" data-dropdown="item-{{ $item->purchases->purchaseId }}-{{ $item->itemId }}-options" aria-controls="item-{{ $item->purchases->purchaseId }}-{{ $item->itemId }}-options" aria-expanded="false" class="button tiny radius dropdown"><i class="fa fa-cog"></i> Options</button>
                                <ul id="item-{{ $item->purchases->purchaseId }}-{{ $item->itemId }}-options" data-dropdown-content class="f-dropdown" aria-hidden="true" tabindex="-1">
                                    @if (!$item->purchases)
                                        <li><a href="/checkout/{{ $item->itemId }}">Pay</a></li>
                                    @elseif (!$item->purchases->isPaid())
                                        <li><a href="/pay/{{ $item->purchases->purchaseId }}">Pay</a></li>
                                    @else
                                        <li><a href="/purchase/details/{{ $item->purchases->purchaseId }}">View full details</a></li>
                                    @endif
                                        <li><a href="/item/report/{{ $item->purchases->purchaseId }}">Report undelivered item</a></li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No items found. <a href="/buy">Find something to bid on</a>.</p>
        @endif
        <div class="row">
            <div class="medium-12 columns text-center">
                {{ $table->paginator() }}
            </div>
        </div>
    </div>
</div>
@stop

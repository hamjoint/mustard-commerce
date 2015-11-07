@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Mark as dispatched: {{ $purchase->item->name }}
@stop

@section('content')
    <div class="purchase-dispatched">
        <div class="row">
            <div class="medium-12 columns">
                <h1>Mark as dispatched: {{ $purchase->item->name }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="medium-6 medium-offset-3 columns">
                <form method="post" action="/purchase/dispatched" data-abide="true">
                    <input type="hidden" name="purchase_id" value="{{ $purchase->purchaseId }}" />
                    <fieldset>
                        <legend>Delivery details</legend>
                        <div class="row">
                            <div class="large-12 columns">
                                <div class="alert-box radius warning">Providing a tracking number will help us to verify delivery in the event of a sent item not arriving. Without a tracking number, the buyer will be refunded and you must claim the appropriate compensation from your delivery company.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Delivery service
                                    <input type="text" value="{{ $purchase->deliveryOption->name }}" disabled />
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Tracking number
                                    <input type="text" name="tracking_number" placeholder="" />
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="large-12 columns">
                            <button class="button success expand radius"><i class="fa fa-check"></i> Mark as dispatched</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="medium-12 columns">
                <a href="/inventory/sold"><i class="fa fa-arrow-circle-left"></i> Return to inventory</a>
            </div>
        </div>
    </div>
@stop

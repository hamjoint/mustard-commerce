@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Checkout: {{ $item->name }}
@stop

@section('content')
    <div class="purchase-checkout">
        <form method="post" action="/purchase/checkout" data-abide="true">
            <input type="hidden" name="item_id" value="{{ $item->itemId }}" />
            <div class="row">
                <div class="medium-12 columns">
                    <h1>Checkout: {{ $item->name }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="medium-6 columns">
                    <ul class="pricing-table">
                        <li class="price">{{ p($item_total) }}</li>
                    </ul>
                </div>
                <div class="medium-6 columns">
                    <p>Sold by: {{ u($item->seller) }}</p>
                </div>
            </div>
            @if ($item->hasQuantity())
            <div class="row">
                <div class="medium-6 columns">
                    <label>Choose a quantity
                        <input type="number" name="quantity" value="1" max="{{ $item->quantity }}" min="1" required />
                    </label>
                    <small class="error">Please choose a quantity.</small>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="medium-6 columns">
                    <label>Choose a delivery option
                        <select name="delivery_option" required>
                            <option></option>
                            @foreach ($item->deliveryOptions as $delivery_option)
                            <option value="{{ $delivery_option->itemDeliveryOptionId }}" data-price="{{ $delivery_option->price }}">{{ p($delivery_option->price) }}: {{ $delivery_option->name }} ({{ $delivery_option->humanArrivalTime }})</option>
                            @endforeach
                            @if ($item->isCollectable())
                            <option value="collection" data-price="0.00">Free: Collection from {{ $item->collectionLocation }}</option>
                            @endif
                        </select>
                    </label>
                    <small class="error">Please choose a delivery option.</small>
                </div>
                <div class="postal-address">
                    <div class="medium-6 columns">
                        <label>Where should the item be delivered?
                            <select name="postal_address">
                                @foreach (Auth::user()->postalAddresses as $pa)
                                <option value="{{ $pa->postalAddressId }}">{{ implode(', ', $pa->allParts()) }}</option>
                                @endforeach
                                <option value="add">Add a postal address</option>
                            </select>
                        </label>
                        <small class="error">Please choose a postal address.</small>
                    </div>
                </div>
            </div>
            <div class="add-postal-address">
                <div class="row">
                    <div class="medium-12 columns">
                        <label>Individual or company name
                            <input type="text" name="name" placeholder="eg. Marty McFly" required />
                        </label>
                        <small class="error">Please enter an individual's name or that of a company.</small>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-6 columns">
                        <label>Street number &amp; name, or building name
                            <input type="text" name="street1" placeholder="eg. 1 Chapel Hill" required />
                        </label>
                        <small class="error">Please enter a street number &amp; name, or building name.</small>
                    </div>
                    <div class="medium-6 columns">
                        <label>Second line (optional)
                            <input type="text" name="street2" placeholder="eg. Heswall" />
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-4 columns">
                        <label>City
                            <input type="text" name="city" placeholder="eg. Bournemouth" required />
                        </label>
                        <small class="error">Please enter a city.</small>
                    </div>
                    <div class="medium-4 columns">
                        <label>County, State or Province
                            <input type="text" name="county" placeholder="eg. Dorset" required />
                        </label>
                        <small class="error">Please enter a county, state or province.</small>
                    </div>
                    <div class="medium-4 columns">
                        <label>Postcode
                            <input type="text" name="postcode" placeholder="eg. BH1 1AA" required />
                        </label>
                        <small class="error">Please enter a postcode.</small>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-12 columns">
                        <label>Country
                            <select name="country" required>
                                <option></option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->countryId }}" {{ $country->iso3166Alpha2 == 'GB' ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-12 columns">
                        <input type="checkbox" name="remember" value="1" id="label-remember" checked /><label for="label-remember">Remember this address</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="medium-6 medium-offset-3 columns">
                    <button type="submit" class="button expand radius" data-item-price="{{ $item_total }}">Pay</button>
                </div>
            </div>
        </form>
    </div>
@stop

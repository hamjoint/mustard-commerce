@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Collection address: {{ $purchase->item->name }}
@stop

@section('content')
    <div class="purchase-collection-address">
        <form method="post" action="/purchase/collection-address" data-abide="true">
            {!! csrf_field() !!}
            <input type="hidden" name="purchase_id" value="{{ $purchase->purchaseId }}" />
            <div class="row">
                <div class="medium-12 columns">
                    <h1>Collection address: {{ $purchase->item->name }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="postal-address">
                    <div class="medium-6 columns">
                        <label>Where should the item be collected from?
                            <select name="postal_address">
                                @foreach (\Auth::user()->postalAddresses as $pa)
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
                                @foreach ($countries as $country)
                                    <option value="{{ $country->countryId }}" {{ $country->iso3166Alpha2 == 'GB' ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="medium-12 columns">
                        <label>
                            <input type="checkbox" name="remember" value="1" id="label-remember" checked /><label for="label-remember">Remember this address</label>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="medium-6 medium-offset-3 columns">
                    <button type="submit" class="button expand radius">Send</button>
                </div>
            </div>
        </form>
    </div>
@stop

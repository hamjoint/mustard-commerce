@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Postal addresses - Account settings
@stop

@section('content')
<div class="row">
    <div class="medium-3 large-2 columns">
        @include('mustard::account.nav')
    </div>
    <div class="medium-9 large-10 columns">
        <form method="post" action="/account/delete-postal-address">
            <fieldset>
                <div class="row">
                    <div class="medium-12 columns">
                        <p>Any postal addresses configured here can be selected at checkout as your delivery address.</p>
                    </div>
                </div>
                @if (Auth::user()->postalAddresses->count())
                <div class="row">
                    <div class="medium-12 columns end">
                        @foreach (Auth::user()->postalAddresses as $pa)
                        <ul class="vcard">
                            <li class="fn">{{ $pa->name }}</li>
                            <li class="street-address">{{ $pa->street1 }}</li>
                            <li class="street-address">{{ $pa->street2 }}</li>
                            <li class="locality">{{ $pa->city }}</li>
                            <li class="state">{{ $pa->county }}</li>
                            <li class="zip">{{ $pa->postcode }}</li>
                            <li class="country">{{ $pa->country }}</li>
                            <button class="button alert expand radius tiny" name="postal_address_id" value="{{ $pa->postalAddressId }}"><i class="fa fa-minus"></i> Delete</button>
                        </ul>
                        @endforeach
                    </div>
                </div>
                @endif
            </fieldset>
        </form>
        <form method="post" action="/account/postal-address" data-abide="true">
            <fieldset>
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
                    <div class="medium-8 columns">
                        <label>Country
                            <select name="country" required>
                                {{--@foreach (Country::orderBy('name')->get() as $country)
                                <option value="{{ $country->countryId }}" {{ $country->iso3166Alpha2 == 'GB' ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach--}}
                            </select>
                        </label>
                    </div>
                    <div class="medium-4 columns">
                        <label>&nbsp;
                            <button class="button expand radius"><i class="fa fa-plus"></i> Add</button>
                        </label>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@stop

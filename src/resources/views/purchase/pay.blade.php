@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Pay for {{ $purchase->item->name }}
@stop

@section('content')
    <div class="row">
        <div class="medium-12 columns">
            <h1>Pay for {{ $purchase->item->name }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="medium-6 medium-offset-3 columns">
            <p class="text-center">Please wait while the card processor loads...</p>
            <p class="text-center">
                <i class="fa fa-cog fa-spin"></i>
            </p>
            <p class="text-center">You can refresh the page if nothing loads (it should only take a few seconds)</p>
        </div>
    </div>
@stop

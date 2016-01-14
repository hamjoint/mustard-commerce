<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Hamjoint\Mustard\Commerce\Http\Controllers;

use Auth;
use Hamjoint\Mustard\Commerce\PostalAddress;
use Hamjoint\Mustard\Commerce\Purchase;
use Hamjoint\Mustard\DeliveryOption;
use Hamjoint\Mustard\Http\Controllers\Controller;
use Hamjoint\Mustard\Item;
use Illuminate\Http\Request;
use Log;
use Tjbp\Countries\Iso3166;

class PurchaseController extends Controller
{
    /**
     * Return the purchase details view.
     *
     * @return \Illuminate\View\View
     */
    public function getDetails($purchaseId) {
        $purchase = Purchase::findOrFail($purchaseId);

        if (!in_array(Auth::user()->userId, [
            $purchase->item->seller->userId,
            $purchase->buyer->userId
        ])) {
            return redirect('/inventory')->withErrors([
                "You are not the buyer or seller of this item."
            ]);
        }

        return view('mustard::purchase.details', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Return the item checkout view.
     *
     * @return \Illuminate\View\View
     */
    public function getCheckout($itemId)
    {
        $item = Item::findOrFail($itemId);

        if ($item->seller->userId == Auth::user()->userId) {
            return redirect($item->url)->withErrors([
                'You cannot purchase your own items.'
            ]);
        }

        if ($item->auction && !$item->isActive() && $item->purchases->count()) {
            return redirect('/pay/' . $item->purchases->first()->purchaseId);
        }

        // Check for unpaid item
        if ($unpaid = $item->purchases()->where('user_id', Auth::user()->userId)->where('paid', 0)->first()) {
            return redirect('/pay/' . $unpaid->purchaseId);
        }

        if (!$item->isActive() && !$item->auction) {
            return redirect($item->url)->withErrors([
                'This item has ended.'
            ]);
        }

        if ($item->isActive() && !$item->hasFixed()) {
            return redirect($item->url)->withErrors([
                'This auction has no fixed price, so cannot be bought outright.'
            ]);
        }

        if (!$item->isActive() && $item->auction && !$item->winningBid) {
            return redirect($item->url)->withStatus(
                'Please wait while this auction is processed.'
            );
        }

        return view('mustard::purchase.checkout', [
            'countries' => Iso3166::all(),
            'item' => $item,
            'item_total' => ($item->auction && !$item->isActive())
                ? $item->biddingPrice
                : $item->fixedPrice,
        ]);
    }

    /**
     * Return the purchase pay view.
     *
     * @return \Illuminate\View\View
     */
    public function getPay($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);

        if ($purchase->buyer->userId != Auth::user()->userId) {
            return redirect('/inventory/bought')->withErrors([
                "You are not the buyer of this item."
            ]);
        }

        if ($purchase->isPaid()) {
            return redirect('/inventory/bought')->withStatus(
                'You have already successfully paid for this item.'
            );
        }

        return view('mustard::purchase.pay', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Return the purchase dispatched view.
     *
     * @return \Illuminate\View\View
     */
    public function getDispatched($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);

        if ($purchase->item->seller->userId != Auth::user()->userId) {
            return redirect('/inventory/sold')->withErrors([
                "You are not seller of this item."
            ]);
        }

        if ($purchase->isDispatched()) {
            return redirect('/inventory/sold')->withStatus(
                'You have already marked this item as dispatched.'
            );
        }

        if (!$purchase->deliveryOption) {
            return redirect()->back()->withErrors([
                "This item is due to be collected."
            ]);
        }

        return view('mustard::purchase.dispatched', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Return the purchase collection address view.
     *
     * @return \Illuminate\View\View
     */
    public function getCollectionAddress($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);

        if ($purchase->item->seller->userId != Auth::user()->userId) {
            return redirect('/inventory/sold')->withErrors([
                "You are not the seller of this item."
            ]);
        }

        if ($purchase->deliveryOption) {
            return redirect('/inventory/sold')->withErrors([
                "This item is not due to be collected."
            ]);
        }

        if ($purchase->hasAddress()) {
            return redirect('/inventory/sold')->withErrors([
                "You have already provided a collection address for this item."
            ]);
        }

        return view('mustard::purchase.collection-address', [
            'countries' => Iso3166::all(),
            'purchase' => $purchase,
        ]);
    }

    /**
     * Checkout an item.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCheckout(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));

        if ($item->seller->userId == Auth::user()->userId) {
            return redirect($item->url)->withStatus('You cannot purchase your own items.');
        }

        // Check for unpaid item
        if ($unpaid = $item->purchases()->where('user_id', Auth::user()->userId)->where('paid', 0)->first()) {
            return redirect('/pay/' . $unpaid->purchaseId);
        }

        if (!$item->auction && $item->isEnded()) {
            return redirect()->back()->withErrors([
                'You can no longer can no longer continue with this purchase because the item has ended.'
            ]);
        }

        if (!$item->auction && $request->input('quantity') > $item->quantity) {
            return redirect()->back()->withErrors([
                'You cannot purchase more than the available stock.'
            ]);
        }

        if ($item->auction && !$item->isEnded()) {
            return redirect()->back()->withErrors([
                'You must win an auction before you can pay for it.'
            ]);
        }

        if ($item->auction && !$item->winningBid) {
            return redirect()->back()->withErrors([
                'Please wait while this auction is processed.'
            ]);
        }

        if ($item->auction && $item->winningBid->bidder != Auth::user()) {
            return redirect()->back()->withErrors([
                "You can only pay for an auction you've won."
            ]);
        }

        if ($item->auction && $item->isEnded() && $item->purchases->count()) {
            return redirect('/pay/' . $item->purchases->first()->purchaseId);
        }

        $validator = \Validator::make(
            \Input::all(),
            [
                'delivery_option' => 'required',
                'quantity' => 'integer',
                'postal_address' => 'required_if:delivery_option,collection',
                'name' => 'required_if:postal_address,add',
                'street1' => 'required_if:postal_address,add',
                'city' => 'required_if:postal_address,add',
                'county' => 'required_if:postal_address,add',
                'postcode' => 'required_if:postal_address,add',
                'country' => 'required_if:postal_address,add'
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $purchase = new Purchase;

        $purchase->created = time();

        $purchase->item()->associate($item);
        $purchase->buyer()->associate(Auth::user());

        if ($request->input('delivery_option') != 'collection') {
            if (!$delivery_option = DeliveryOption::find($request->input('delivery_option'))) {
                return redirect()->back()->withErrors(['delivery_option' => 'Please select a valid delivery option.']);
            }

            $purchase->deliveryOption()->associate($delivery_option);

            if ($request->input('postal_address') == 'add') {
                $postal_address = new PostalAddress;

                $postal_address->name = $request->input('name');
                $postal_address->street1 = $request->input('street1');
                $postal_address->street2 = $request->input('street2');
                $postal_address->city = $request->input('city');
                $postal_address->county = $request->input('county');
                $postal_address->country = $request->input('country');
                $postal_address->postcode = $request->input('postcode');

                $postal_address->user()->associate(Auth::user());

                if (!Iso3166::exists($postal_address->country)) {
                    return redirect()->back()->withErrors([
                        'country' => 'Please select a valid country.'
                    ]);
                }

                if ($request->input('remember')) {
                    $postal_address->save();
                }
            } elseif (!$postal_address = PostalAddress::find($request->input('postal_address'))) {
                return redirect()->back()->withErrors(['postal_address' => 'Please select a valid postal address.']);
            } elseif ($postal_address->user != Auth::user()) {
                return redirect()->back()->withErrors(['postal_address' => 'You can only select your own postal addresses.']);
            }

            $purchase->useAddress($postal_address);
        }

        if ($item->auction && !$item->isActive()) {
            $purchase->unitPrice = $purchase->total = $item->biddingPrice;

            $purchase->quantity = 1;
        } else {
            $purchase->unitPrice = $item->fixedPrice;

            $purchase->quantity = $request->input('quantity') ?: 1;

            $purchase->total = round($purchase->unitPrice * $purchase->quantity, 2);

            $item->quantity -= $purchase->quantity;

            $item->save();
        }

        $purchase->save();

        return redirect("/pay/{$purchase->purchaseId}");
    }

    /**
     * Mark a purchase dispatched.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDispatched(Request $request)
    {
        $purchase = Purchase::findOrFail($request->input('purchase_id'));

        if ($purchase->item->seller->userId != Auth::user()->userId) {
            return redirect('/inventory/sold')->withErrors(["You are not seller of this item."]);
        }

        if ($purchase->isDispatched()) {
            return redirect('/inventory/sold')->withErrors(["This item has already been marked as dispatched."]);
        }

        if (!$purchase->deliveryOption) {
            return redirect('/inventory/sold')->withErrors(["This item is due to be collected."]);
        }

        $purchase->dispatched = time();
        $purchase->trackingNumber = $request->input('tracking_number');

        $purchase->save();

        $purchase->buyer->sendEmail(
            'Your item has been dispatched',
            'emails.item.dispatched',
            [
                'item_name' => $purchase->item->name,
                'delivery_service' => $purchase->deliveryOption->name,
                'tracking_number' => $purchase->trackingNumber,
                'arrival_time' => $purchase->deliveryOption->humanArrivalTime,
            ]
        );

        return redirect('/inventory/sold')->withStatus("This item has been marked as dispatched and the buyer notified.");
    }

    /**
     * Apply a collection address to a purchase.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCollectionAddress(Request $request)
    {
        $purchase = Purchase::findOrFail($request->input('purchase_id'));

        if ($purchase->item->seller->userId != Auth::user()->userId) {
            return redirect('/inventory/sold')->withErrors([
                "You are not the seller of this item."
            ]);
        }

        if ($purchase->deliveryOption) {
            return redirect('/inventory/sold')->withErrors([
                "This item is not due to be collected."
            ]);
        }

        if ($purchase->hasAddress()) {
            return redirect('/inventory/sold')->withErrors([
                "You have already provided a collection address for this item."
            ]);
        }

        if ($request->input('postal_address') == 'add') {
            $postal_address = new PostalAddress;

            $postal_address->name = $request->input('name');
            $postal_address->street1 = $request->input('street1');
            $postal_address->street2 = $request->input('street2');
            $postal_address->city = $request->input('city');
            $postal_address->county = $request->input('county');
            $postal_address->country = $request->input('country');
            $postal_address->postcode = $request->input('postcode');

            $postal_address->user()->associate(Auth::user());

            if (!Iso3166::exists($request->input('country'))) {
                return redirect()->back()->withErrors([
                    'country' => 'Please select a valid country.'
                ]);
            }

            if ($request->input('remember')) {
                $postal_address->save();
            }
        } elseif (!$postal_address = PostalAddress::find($request->input('postal_address'))) {
            return redirect()->back()->withErrors(['postal_address' => 'Please select a valid postal address.']);
        } elseif ($postal_address->user != Auth::user()) {
            return redirect()->back()->withErrors(['postal_address' => 'You can only select your own postal addresses.']);
        }

        $purchase->useAddress($postal_address);

        $purchase->save();

        $purchase->buyer->sendEmail(
            'Your item is ready for collection',
            'emails.item.collection',
            [
                'item_name' => $purchase->item->name,
                'seller_name' => $purchase->name,
                'seller_street1' => $purchase->street1,
                'seller_street2' => $purchase->street2,
                'seller_city' => $purchase->city,
                'seller_county' => $purchase->county,
                'seller_postcode' => $purchase->postcode,
                'seller_country' => Iso3166::get($purchase->country)->name,
            ]
        );

        return redirect('/inventory/sold')->withStatus("Your collection address has been saved and sent to the buyer.");
    }

    /**
     * Register a received amount against a purchase.
     *
     * @param \Hamjoint\Mustard\Commerce\Purchase $purchase
     * @param float $amount
     * @return void
     */
    public static function paymentReceived(Purchase $purchase, $amount)
    {
        $purchase->received += $amount;

        if ($purchase->received >= $purchase->grandTotal) {
            $purchase->paid = time();

            $purchase->item->seller->sendEmail(
                'You have received a payment',
                'emails.item.paid',
                [
                    'total' => $purchase->received,
                    'item_name' => $purchase->item->name,
                    'buyer_name' => $purchase->name,
                    'buyer_street1' => $purchase->street1,
                    'buyer_street2' => $purchase->street2,
                    'buyer_city' => $purchase->city,
                    'buyer_county' => $purchase->county,
                    'buyer_postcode' => $purchase->postcode,
                    'buyer_country' => Iso3166::get($purchase->country)->name,
                    'has_delivery' => $purchase->hasDelivery(),
                    'has_address' => $purchase->hasAddress(),
                    'purchase_id' => $purchase->purchaseId,
                    'full' => $purchase->received == 0,
                ]
            );

            $purchase->buyer->sendEmail(
                'Receipt for your item',
                'emails.item.receipt',
                [
                    'total' => $purchase->received,
                    'item_name' => $purchase->item->name,
                    'seller_name' => $purchase->name,
                    'seller_street1' => $purchase->street1,
                    'seller_street2' => $purchase->street2,
                    'seller_city' => $purchase->city,
                    'seller_county' => $purchase->county,
                    'seller_postcode' => $purchase->postcode,
                    'seller_country' => Iso3166::get($purchase->country)->name,
                    'full' => $purchase->received == 0,
                    'has_delivery' => $purchase->hasDelivery(),
                    'has_address' => $purchase->hasAddress(),
                    'is_paid' => $purchase->isPaid()
                ]
            );
        }

        $purchase->save();
    }

    /**
     * Register a refunded amount against a purchase.
     *
     * @param \Hamjoint\Mustard\Commerce\Purchase $purchase
     * @param float $amount
     * @return void
     */
    public static function paymentRefunded(Purchase $purchase, $amount)
    {
        $purchase->refunded = time();

        $purchase->refundedAmount = $amount;

        $purchase->buyer->sendEmail(
            'You have been refunded',
            'emails.item.refunded',
            [
                'total' => $purchase->refundedAmount,
                'item_name' => $purchase->item->name,
            ]
        );

        $purchase->save();
    }

    /**
     * Register a failed amount against a purchase.
     *
     * @param \Hamjoint\Mustard\Commerce\Purchase $purchase
     * @param float $amount
     * @return void
     */
    public static function paymentFailed(Purchase $purchase, $amount)
    {
        Log::info("Payment failed ({$purchase->purchaseId})");

        $purchase->buyer->sendEmail(
            'Your payment has failed',
            'emails.item.failed',
            [
                'total' => $amount,
                'item_name' => $purchase->item->name,
                'payment_id' => $purchase->purchaseId,
            ]
        );
    }
}

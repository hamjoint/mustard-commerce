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
use Hamjoint\Mustard\Commerce\BankDetail;
use Hamjoint\Mustard\Commerce\PostalAddress;
use Hamjoint\Mustard\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tjbp\Countries\HasCountries;

class AccountController extends Controller
{
    use HasCountries;

    /**
     * Return the account postal addresses view.
     *
     * @return \Illuminate\View\View
     */
    public function getPostalAddresses()
    {
        return view('mustard::account.postal-addresses', [
            'page' => "postal-addresses",
        ]);
    }

    /**
     * Return the account bank details view.
     *
     * @return \Illuminate\View\View
     */
    public function getBankDetails()
    {
        $bank_details = Auth::user()->bankDetails ?: new BankDetail;

        return view('mustard::account.bank-details', [
            'bank_details' => $bank_details,
            'page' => "bank-details",
        ]);
    }

    /**
     * Add a postal address.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPostalAddress(Request $request)
    {
        $this->validates(
            $request->all(),
            [
                'name' => 'required',
                'street1' => 'required',
                'city' => 'required',
                'county' => 'required',
                'postcode' => 'required',
                'country' => 'required',
            ]
        );

        $pa = new PostalAddress;

        $pa->name = $request->input('name');
        $pa->street1 = $request->input('street1');
        $pa->street2 = $request->input('street2');
        $pa->city = $request->input('city');
        $pa->county = $request->input('county');
        $pa->postcode = $request->input('postcode');
        $pa->country = $this->country($request->input('country'))['alpha2'];
        $pa->added = time();

        $pa->user()->associate(Auth::user());

        $pa->save();

        return redirect('/account/postal-addresses')->withMessage('Postal address added.');
    }

    /**
     * Delete a postal address.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePostalAddress(Request $request)
    {
        $pa = PostalAddress::findOrFail($request->input('postal_address_id'));

        if ($pa->user->userId != Auth::user()->userId) {
            return redirect('/account/postal-addresses')
                ->withErrors(["You can only delete your own postal addresses."]);
        }

        if ($pa->purchases()->count()) {
            return redirect('/account/postal-addresses')
                ->withErrors(["You cannot delete postal addresses that are associated with purchases."]);
        }

        $pa->delete();

        return redirect('/account/postal-addresses')
            ->withMessage('Postal address deleted.');
    }

    /**
     * Add bank details.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBankDetails(Request $request)
    {
        $this->validates(
            $request->all(),
            [
                'account_number' => 'required|accountnumber',
                'sort_code' => 'required|sortcode',
            ]
        );

        $bank_details = Auth::user()->bankDetails ? Auth::user()->bankDetails : new BankDetail;

        $bank_details->accountNumber = $request->input('account_number');
        $bank_details->sortCode = $request->input('sort_code');

        if (!$bank_details->exists) {
            $bank_details->user()->associate(Auth::user());
        }

        $bank_details->save();

        Auth::user()->sendEmail(
            'Your bank details have been changed',
            'emails.account.bank-details-changed'
        );

        return redirect('/account/bank-details')
            ->withMessage('Your bank details have been changed.');
    }
}

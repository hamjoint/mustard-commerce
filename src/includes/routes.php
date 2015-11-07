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

Route::group([
    'prefix' => env('MUSTARD_BASE', ''),
    'namespace' => 'Hamjoint\Mustard\Commerce\Http\Controllers',
    'middleware' => 'auth',
], function()
{
    Route::get('account/postal-addresses', 'AccountController@getPostalAddresses');
    Route::post('account/postal-address', 'AccountController@postPostalAddress');
    Route::post('account/delete-postal-address', 'AccountController@postDeletePostalAddress');

    Route::get('account/bank-details', 'AccountController@getBankDetails');
    Route::post('account/bank-details', 'AccountController@postBankDetails');

    Route::get('inventory/bought', ['uses' => 'InventoryController@getBought']);
    Route::get('inventory/sold', ['uses' => 'InventoryController@getSold']);
    Route::get('inventory/unsold', ['uses' => 'InventoryController@getUnsold']);

    Route::get('checkout/{id}', 'PurchaseController@getCheckout');
    Route::get('pay/{id}', 'PurchaseController@getPay');
    Route::controller('purchase', 'PurchaseController');
});

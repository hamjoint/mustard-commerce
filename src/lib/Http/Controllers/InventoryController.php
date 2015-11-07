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
use Hamjoint\Mustard\Commerce\Tables\InventoryBought;
use Hamjoint\Mustard\Commerce\Tables\InventorySold;
use Hamjoint\Mustard\Commerce\Tables\InventoryUnsold;
use Hamjoint\Mustard\Http\Controllers\Controller;
use Hamjoint\Mustard\Item;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class InventoryController extends Controller
{
    /**
     * Return the inventory bought items view.
     *
     * @return \Illuminate\View\View
     */
    public function getBought()
    {
        $items = Item::join('seller')
            ->leftJoin('purchases')
            ->where('purchases.user_id', Auth::user()->userId);

        if (mustard_loaded('auctions')) {
            $items->leftJoin('winningBid')
                ->orWhere('bids.user_id', Auth::user()->userId);
        }

        $table = new InventoryBought($items);

        return view('mustard::inventory.bought', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }

    /**
     * Return the inventory sold items view.
     *
     * @return \Illuminate\View\View
     */
    public function getSold()
    {
        $items = Auth::user()->items()
            ->leftJoin('purchases')
            ->where(function ($query) {
                $query->typeFixed()->whereNotNull('purchases.purchase_id');
            });

        // Allows sorting by buyer username
        $items->getBaseQuery()
            ->join('users', 'users.user_id', '=', 'purchases.user_id');

        if (mustard_loaded('auctions')) {
            $items->leftJoin('winningBid')
                ->orWhere(function ($query) {
                    $query->typeAuction()->whereNotNull('bids.bid_id');
                });
        }

        $table = new InventorySold($items);

        return view('mustard::inventory.sold', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }

    /**
     * Return the inventory unsold items view.
     *
     * @return \Illuminate\View\View
     */
    public function getUnsold()
    {
        $items = Auth::user()->items()
            ->with('purchases')
            ->where(function ($query) {
                $query->has('purchases', 0)
                    ->where('end_date', '<', time())
                    ->where('auction', false)
                    ->orWhere(function ($query) {
                        $query->where('auction', true)
                            ->where('end_date', '<', time())
                            ->where('winning_bid_id', 0);
                    });
            });

        $table = new InventoryUnsold($items);

        return view('mustard::inventory.unsold', [
            'table' => $table,
            'items' => $table->paginate(),
        ]);
    }
}

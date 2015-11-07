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

namespace Hamjoint\Mustard\Commerce\Tables;

use DB;
use Foundation\Pagination\FoundationFivePresenter;
use Tablelegs\Table;

class InventorySold extends Table
{
    /**
     * Column headers for the table. URL-friendly keys with human values.
     *
     * @var array
     */
    public $columnHeaders = [
        'Item ID' => 'item_id',
        'Name' => 'name',
        'Buyer' => 'buyer',
        'Details' => null,
        'Date' => 'date',
        'Options' => null,
    ];

    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
        'Payment' => [
            'Received',
            'Pending'
        ],
        'Type' => [
            'Auction',
            'Fixed',
        ],
    ];

    /**
     * Default key to sort by.
     *
     * @var string
     */
    public $defaultSortKey = 'date';

    /**
     * Default sort order.
     *
     * @var string
     */
    public $defaultSortOrder = 'desc';

    /**
     * Class name for the paginator presenter.
     *
     * @var string
     */
    public $presenter = FoundationFivePresenter::class;

    /**
     * Include purchases with payment received only.
     *
     * @return void
     */
    public function filterPaymentReceived()
    {
        $this->db->where('purchases.received', '>', 0);
    }

    /**
     * Include purchases with payment pending only.
     *
     * @return void
     */
    public function filterPaymentPending()
    {
        $this->db->where('purchases.received', 0);
    }

    /**
     * Include auction-type items only.
     *
     * @return void
     */
    public function filterTypeAuction()
    {
        $this->db->typeAuction();
    }

    /**
     * Include fixed-type items only.
     *
     * @return void
     */
    public function filterTypeFixed()
    {
        $this->db->typeFixed();
    }

    /**
     * Sort items by item ID.
     *
     * @param string $sortOrder
     * @return void
     */
    public function sortItemId($sortOrder)
    {
        $this->db->orderBy('items.item_id', $sortOrder);
    }

    /**
     * Sort items by item name.
     *
     * @param string $sortOrder
     * @return void
     */
    public function sortName($sortOrder)
    {
        $this->db->orderBy('items.name', $sortOrder);
    }

    /**
     * Sort items by buyer username.
     *
     * @param string $sortOrder
     * @return void
     */
    public function sortBuyer($sortOrder)
    {
        $this->db->orderBy('users.username', $sortOrder);
    }

    /**
     * Sort items by purchase date.
     *
     * @param string $sortOrder
     * @return void
     */
    public function sortDate($sortOrder)
    {
        $this->db->orderBy('purchases.created', $sortOrder)
            ->orderBy('items.end_date', $sortOrder);
    }
}

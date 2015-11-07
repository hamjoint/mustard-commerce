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

class InventoryUnsold extends Table
{
    /**
     * Column headers for the table. URL-friendly keys with human values.
     *
     * @var array
     */
    public $columnHeaders = [
        'Item ID' => 'item_id',
        'Name' => 'name',
        'Duration' => 'duration',
        'Details' => null,
        'Time ended' => 'time_ended',
        'Options' => null,
    ];

    /**
     * Array of filter names containing available options and their keys.
     *
     * @var array
     */
    public $filters = [
        'Bids' => [
            'None',
            'One or more'
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
    public $defaultSortKey = 'time_ended';

    /**
     * Class name for the paginator presenter.
     *
     * @var string
     */
    public $presenter = FoundationFivePresenter::class;

    /**
     * Include items with no bids only.
     *
     * @return void
     */
    public function filterBidsNone()
    {
        $this->db->has('bids', 0);
    }

    /**
     * Include items with bids only.
     *
     * @return void
     */
    public function filterBidsOneOrMore()
    {
        $this->db->has('bids');
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
     * Sort items by item end date.
     *
     * @param string $sortOrder
     * @return void
     */
    public function sortTimeEnded($sortOrder)
    {
        $this->db->orderBy(DB::raw('unix_timestamp() - cast(`end_date` as signed)'), $sortOrder);
    }
}

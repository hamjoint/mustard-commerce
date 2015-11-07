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

namespace Hamjoint\Mustard\Commerce;

class Purchase extends \Hamjoint\Mustard\Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchases';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'purchase_id';

    /**
     * Returns true if payment has been received.
     *
     * @return boolean
     */
    public function isPaid()
    {
        return (bool) $this->paid;
    }

    /**
     * Returns true if purchase has been dispatched.
     *
     * @return boolean
     */
    public function isDispatched()
    {
        return (bool) $this->dispatched;
    }

    /**
     * Returns true if payment has been refunded.
     *
     * @return boolean
     */
    public function isRefunded()
    {
        return (bool) $this->refunded;
    }

    /**
     * Returns true if purchase has a delivery option.
     *
     * @return boolean
     */
    public function hasDelivery()
    {
        return (bool) $this->deliveryOptionId;
    }

    /**
     * Returns true if purchase has an address.
     *
     * @return boolean
     */
    public function hasAddress()
    {
        return (bool) $this->country;
    }

    /**
     * Calculate grand total in respect to quantity and delivery.
     *
     * @return float
     */
    public function getGrandTotalAttribute()
    {
        if (!$this->deliveryOption) return $this->total;

        return (float) round($this->total + $this->deliveryOption->price, 2);
    }

    /**
     * Calculate grand total with commission deducted.
     *
     * @return float
     */
    public function getNetTotalAttribute()
    {
        return (float) round($this->grandTotal * (1 - $this->item->commission), 2);
    }

    /**
     * Calculate grand total in respect of any received sum.
     *
     * @return float
     */
    public function getGrandTotalRemainingAttribute()
    {
        return (float) round($this->grandTotal - $this->received, 2);
    }

    /**
     * Mass-assignment method for using a postal address.
     *
     * @param \Hamjoint\Mustard\Commerce\PostalAddress $postalAddress
     * @return void
     */
    public function useAddress(PostalAddress $postalAddress)
    {
        $this->name = $postalAddress->name;
        $this->street1 = $postalAddress->street1;
        $this->street2 = $postalAddress->street2;
        $this->city = $postalAddress->city;
        $this->county = $postalAddress->county;
        $this->postcode = $postalAddress->postcode;
        $this->country = $postalAddress->country;
    }

    /**
     * Relationship to a delivery option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryOption()
    {
        return $this->belongsTo('\Hamjoint\Mustard\DeliveryOption');
    }

    /**
     * Relationship to a feedback rating.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feedback()
    {
        return $this->hasOne('\Hamjoint\Mustard\Feedback\UserFeedback');
    }

    /**
     * Relationship to an item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('\Hamjoint\Mustard\Item');
    }

    /**
     * Relationship to a purchasing user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyer()
    {
        return $this->belongsTo('\Hamjoint\Mustard\User', 'user_id');
    }

    /**
     * Return the total number of purchases.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function totalCreated($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->count();
    }

    /**
     * Return the average amount of purchases.
     *
     * @param integer $since UNIX timestamp to optionally specify a lower selection boundary.
     * @param integer $until UNIX timestamp to optionally specify an upper selection boundary.
     * @return integer
     */
    public static function averageAmount($since = 0, $until = null)
    {
        $until = $until ?: time();

        return (int) self::where('created', '>=', $since)
            ->where('created', '<=', $until)
            ->avg('total');
    }
}

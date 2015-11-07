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

class PostalAddress extends \Hamjoint\Mustard\Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'postal_addresses';

    /**
     * The database key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'postal_address_id';

    /**
     * Relationship to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\Hamjoint\Mustard\User');
    }
}

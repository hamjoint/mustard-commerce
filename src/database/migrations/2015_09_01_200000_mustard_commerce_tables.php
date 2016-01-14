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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MustardCommerceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_details', function(Blueprint $table)
        {
            $table->integer('bank_detail_id', true)->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('account_number');
            $table->text('sort_code');

            $table->foreign('user_id')->references('user_id')->on('users');
        });

        Schema::create('purchases', function(Blueprint $table)
        {
            $table->integer('purchase_id', true)->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('delivery_option_id')->unsigned();
            $table->decimal('unit_price', 8, 2)->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('total', 8, 2)->unsigned();
            $table->decimal('received', 8, 2)->unsigned();
            $table->integer('refunded')->unsigned();
            $table->decimal('refunded_amount', 8, 2)->unsigned();
            $table->integer('created')->unsigned();
            $table->integer('paid')->unsigned();
            $table->integer('dispatched')->unsigned();
            $table->string('tracking_number', 128);
            $table->char('country', 2);
            $table->string('name', 64);
            $table->string('street1', 64);
            $table->string('street2', 64);
            $table->string('city', 64);
            $table->string('county', 64);
            $table->string('postcode', 16);

            $table->foreign('item_id')->references('item_id')->on('items');
            $table->foreign('user_id')->references('user_id')->on('users');
        });

        Schema::create('postal_addresses', function(Blueprint $table)
        {
            $table->integer('postal_address_id', true)->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->char('country', 2);
            $table->string('name', 64);
            $table->string('street1', 64);
            $table->string('street2', 64);
            $table->string('city', 64);
            $table->string('county', 64);
            $table->string('postcode', 16);
            $table->integer('added')->unsigned();

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('postal_addresses');
        Schema::drop('purchases');
        Schema::drop('bank_details');
    }
}

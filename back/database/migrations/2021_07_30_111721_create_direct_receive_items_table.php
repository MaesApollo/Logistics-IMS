<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectReceiveItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direct_receive_item', function (Blueprint $table) {
            $table->integer('direct_receive_id');
            $table->integer('item_id');
            $table->integer('warehouse_id');
            $table->integer('qty');
            $table->decimal('price')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direct_receive_item');
    }
}

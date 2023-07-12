<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('item_id');
            $table->integer('warehouse_id');
            $table->integer('purchase_order_id')->nullable();
            $table->decimal('price')->default(0.00);
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->string('received_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}

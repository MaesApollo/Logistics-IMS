<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryReceiptItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_receipt_item', function (Blueprint $table) {
            $table->integer('delivery_receipt_id');
            $table->integer('item_id');
            $table->string('barcode')->nullable();
            $table->integer('qty');
            $table->integer('qty_return');
            $table->decimal('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_receipt_item');
    }
}

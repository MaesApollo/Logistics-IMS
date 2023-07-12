<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemDeliveryReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_delivery_receipt', function (Blueprint $table) {
            $table->integer('delivery_receipt_id');
            $table->integer('item_id');
            $table->string('barcode')->nullable();
            $table->integer('qty');
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
        Schema::dropIfExists('item_delivery_receipt');
    }
}

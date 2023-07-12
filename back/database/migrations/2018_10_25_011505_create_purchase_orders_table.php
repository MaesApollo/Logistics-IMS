<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('supplier_id')->nullable();
            $table->decimal('shipping_fee')->default(0.00);
            $table->decimal('tax')->default(0.00);
            $table->string('file')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE purchase_orders AUTO_INCREMENT = 10000");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}

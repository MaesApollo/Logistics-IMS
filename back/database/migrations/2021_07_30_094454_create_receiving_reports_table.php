<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('received_from')->nullable();
            $table->integer('purchase_no')->nullable();
            $table->integer('stock_no')->nullable();
            $table->integer('invoice_num')->nullable();
            $table->string('freight')->nullable();
            $table->string('date_received')->nullable();
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
        Schema::dropIfExists('receiving_reports');
    }
}

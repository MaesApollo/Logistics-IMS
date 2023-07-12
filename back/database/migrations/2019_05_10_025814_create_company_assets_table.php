<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_assets_types_id');
            $table->string('name');
            $table->string('model');
            $table->string('area');
            $table->string('accountable_name')->nullable();
            $table->datetime('date_accounted')->nullable();
            $table->string('remarks')->nullable();
            $table->datetime('date_in');
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
        Schema::dropIfExists('company_assets');
    }
}

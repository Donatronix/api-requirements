<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreatePricesTable.
 */
class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('product_id');
            $table->unsignedBigInteger('original');
            $table->unsignedBigInteger('final');
            $table->string('discount_percentage')->nullable();
            $table->string('currency')->default('EUR');
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
        Schema::drop('prices');
    }
}

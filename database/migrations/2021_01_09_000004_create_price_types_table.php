<?php

use Illuminate\Support\Facades\Schema;
use Unusualify\Priceable\Models\Price;
use Illuminate\Database\Schema\Blueprint;
use Unusualify\Priceable\Models\PriceType;
use Illuminate\Database\Migrations\Migration;

class AddPriceTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_types');
    }
}

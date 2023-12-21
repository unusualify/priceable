<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('priceable.tables.prices'), function (Blueprint $table) {
            $table->id();
            $table->morphs('priceable');

            $table->unsignedBigInteger('price_type_id')->default(null)->nullable();

            $table->unsignedBigInteger('vat_rate_id');
            $table->unsignedBigInteger('currency_id');
            $table->bigInteger('display_price')->default(0);
            $table->bigInteger('price_excluding_vat')->default(0);
            $table->bigInteger('price_including_vat')->default(0);
            $table->bigInteger('vat_amount')->default(0);
            $table->timestamp('valid_from')->nullable()->default(null);
            $table->timestamp('valid_till')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vat_rate_id')->references('id')->on(config('priceable.tables.vat_rates'));
            $table->foreign('currency_id')->references('id')->on(config('priceable.tables.currencies'));
            $table->foreign('price_type_id')->references('id')->on(config('priceable.tables.price_types'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('priceable.tables.prices'));
    }
};

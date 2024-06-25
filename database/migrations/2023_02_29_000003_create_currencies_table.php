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
        Schema::create(config('priceable.tables.currencies'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('symbol', 10)->nullable()->default(NULL);
            $table->string('iso_4217', 3)->default(null)->nullable();
            $table->integer('iso_4217_number')->default(null)->nullable();
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
        Schema::dropIfExists(config('priceable.tables.currencies'));
    }
};

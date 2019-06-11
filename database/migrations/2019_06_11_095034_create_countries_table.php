<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('code');
            $table->string('name', 30);
            $table->unsignedSmallInteger('tax_haven_code')->nullable();
            $table->string('tax_haven_name', 300)->nullable();
            $table->timestamps();
            $table->unique(['code', 'tax_haven_code'], 'country_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            // 'name', 'latitude', 'longitude', 'price', 'distance'
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('price')->nullable();
            $table->string('distance')->nullable();
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
        Schema::dropIfExists('presentation_workflows');
    }
}

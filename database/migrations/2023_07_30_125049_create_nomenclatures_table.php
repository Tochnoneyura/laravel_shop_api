<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNomenclaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nomenclatures', function (Blueprint $table) {
            $table->string('guid', 36);
            $table->string('code', 11);
            $table->string('name', 100);
            $table->string('fullname');
            $table->string('set_number', 25);
            $table->string('brand_guid', 36)->references('guid')->on('brands');
            $table->float('price', 15, 2)->nullable();
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
        Schema::dropIfExists('nomenclatures');
    }
}

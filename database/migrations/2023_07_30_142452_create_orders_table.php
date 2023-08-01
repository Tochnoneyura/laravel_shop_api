<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->boolean('is_processed')->default('false');
            $table->float('total', 15, 2)->nullable();
            $table->string('payment_status')->default('НеОплачен');
            $table->string('status', 36)->references('guid')->on('document_statuses')->default('4454a860-1b1e-4020-83c4-beaede97e3ec');
            $table->string('delivery_method');
            $table->date('delivery_date');
            $table->string('delivery_address');
            $table->string('delivery_company');
            $table->string('contact_name');
            $table->string('contact_phone', 255);
            $table->string('website_comment')->nullable();
            $table->string('website_comment_for_client')->nullable();
            $table->timestamp('latest_update_by_client');
            $table->string('payment_type');
            $table->boolean('is_delivery_today');
            $table->integer('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('orders');
    }
}

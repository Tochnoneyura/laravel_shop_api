<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PrepareOrderCheckout extends Migration
{
    public function up()
    {
        Schema::table('nomenclatures', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->change();
        });
        Schema::table('baskets', function (Blueprint $table) {
            $table->decimal('amount', 15, 3)->nullable()->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total', 30, 2)->nullable()->change();
            $table->date('delivery_date')->nullable()->change();
            $table->index(['created_by', 'id']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('amount', 15, 3)->change();
            $table->decimal('price', 15, 2)->nullable()->change();
        });
    }

    public function down()
    {
        DB::table('orders')->whereNull('delivery_date')->update(['delivery_date' => DB::raw('date')]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->float('price', 15, 2)->nullable()->change();
            $table->float('amount', 15, 3)->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_by', 'id']);
            $table->float('total', 15, 2)->nullable()->change();
            $table->date('delivery_date')->nullable(false)->change();
        });
        Schema::table('baskets', function (Blueprint $table) {
            $table->float('amount', 15, 3)->nullable()->change();
        });
        Schema::table('nomenclatures', function (Blueprint $table) {
            $table->float('price', 15, 2)->nullable()->change();
        });
    }
}

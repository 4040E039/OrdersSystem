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
          $table->integer('raise_order_id')->index();
          $table->integer('user_id');
          $table->string('order_item', '150');
          $table->integer('order_quantity');
          $table->float('order_cost', '10', '2');
          $table->text('memo')->nullable();
          $table->softDeletes('deleted_at', 0);
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

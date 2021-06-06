<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaiseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raise_orders', function (Blueprint $table) {
          $table->id();
          $table->integer('restaurant_id');
          $table->integer('user_id');
          $table->string('raise_order_token', '150')->unique();
          $table->string('raise_order_theme', '150');
          $table->dateTime('start_time')->index();
          $table->dateTime('end_time')->index();
          $table->text('memo')->nullable();
          $table->tinyInteger('is_found')->default(0);
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
        Schema::dropIfExists('raise_orders');
    }
}

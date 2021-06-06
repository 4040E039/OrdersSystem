<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trading_records', function (Blueprint $table) {
          $table->id();
          $table->foreignId('user_id')->index()->constrained('users')->onDelete('cascade');
          $table->string('trading_item', '150');
          $table->float('trading_cost', '10', '2');
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
        Schema::dropIfExists('trading_records');
    }
}

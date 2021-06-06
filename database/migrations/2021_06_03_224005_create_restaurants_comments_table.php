<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants_comments', function (Blueprint $table) {
          $table->id();
          $table->foreignId('restaurant_id')->index()->constrained('restaurants')->onDelete('cascade');
          $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
          $table->string('message', '255')->nullable();
          $table->tinyInteger('score');
          $table->timestamps();

          $table->unique(['restaurant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants_comments');
    }
}

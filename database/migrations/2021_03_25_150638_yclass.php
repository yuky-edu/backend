<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Yclass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('yclasses', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('user');
          $table->unsignedBigInteger('yclass_category');
          $table->string("code")->unique();
          $table->string("title");
          $table->string("description");
          $table->timestamps();

          $table->foreign("user")->references("id")->on("users")->onDelete("cascade");
          $table->foreign("yclass_category")->references("id")->on("yclass_categories")->onDelete("cascade");
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('yclasses');
    }
}

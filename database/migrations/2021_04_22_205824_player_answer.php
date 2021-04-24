<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PlayerAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player');
            $table->unsignedBigInteger('entity');
            $table->enum('answer', ['a1', 'a2', 'a3', 'a4', 'a5', 'a6']);
            $table->timestamps();

            $table->foreign("player")->references("id")->on("players")->onDelete("cascade");
            $table->foreign("entity")->references("id")->on("entities")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_answers');
    }
}

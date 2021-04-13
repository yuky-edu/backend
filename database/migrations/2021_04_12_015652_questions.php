<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Questions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("yclass");
            $table->string("question");
            $table->text("media")->nullable()->default(null);
            $table->string("a1");
            $table->string("a2");
            $table->string("a3")->nullable()->default(null);
            $table->string("a4")->nullable()->default(null);
            $table->string("a5")->nullable()->default(null);
            $table->string("a6")->nullable()->default(null);
            $table->enum("correct", ["a1", "a2", "a3", "a4", "a5", "a6"]);
            $table->timestamps();

            $table->foreign("yclass")->references("id")->on("yclasses")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class YclassSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yclass_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('yclass');
            $table->integer('index_question')->default(0);
            $table->enum('isExplain', [0, 1])->default(0);
            $table->enum('played', [0, 1])->default(1);
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
        Schema::dropIfExists('yclass_sessions');
    }
}

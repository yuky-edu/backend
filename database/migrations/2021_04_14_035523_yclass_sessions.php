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
            $table->integer('index_entity')->default(0);
            $table->enum('status', ['off', 'wait', 'on_mode_block', 'on_mode_play'])->default('wait');
            $table->string("ws_channel")->unique();
            $table->string("answered_entity")->default('[]');
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

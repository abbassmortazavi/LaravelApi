<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordBackUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('word_back_ups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('word_id');
            $table->string('user_note')->nullable();
            $table->string('leitner_level')->nullable();
            $table->string('last_leitner_date')->nullable();

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
        Schema::dropIfExists('word_back_ups');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word');
            $table->string('phonetic');
            $table->text('english_meaning');
            $table->text('persian_meaning');
            $table->text('english_example');
            $table->text('persian_example');
            $table->string('user_note')->default(null);
            $table->text('choices_question')->default(null);
            $table->string('correspondence_question')->default(null);
            $table->string('image_path')->default(null);
            $table->boolean('is_delete')->default('0');
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
        Schema::dropIfExists('words');
    }
}

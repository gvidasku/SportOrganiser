<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisator_id');
            $table->string('sportevent_title', 50);
            $table->string('sport_category', 20);
            $table->unsignedSmallInteger('attendance');
            $table->string('event_type');
            $table->string('price', 30);
            $table->string('sportevent_location');
            $table->timestamp('date');
            $table->string('level');
            $table->string('age');
            $table->string('time');
            $table->text('description');
            $table->unsignedMediumInteger('views')->default(1);
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
        Schema::dropIfExists('posts');
    }
}

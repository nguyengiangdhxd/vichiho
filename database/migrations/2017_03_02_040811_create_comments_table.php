<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('object_id');
            $table->string('object_type', 50);
            $table->enum('scope', ['EXTERNAL','INTERNAL'])->default('EXTERNAL');
            $table->text('message');
            $table->enum('type_context', ['CHAT','ACTIVITY','LOG'])->default('CHAT');
            $table->tinyInteger('is_public_profile')->default(1);
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
        Schema::dropIfExists('comments');
    }
}

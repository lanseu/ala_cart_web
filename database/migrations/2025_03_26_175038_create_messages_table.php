<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['conversation', 'promotion']); 
            $table->text('chat');
            $table->string('iconpath')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->boolean('hasUnread')->default(false);
            $table->boolean('isMe')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

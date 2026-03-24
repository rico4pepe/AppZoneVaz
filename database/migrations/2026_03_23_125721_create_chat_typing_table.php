<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_typing', function (Blueprint $table) {
                  $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->unsignedBigInteger('match_id')->nullable();
        $table->timestamp('updated_at')->nullable();

        $table->unique(['user_id', 'match_id']); // prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_typing');
    }
};

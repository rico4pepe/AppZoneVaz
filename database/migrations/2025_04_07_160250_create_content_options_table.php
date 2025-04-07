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
        Schema::create('content_options', function (Blueprint $table) {
        $table->id();
        $table->foreignId('content_id')->constrained()->onDelete('cascade');
        $table->string('option_text');
        $table->boolean('is_correct')->default(false); // Only used for quizzes/trivia
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
        Schema::dropIfExists('content_options');
    }
};

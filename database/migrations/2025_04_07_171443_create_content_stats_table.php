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
        Schema::create('content_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('correct_answers')->default(0); // for quiz/trivia
            $table->unsignedInteger('poll_votes')->default(0); // for poll
            $table->json('option_counts')->nullable(); // counts per option (poll/trivia)
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
        Schema::dropIfExists('content_stats');
    }
};

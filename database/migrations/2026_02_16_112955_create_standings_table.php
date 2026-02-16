<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('team_id');

            $table->integer('season');
            $table->integer('position');

            $table->integer('played');
            $table->integer('wins');
            $table->integer('draws');
            $table->integer('losses');

            $table->integer('goals_for');
            $table->integer('goals_against');
            $table->integer('goal_difference');

            $table->integer('points');

            $table->timestamps();

            $table->unique(['league_id', 'team_id', 'season']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standings');
    }
};

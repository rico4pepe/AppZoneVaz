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
        Schema::table('users', function (Blueprint $table) {
            //
               $table->foreignId('team_id')
                  ->nullable()
                  ->after('club') // Optional: position the column
                  ->constrained('teams')
                  ->nullOnDelete(); // When team is deleted, set user's team_id to null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
              $table->dropForeign(['team_id']);
            // Then drop the column
            $table->dropColumn('team_id');
        });
    }
};

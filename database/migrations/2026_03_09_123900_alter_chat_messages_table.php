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
        //
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('match_id')
                ->nullable()
                ->after('user_id')
                ->constrained('matches')
                ->cascadeOnDelete();

            $table->index(['match_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback the changes to the chat_messages table
        
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropForeign(['match_id']);
                $table->dropIndex(['match_id', 'id']);
                $table->dropColumn('match_id');
            });
        
    }
};

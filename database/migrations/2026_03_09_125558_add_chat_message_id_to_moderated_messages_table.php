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
        Schema::table('moderated_messages', function (Blueprint $table) {
            //
             $table->foreignId('chat_message_id')
              ->nullable()
              ->after('id')
              ->constrained('chat_messages')
              ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moderated_messages', function (Blueprint $table) {
            //
                 $table->dropForeign(['chat_message_id']);
                $table->dropColumn('chat_message_id');
            
        });
    }
};

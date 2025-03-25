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

            $table->string('plan')->nullable()->after('email'); // Subscription plan
            $table->timestamp('subscribed_at')->nullable()->after('plan'); // Subscription start time
            $table->timestamp('expires_at')->nullable()->after('subscribed_at'); // Subscription end time
            $table->string('phone_number')->unique()->after('expires_at'); // User phone number
            $table->string('token')->nullable()->after('phone_number'); // Temporary login token for WiFi users
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
            $table->dropColumn(['plan', 'subscribed_at', 'expires_at', 'phone_number', 'token']);
        });
    }
};

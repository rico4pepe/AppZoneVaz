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
        Schema::table('leagues', function (Blueprint $table) {
            //
                $table->string('type', 50)->nullable()->after('name');
            $table->string('country', 100)->nullable()->after('type');
            $table->string('country_code', 10)->nullable()->after('country');
            $table->string('logo')->nullable()->after('country_code');
            $table->boolean('is_active')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leagues', function (Blueprint $table) {
            //
            $table->dropColumn(['type', 'country', 'country_code', 'logo', 'is_active']);
        });
    }
};

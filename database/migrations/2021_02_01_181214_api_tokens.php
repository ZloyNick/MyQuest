<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->string('api_token', 80);
            $table->id();
        });

        // 10 tokens
        \App\ApiToken::forceCreate(10);

        \App\ApiToken::create(['api_token' => 'si0Z9bZrD8JPqVLYeRZ5FI5DDCaLPhRu4WWVp22JVErWeh0Lssx64xPcQ3UlB6F4qqONjascVzEeskVA']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_tokens');
    }
}

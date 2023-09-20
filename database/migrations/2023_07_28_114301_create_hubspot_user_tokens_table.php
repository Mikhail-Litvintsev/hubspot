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
        Schema::create('hubspot_user_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('block_id')->nullable();
            $table->string('code');
            $table->jsonb('hubspot_user_token_dto')->nullable();
            $table->timestamp('expire_at');
            $table->timestamps();

            $table->unique(['user_id', 'block_id'], 'hubspot_user_tokens_user_id_block_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hubspot_user_tokens');
    }
};

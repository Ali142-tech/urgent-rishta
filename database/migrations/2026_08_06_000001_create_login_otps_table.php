<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginOtpsTable extends Migration
{
    public function up()
    {
        Schema::create('login_otps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identifier', 32)->index(); // normalized mobile
            $table->string('channel', 16)->default('email'); // email | sms (future)
            $table->string('code_hash');
            $table->string('delivery_target')->nullable(); // email used, or phone for SMS later
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_otps');
    }
}

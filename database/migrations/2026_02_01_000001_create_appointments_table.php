<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('appointments')) {
            return;
        }

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('appointment_date');
            $table->string('appointment_time', 20)->nullable(); // e.g. "10:00 AM", "14:00"
            $table->string('subject', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending')->index(); // pending, confirmed, cancelled, completed
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}

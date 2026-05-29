<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->morphs('eventsable');
            $table->enum('type', ['meeting', 'appointment', 'reminder'])->default('meeting');
            $table->boolean('all_day')->default(false);
            $table->string('organizer')->nullable();
            $table->string('attendees')->nullable();
            $table->string('status')->default('scheduled');
            $table->date('event_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

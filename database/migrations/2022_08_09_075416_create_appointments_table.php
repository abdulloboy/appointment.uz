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
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('performer_id');
            $table->foreignUuid('practitioner_id');
            $table->foreignUuid('patient_id');
            $table->enum('status', [
                "proposed",
                "pending",
                "booked",
                "arrived",
                "fulfilled",
                "cancelled",
                "noshow",
                "entered-in-error",
                "checked-in",
                "waitlist"
            ]);

            $table->enum('use', [
                "usual",
                "official",
                "temp",
                "secondary",
                "old (If known)"
            ]);
            $table->string('type');
            $table->string('value');
            $table->string('system');
            $table->timestamp('period_start');
            $table->timestamp('period_end')->nullable();
            $table->string('assigner');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};

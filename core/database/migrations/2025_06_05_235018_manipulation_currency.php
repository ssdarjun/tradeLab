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
        Schema::create('manipulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->tinyInteger('prediction_override')->comment('1 = High, 2 = Low');
            $table->float('min')->default(0.0);
            $table->float('max')->default(0.0);
            $table->float('current')->default(0.0)->comment('Current price of the crypto during manipulation');
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
        //
    }
};

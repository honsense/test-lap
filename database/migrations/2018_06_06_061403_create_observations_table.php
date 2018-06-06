<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('request_id')->unsigned();
            $table->string('reference');
            $table->text('observation');
            $table->text('actions');
            $table->text('response')->nullable();
            $table->integer('approved')->default(0);
            $table->string('criticality');
            $table->datetime('responded_on')->nullable(); //formerly date_responded
            $table->datetime('approved_on')->nullable(); //formerly date_approved
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps(); //check vue components, controllers for references to date_observed, updated_on
        });

        Schema::table('observations', function (Blueprint $table) {
        $table->foreign('request_id')->references('id')->on('requests')->onDelete('restrict')->onUpdate('restrict');
        $table->foreign('reference')->references('reference')->on('requests')->onDelete('restrict')->onUpdate('cascade');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('observations');
    }
}

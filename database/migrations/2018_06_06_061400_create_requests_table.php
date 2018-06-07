<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('reference')->unique();
            $table->string('location');
            $table->string('sample_type')->nullable();
            $table->string('requester');
            $table->string('method');
            $table->string('assigned_reviewer')->nullable();
            $table->date('due_on')->nullable(); //formerly date_due
            $table->string('comments')->nullable();
            $table->integer('approved')->default(0);
            $table->datetime('approved_on')->nullable();
            $table->string('approved_by')->nullable();
            $table->datetime('submitted_on')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('started_on')->nullable();
            $table->datetime('to_analyst_on')->nullable();
            $table->datetime('assigned_on')->nullable();
            $table->string('prnumber')->nullable();
            $table->string('updated_by');
            $table->string('status');
            $table->timestamps(); //check for references to DATE_REQUESTED, UPDATED_ON
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}

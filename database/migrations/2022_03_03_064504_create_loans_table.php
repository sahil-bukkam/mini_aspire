<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->integer('loan_term')->comment('in weeks');
            $table->tinyInteger('status')->default(0)->comment('0- Pending, 1- Approved, 2- Rejected');
            $table->decimal('weekly_amount', 10, 2)->default(0);
            $table->decimal('amount_remaining', 10, 2)->default(0);
            $table->decimal('previous_weekly_amount', 10, 2)->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}

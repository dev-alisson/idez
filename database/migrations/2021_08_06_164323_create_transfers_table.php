<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'transfers',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('shipping_account_id');
                $table->unsignedBigInteger('receiving_account_id');
                $table->decimal('amount');
                $table->timestamps();

                $table->foreign('shipping_account_id')
                    ->references('id')
                    ->on('accounts')
                    ->onDelete('cascade');

                $table->foreign('receiving_account_id')
                    ->references('id')
                    ->on('accounts')
                    ->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}

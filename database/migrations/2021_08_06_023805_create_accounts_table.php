<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'accounts',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->after('id');
                $table->string('agency');
                $table->string('number');
                $table->char('digit');
                $table->string('cnpj')->nullable()->unique();
                $table->string('corporate_name')->nullable();
                $table->string('fantasy_name')->nullable();
                $table->string('type');
                $table->decimal('balance')->default(0);
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('accounts');
    }
}

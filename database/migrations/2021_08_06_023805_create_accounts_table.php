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
                $table->string('agency');
                $table->string('number');
                $table->char('digit');
                $table->string('cnpj')->nullable()->unique();
                $table->string('corporate_name')->nullable();
                $table->string('fantasy_name')->nullable();
                $table->string('type');
                $table->decimal('balance')->default(0);
                $table->timestamps();
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

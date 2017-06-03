<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lname');
            $table->string('fname');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('phone');
            $table->string('role');
            $table->string('identity')->nullable();
            $table->string('address');
            $table->string('postcode');
            $table->string('state');
            $table->string('city');
            $table->string('nationality');
            $table->string('ic')->nullable();
            $table->string('gender');
            $table->string('bank')->nullable();
            $table->string('account')->nullable();
            $table->string('license')->nullable();
            $table->string('license_plate')->nullable();
            $table->string('online_status');
            $table->string('status');
            $table->float('lat', 30, 20)->nullable();
            $table->float('long', 30, 20)->nullable();
            $table->string('current_postcode')->nullable();
            $table->string('current_placeid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

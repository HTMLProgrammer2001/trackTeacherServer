<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('fullName');
            $table->date('birthday')->nullable();
            $table->string('passport')->nullable()->unique();
            $table->string('code')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->bigInteger('commission_id')->unsigned();
            $table->bigInteger('department_id')->unsigned();
            $table->bigInteger('rank_id')->unsigned()->nullable();
            $table->integer('role')->default(\Constants::$roles['user']);
            $table->string('avatar')->nullable();
            $table->smallInteger('hiring_year')->nullable();
            $table->smallInteger('pedagogical_title')->nullable();
            $table->smallInteger('experience')->default(0)->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->smallInteger('academic_status')->nullable();
            $table->smallInteger('academic_status_year')->nullable();
            $table->smallInteger('scientific_degree')->nullable();
            $table->smallInteger('scientific_degree_year')->nullable();

            $table->rememberToken();
            $table->timestamps();

            //relations
            $table->foreign('commission_id')->references('id')
                ->on('commissions')->onDelete('CASCADE')->onUpdate('CASCADE');
            
            $table->foreign('department_id')->references('id')
                ->on('departments')->onDelete('CASCADE')->onUpdate('CASCADE');
            
            $table->foreign('rank_id')->references('id')
                ->on('ranks')->onDelete('SET NULL');
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

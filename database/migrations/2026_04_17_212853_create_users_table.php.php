<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    /**

     * Run the migrations.

     */

    public function up()

    {

            Schema::create('users',function (Blueprint $table){

                $table->id();

                $table->string('full_name',150);

                $table->string('email',150)->unique();

                $table->string('password_hash');

                $table->char('cpf',20)->unique()->nullable();

                $table->string('phone', 20)->nullable();

                $table->enum('role', ['customer', 'admin'])->default('customer');

                $table->boolean('active')->default(true);

                $table->timestamp('last_login')->nullable();

                $table->timestamps();

            });

    }



    /**

     * Reverse the migrations.

     */

    public function down()

    {

        Schema::dropIfExists('users');

    }

};


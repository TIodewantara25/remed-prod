<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //mebuat table beserta kolong dan atribut   
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            //membuat kolom
            $table->id();
            $table->string("username")->unique();
            $table->string("email")->unique();
            $table->string("password");
            $table->enum    ("role", ['staff','admin']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */

    //membatalkan perbahan 
    public function down(): void
    {
        //mengenbalikan table user dari data base  
        Schema::dropIfExists('users');
    }
};

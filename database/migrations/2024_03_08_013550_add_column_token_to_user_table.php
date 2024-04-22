<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable();
            //$table->renameColum('token', 'api_token');
            //menhambil nama  kolom, argumn 1 nama kolom yang lama, argument 2 nama kolom baru

            //$table->text('token')->change();
            //menganti tipe data dari string ke text lalu di ikuti method change (karena merubah, bukan menambah)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColum('token');
        //$table->renameColum(api_token', 'token');
        //menhambil nama  kolom, argumn 1 nama kolom yang baru, argument 2 nama kolom lama
        });
    }
};

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

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->string('operation');
            //por defecto el estado (2) en revision
            $table->unsignedTinyInteger('status')->default(2);
            //por defecto la operacion es un deposito (1)
            //pero puede ser una transferencia de otra persona
            //Puede ser un retiro
            $table->unsignedTinyInteger('type')->default(1); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receptor_id')->nullable();
            $table->unsignedBigInteger('emisor_id')->nullable();
            $table->unsignedBigInteger('herramienta_id');
            $table->integer('cantidad');
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_limite');
            $table->text('comentarios')->nullable();
            $table->enum('status', ['activo', 'devuelto', 'atrasado'])->default('activo'); // Estado del préstamo
            $table->timestamps();
        
            // Llaves foráneas
            $table->foreign('herramienta_id')->references('id')->on('inventarios')->onDelete('cascade');
            $table->foreign('emisor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('receptor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prestamos');
    }
};

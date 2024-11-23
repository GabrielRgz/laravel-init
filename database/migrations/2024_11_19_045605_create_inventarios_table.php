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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_id')->constrained('catalogos')->onDelete('cascade'); // Relación con la tabla catalogos
            $table->integer('cantidad_stock')->default(0); // Cantidad en stock
            $table->string('ubicacion'); // Ubicación del artículo en el inventario
            $table->string('tipo', 50)->default('herramienta'); // Nuevo campo: tipo (herramienta o insumos)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventarios');
    }
};

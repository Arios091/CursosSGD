<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
                $table->string('titulo');
                $table->string('tipo'); // 'video', 'pdf', 'cuestionario'
                $table->string('url'); // enlace al video/PDF o ID del cuestionario
                $table->integer('orden')->default(1);
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
        Schema::dropIfExists('materials');
    }
}

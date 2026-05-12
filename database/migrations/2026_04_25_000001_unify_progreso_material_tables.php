<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UnifyProgresoMaterialTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('progreso_materiales');
        Schema::dropIfExists('progreso_material');

        Schema::create('progreso_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->boolean('material_completado')->default(false);
            $table->timestamp('completado_at')->nullable();
            $table->integer('tiempo_visto')->default(0);
            $table->boolean('video_completado')->default(false);
            $table->boolean('scroll_completado')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'material_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('progreso_material');
    }
}

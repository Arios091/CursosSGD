<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuestionarioTables extends Migration
{
    public function up()
    {
        // Tabla de cuestionarios por módulo
        Schema::create('cuestionarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->integer('min_aprobacion')->default(75);
            $table->timestamps();
        });

        // Tabla de preguntas
        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuestionario_id')->constrained()->onDelete('cascade');
            $table->text('pregunta');
            $table->integer('orden')->default(1);
            $table->timestamps();
        });

        // Tabla de opciones (respuestas)
        Schema::create('opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained()->onDelete('cascade');
            $table->text('opcion');
            $table->boolean('es_correcta')->default(false);
            $table->integer('orden')->default(1);
            $table->timestamps();
        });

        // Tabla de evaluación final del curso - CREAR PRIMERO
        Schema::create('evaluaciones_finales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->integer('min_aprobacion')->default(75);
            $table->timestamps();
        });

        // Tabla de preguntas de evaluación final
        Schema::create('preguntas_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_final_id')->constrained('evaluaciones_finales')->onDelete('cascade');
            $table->text('pregunta');
            $table->integer('orden')->default(1);
            $table->timestamps();
        });

        // Tabla de opciones de evaluación final
        Schema::create('opciones_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_evaluacion_id')->constrained('preguntas_evaluacion')->onDelete('cascade');
            $table->text('opcion');
            $table->boolean('es_correcta')->default(false);
            $table->integer('orden')->default(1);
            $table->timestamps();
        });

        // Tabla de progreso de materiales (PDF leído, video visto)
        Schema::create('progreso_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->boolean('completado')->default(false);
            $table->timestamp('completado_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'material_id']);
        });

        // Tabla de respuestas de cuestionarios de módulos
        Schema::create('respuestas_cuestionario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cuestionario_id')->constrained()->onDelete('cascade');
            $table->foreignId('pregunta_id')->constrained()->onDelete('cascade');
            $table->foreignId('opcion_seleccionada_id')->constrained('opciones')->onDelete('cascade');
            $table->boolean('es_correcta')->default(false);
            $table->timestamps();
        });

        // Tabla de resultados de cuestionarios de módulos
        Schema::create('resultados_cuestionario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('modulo_id')->constrained()->onDelete('cascade');
            $table->foreignId('cuestionario_id')->constrained()->onDelete('cascade');
            $table->integer('nota')->default(0);
            $table->boolean('aprobado')->default(false);
            $table->timestamp('completado_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'modulo_id']);
        });

        // Tabla de resultados de evaluación final
        Schema::create('resultados_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('curso_id')->constrained()->onDelete('cascade');
            $table->integer('nota')->default(0);
            $table->boolean('aprobado')->default(false);
            $table->timestamp('completado_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'curso_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultados_evaluacion');
        Schema::dropIfExists('respuestas_cuestionario');
        Schema::dropIfExists('resultados_cuestionario');
        Schema::dropIfExists('progreso_materiales');
        Schema::dropIfExists('opciones_evaluacion');
        Schema::dropIfExists('preguntas_evaluacion');
        Schema::dropIfExists('evaluaciones_finales');
        Schema::dropIfExists('opciones');
        Schema::dropIfExists('preguntas');
        Schema::dropIfExists('cuestionarios');
    }
}
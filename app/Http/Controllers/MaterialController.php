<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\ProgresoMaterial;

class MaterialController extends Controller
{
    // Actualizar tiempo de video visto
    public function updateVideoProgress(Request $request, $material)
    {
        $request->validate([
            'tiempo_visto' => 'required|integer|min:0',
            'duracion_total' => 'sometimes|integer|min:0'
        ]);

        $material = \App\Models\Material::findOrFail($material);
        
        $tiempoVisto = $request->tiempo_visto;
        $duracionTotal = $request->duracion_total ?? 0;
        
        $progreso = ProgresoMaterial::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'material_id' => $material->id
            ],
            [
                'tiempo_visto' => $tiempoVisto
            ]
        );

        // Calcular 90% de la duración
        $requiredTime = $duracionTotal * 0.9;
        
        // Verificar si alcanzó el 90% de la duración
        if ($duracionTotal > 0 && $tiempoVisto >= $requiredTime) {
            $progreso->video_completado = true;
            $progreso->material_completado = true;
            $progreso->save();
        } elseif ($request->completado) {
            // Si se envió explicitamente como completado
            $progreso->video_completado = true;
            $progreso->material_completado = true;
            $progreso->save();
        }

        return response()->json([
            'success' => true,
            'video_completado' => $progreso->video_completado,
            'tiempo_visto' => $tiempoVisto,
            'duracion_total' => $duracionTotal,
            'required_time' => $requiredTime
        ]);
    }

    // Marcar scroll de PDF completado
    public function updatePdfScroll(Request $request, $material)
    {
        $material = \App\Models\Material::findOrFail($material);
        
        $progreso = ProgresoMaterial::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'material_id' => $material->id
            ],
            [
                'scroll_completado' => true,
                'material_completado' => true
            ]
        );

        return response()->json([
            'success' => true,
            'scroll_completado' => true
        ]);
    }

    // Obtener estado de progreso del material
    public function getProgress($material)
    {
        $material = \App\Models\Material::findOrFail($material);
        
        $progreso = ProgresoMaterial::where('user_id', auth()->id())
            ->where('material_id', $material->id)
            ->first();

        return response()->json([
            'progreso' => $progreso ?? [
                'tiempo_visto' => 0,
                'video_completado' => false,
                'scroll_completado' => false,
                'material_completado' => false
            ]
        ]);
    }
}
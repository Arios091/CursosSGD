<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function verPdf($filename)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $path = 'materiales/' . $filename;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no encontrado: ' . $path);
        }
        
        $file = Storage::disk('public')->get($path);
        $type = Storage::disk('public')->mimeType($path);
        
        return response($file, 200, [
            'Content-Type' => $type,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
    
    public function descargarPdf($filename)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $path = 'materiales/' . $filename;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }
        
        return Storage::disk('public')->download($path, $filename);
    }
}
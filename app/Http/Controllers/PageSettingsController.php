<?php

namespace App\Http\Controllers;

use App\Models\PageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageSettingsController extends Controller
{
    public function index()
    {
        $settings = PageSetting::pluck('value', 'key')->toArray();
        return view('admin.page-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|string|email|max:255',
            'contact_address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'carousel_1' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'carousel_2' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'carousel_3' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
            'carousel_4' => 'nullable|image|mimes:png,jpg,jpeg|max:5120',
        ]);

        $data = $request->except(['_token', '_method', 'logo', 'carousel_1', 'carousel_2', 'carousel_3', 'carousel_4']);

        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                PageSetting::setValue($key, $value);
            }
        }

        // Logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            PageSetting::setValue('logo', $path, 'image');
        }

        // Carrusel
        for ($i = 1; $i <= 4; $i++) {
            $key = 'carousel_' . $i;
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings/carousel', 'public');
                PageSetting::setValue($key, $path, 'image');
            }
        }

        return redirect()->route('admin.page-settings')
            ->with('success', 'Configuración guardada correctamente.');
    }
}
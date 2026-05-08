@extends('layouts.app')

@section('page-title', 'Configuración de la Página')
@section('breadcrumbs')
<span><i class="fas fa-chevron-right"></i></span>
<a href="{{ route('admin.page-settings') }}" style="color: var(--gris-600);">Configuración</a>
@endsection

@section('content')
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 4px;">Configuración de la Página</h2>
    <p style="color: #6b7280; margin: 0;">Personaliza la apariencia y contenido del sitio</p>
</div>

@if(session('success'))
<div class="alert alert-success d-flex align-items-center" style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
    <i class="fas fa-check-circle text-success me-2"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<form method="POST" action="{{ route('admin.page-settings.update') }}" enctype="multipart/form-data">
@csrf

<div class="row g-4">
    {{-- Hero Section --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: #0B5E2E; color: white; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0"><i class="fas fa-home me-2"></i>Sección Hero (Inicio)</h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Título del Hero</label>
                    <input type="text" name="hero_title" class="form-control" value="{{ $settings['hero_title'] ?? 'Sistema de Gestión de Docencia UNAS' }}" maxlength="255">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Subtítulo</label>
                    <textarea name="hero_subtitle" class="form-control" rows="3" maxlength="500">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Colors --}}
    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header" style="background: #0B5E2E; color: white; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Colores</h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Color Primario</label>
                    <div class="input-group">
                        <input type="color" name="primary_color" class="form-control form-control-color" value="{{ $settings['primary_color'] ?? '#0B5E2E' }}" style="width: 60px; padding: 4px;">
                        <input type="text" class="form-control" value="{{ $settings['primary_color'] ?? '#0B5E2E' }}" readonly style="background: #f3f4f6;">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Color Secundario (Dorado)</label>
                    <div class="input-group">
                        <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ $settings['secondary_color'] ?? '#C9A227' }}" style="width: 60px; padding: 4px;">
                        <input type="text" class="form-control" value="{{ $settings['secondary_color'] ?? '#C9A227' }}" readonly style="background: #f3f4f6;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logo --}}
    <div class="col-12 col-md-6">
        <div class="card h-100">
            <div class="card-header" style="background: #0B5E2E; color: white; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo</h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Logo actual</label>
                    <div style="background: #f3f4f6; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 12px;">
                        <img src="{{ isset($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/unasicono.png') }}" style="height: 80px; object-fit: contain;">
                    </div>
                    <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml">
                </div>
            </div>
        </div>
    </div>

    {{-- Carousel --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: #0B5E2E; color: white; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0"><i class="fas fa-images me-2"></i>Carrusel de Fotos (Inicio)</h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="row g-4">
                    @for($i = 1; $i <= 4; $i++)
                    @php $key = 'carousel_' . $i; @endphp
                    <div class="col-md-6 col-lg-3">
                        <div style="background: #f9fafb; border-radius: 12px; padding: 16px; text-align: center; border: 2px dashed #e5e7eb;">
                            <div style="height: 140px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; background: #f3f4f6; border-radius: 8px; overflow: hidden;">
                                @if(isset($settings[$key]) && $settings[$key])
                                    <img src="{{ asset('storage/' . $settings[$key]) }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                @else
                                    <i class="fas fa-image" style="font-size: 40px; color: #d1d5db;"></i>
                                @endif
                            </div>
                            <label class="form-label fw-semibold" style="font-size: 13px;">Foto {{ $i }}</label>
                            <input type="file" name="carousel_{{ $i }}" class="form-control form-control-sm" accept="image/png,image/jpeg">
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- Contacto --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: #0B5E2E; color: white; padding: 16px 20px; border-radius: 12px 12px 0 0;">
                <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Información de Contacto</h5>
            </div>
            <div class="card-body" style="padding: 24px;">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '(062) 562341' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'mesadepartes@unas.edu.pe' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Dirección</label>
                        <input type="text" name="contact_address" class="form-control" value="{{ $settings['contact_address'] ?? 'Carretera Central Km. 1.21, Tingo María' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="text-align: right; margin-top: 24px;">
    <button type="submit" class="btn" style="background: #0B5E2E; color: white; padding: 12px 32px; font-weight: 600; border-radius: 8px;">
        <i class="fas fa-save me-2"></i>Guardar Configuración
    </button>
</div>
</form>

<style>
.card { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.form-label { margin-bottom: 6px; }
</style>
@endsection
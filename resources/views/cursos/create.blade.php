@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Crear Nuevo Curso</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('cursos.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título del Curso *</label>
                                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}">
                                    @error('fecha_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin') }}">
                                    @error('fecha_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Sección para agregar un material al curso -->
                                <div class="border-top pt-4 mt-4">
                                    <h5 class="mb-3">Agregar Material al Curso</h5>

                                    <div class="mb-3">
                                        <label for="material_titulo" class="form-label">Título del Material</label>
                                        <input type="text" name="material_titulo" id="material_titulo" class="form-control @error('material_titulo') is-invalid @enderror" value="{{ old('material_titulo') }}" placeholder="Ej: Introducción al curso">
                                        @error('material_titulo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="material_tipo" class="form-label">Tipo de Material</label>
                                        <select name="material_tipo" id="material_tipo" class="form-control @error('material_tipo') is-invalid @enderror">
                                            <option value="">Selecciona tipo</option>
                                            <option value="pdf" {{ old('material_tipo') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                            <option value="video" {{ old('material_tipo') == 'video' ? 'selected' : '' }}>Video</option>
                                            <option value="cuestionario" {{ old('material_tipo') == 'cuestionario' ? 'selected' : '' }}>Cuestionario</option>
                                        </select>
                                        @error('material_tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="material_url" class="form-label">URL o Path del archivo</label>
                                        <input type="text" name="material_url" id="material_url" class="form-control @error('material_url') is-invalid @enderror" value="{{ old('material_url') }}" placeholder="https://... o /storage/...">
                                        @error('material_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Sección para el módulo inicial -->
                                    <div class="border-top pt-4 mt-4">
                                        <h5 class="mb-3">Módulo Inicial</h5>
                                        <p class="text-muted small mb-3">
                                            Puedes dejar el nombre en blanco → se autogenerará como "Módulo 1".
                                        </p>

                                        <div class="mb-3">
                                            <label for="modulo_titulo" class="form-label">Nombre del Módulo 1 (opcional)</label>
                                            <input type="text" name="modulo_titulo" id="modulo_titulo" class="form-control @error('modulo_titulo') is-invalid @enderror" value="{{ old('modulo_titulo') }}" placeholder="Ej: Introducción al tema">
                                            @error('modulo_titulo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    Crear Curso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
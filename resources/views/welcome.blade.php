@extends('layouts.app')

@section('title', 'Portal de Cursos - UNAS')

@section('content')
<div class="py-5">
    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold text-dark mb-3">
                    Impulsa tu carrera con <span class="text-success">CursosSGD</span>
                </h1>
                <p class="lead text-muted mb-4">
                    La plataforma oficial de educación continua de la Universidad Nacional Agraria de la Selva. 
                    Accede a cursos especializados, gestiona tu progreso y obtén certificaciones con validez académica.
                </p>
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-lg-start">
                    <a href="/login" class="btn btn-success btn-lg px-4 me-sm-3 fw-bold shadow-sm">
                        Iniciar Sesión
                    </a>
                    <a href="/register" class="btn btn-outline-dark btn-lg px-4 shadow-sm">
                        Crear Cuenta
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="https://img.freepik.com/free-vector/online-learning-concept-illustration_114360-4408.jpg" 
                     alt="Educación Online" class="img-fluid rounded-3">
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="feature col text-center">
            <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-success bg-gradient text-white fs-2 mb-3 rounded-circle p-3" style="width: 80px; height: 80px;">
                <i class="bi bi-clock-history"></i>
            </div>
            <h3 class="fs-4 fw-bold">Aprendizaje Asíncrono</h3>
            <p>Estudia a tu propio ritmo, en cualquier momento y desde cualquier lugar del mundo.</p>
        </div>
        <div class="feature col text-center">
            <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-success bg-gradient text-white fs-2 mb-3 rounded-circle p-3" style="width: 80px; height: 80px;">
                <i class="bi bi-shield-check"></i>
            </div>
            <h3 class="fs-4 fw-bold">Certificación Oficial</h3>
            <p>Al finalizar cada curso, obtén un certificado respaldado por la OTI y la UNAS.</p>
        </div>
        <div class="feature col text-center">
            <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-success bg-gradient text-white fs-2 mb-3 rounded-circle p-3" style="width: 80px; height: 80px;">
                <i class="bi bi-laptop"></i>
            </div>
            <h3 class="fs-4 fw-bold">Recursos Digitales</h3>
            <p>Material de alta calidad, videos, lecturas y foros de consulta a tu disposición.</p>
        </div>
    </div>
</div>
@endsection
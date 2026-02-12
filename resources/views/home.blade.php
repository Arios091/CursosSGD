@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            Bienvenido, {{ Auth::user()->name }}!
                        </h4>
                    </div>

                    <div class="card-body">
                        <p class="lead">
                            Estás en el panel principal de CursosSGD.
                        </p>

                        <p>
                            Aquí podrás ver tus cursos, inscribirte en nuevos, o crear cursos si eres docente.
                        </p>

                        <div class="mt-4">
                            <a href="#" class="btn btn-primary btn-lg me-2">
                                Ver Cursos Disponibles
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-lg">
                                Mi Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
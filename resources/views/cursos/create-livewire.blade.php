@extends('layouts.app')

@section('page-title', 'Crear Curso')

@section('content')
    <div class="container mt-5">
        <h1>Crear Curso Completo</h1>

        <!-- Aquí se monta el componente Livewire -->
        <livewire:create-curso />
    </div>
@endsection
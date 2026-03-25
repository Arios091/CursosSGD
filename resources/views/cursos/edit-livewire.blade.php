@extends('layouts.app')

@section('content')
    @livewire('edit-curso', ['cursoId' => $curso->id])
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

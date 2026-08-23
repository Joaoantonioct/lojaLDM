@extends('layouts.app')

@section('title', 'Loja LDM - Área do Cliente')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@stop

@section('main-class', 'auth-main')

@section('content')
    <div class="auth-container">
        @yield('auth-content')
    </div>
@stop 
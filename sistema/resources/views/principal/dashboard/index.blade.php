{{-- Vista hija: hereda la plantilla padre --}}
@extends('template.administrador')

{{-- Textos que la plantilla muestra en sus huecos --}}
@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen de estadísticas')
@section('item_section', 'Inicio')
@section('section', 'Dashboard')
@section('module', 'Principal')

{{-- Contenido del dashboard (vacío por ahora) --}}
@section('content')
@endsection
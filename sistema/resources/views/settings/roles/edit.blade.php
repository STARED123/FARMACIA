@extends('template.administrador')

@section('title', 'Usuarios')
@section('name_section', 'Usuarios')
@section('description', 'Editar usuario')
@section('item_section', 'Editar')
@section('route_section', route('users.index'))
@section('section', 'Usuarios')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            @include('management.users.form')

            <div class="mt-2">
                <button type="submit" class="btn btn-primary text-white">Actualizar usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
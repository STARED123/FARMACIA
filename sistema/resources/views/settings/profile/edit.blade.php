@extends('template.administrador')

@section('title', 'Mi perfil')
@section('name_section', 'Mi perfil')
@section('description', 'Actualizar tus datos personales')
@section('item_section', 'Configuración')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Mi perfil')

@section('content')
@include('partials.alerts')

<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre completo</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="dni" class="form-label">DNI</label>
                    <input type="text" name="dni" id="dni" maxlength="8" class="form-control @error('dni') is-invalid @enderror" value="{{ old('dni', $user->dni) }}" required>
                    @error('dni')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="text" name="phone" id="phone" maxlength="9" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="tuition" class="form-label">Colegiatura</label>
                    <input type="text" name="tuition" id="tuition" maxlength="255" class="form-control @error('tuition') is-invalid @enderror" value="{{ old('tuition', $user->tuition) }}">
                    @error('tuition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="password" class="form-label">Nueva contraseña <small class="text-muted">(opcional)</small></label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary text-white">Guardar cambios</button>
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

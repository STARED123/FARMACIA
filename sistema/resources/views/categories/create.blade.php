@extends('template.administrador')

@section('titulo', 'Nueva categoría')
@section('subtitulo', 'Registrar una categoría de medicamentos')

@section('contenido')
<div class="card border-0 shadow-sm" style="max-width: 640px">
    <form action="{{ route('categories.store') }}" method="POST" class="card-body">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                @class(['form-control', 'is-invalid' => $errors->has('name')])>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción (opcional)</label>
            <textarea id="description" name="description" rows="3" 
                @class(['form-control', 'is-invalid' => $errors->has('description')])>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </form>
</div>
@endsection
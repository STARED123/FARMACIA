@extends('template.administrador')

@section('titulo', 'Editar categoría')
@section('subtitulo', 'Modificar los datos de la categoría')

@section('contenido')
<div class="card border-0 shadow-sm" style="max-width: 640px">
    <form action="{{ route('categories.update', $category) }}" method="POST" class="card-body">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" 
                @class(['form-control', 'is-invalid' => $errors->has('name')])>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descripción (opcional)</label>
            <textarea id="description" name="description" rows="3" 
                @class(['form-control', 'is-invalid' => $errors->has('description')])>{{ old('description', $category->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </form>
</div>
@endsection
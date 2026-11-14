<form action="{{ route('categories.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}">
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea class="form-control" id="description" name="description">{{ old('description', $category->description) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
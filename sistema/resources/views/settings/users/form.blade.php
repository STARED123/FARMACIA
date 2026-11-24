<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label for="rol_id" class="form-label">Rol</label>
        <select name="rol_id" id="rol_id"
                class="form-select @error('rol_id') is-invalid @enderror">
            <option value="">-- Seleccione --</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    @selected((string) old('rol_id', $user->rol_id ?? '') === (string) $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('rol_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="name" class="form-label">Nombre completo</label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name ?? '') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" name="dni" id="dni"
               class="form-control @error('dni') is-invalid @enderror"
               value="{{ old('dni', $user->dni ?? '') }}">
        @error('dni')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="phone" class="form-label">Teléfono</label>
        <input type="text" name="phone" id="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $user->phone ?? '') }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4 mb-3">
        <label for="tuition" class="form-label">Colegiatura / Matrícula</label>
        <input type="text" name="tuition" id="tuition"
               class="form-control @error('tuition') is-invalid @enderror"
               value="{{ old('tuition', $user->tuition ?? '') }}">
        @error('tuition')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6 mb-3">
        <label for="password" class="form-label">
            Contraseña @isset($user)<small class="text-muted">(dejar en blanco para no cambiar)</small>@endisset
        </label>
        <input type="password" name="password" id="password"
               class="form-control @error('password') is-invalid @enderror">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
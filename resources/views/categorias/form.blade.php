<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $categoria->nombre ?? '') }}">
    @error('nombre') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $categoria->slug ?? '') }}">
    @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Categoría</label>
    <select name="categoria_id" class="form-select">
        <option value="">-- Selecciona --</option>
        @foreach($categorias as $cat)
            <option value="{{ $cat->id }}" @selected(old('categoria_id', $producto->categoria_id ?? '') == $cat->id)>
                {{ $cat->nombre }}
            </option>
        @endforeach
    </select>
    @error('categoria_id') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre ?? '') }}">
    @error('nombre') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
    @error('descripcion') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Imagen de portada</label>
    <input type="file" name="imagen" class="form-control" accept="image/*">
    @error('imagen') <div class="text-danger">{{ $message }}</div> @enderror

    @if(isset($producto) && $producto->imagen)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" style="max-height:120px;">
            <div class="form-check mt-1">
                <input type="checkbox" name="eliminar_imagen" value="1" class="form-check-input" id="eliminar_imagen">
                <label class="form-check-label" for="eliminar_imagen">Quitar imagen de portada</label>
            </div>
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Galería de imágenes (opcional, puedes seleccionar varias)</label>
    <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*">
    @error('imagenes.*') <div class="text-danger">{{ $message }}</div> @enderror

    @if(isset($producto) && $producto->imagenes && $producto->imagenes->count())
        <div class="d-flex flex-wrap gap-3 mt-2">
            @foreach($producto->imagenes as $img)
                <div class="text-center">
                    <img src="{{ asset('storage/' . $img->ruta) }}" style="height:80px; width:80px; object-fit:cover; border-radius:4px;"><br>
                    <div class="form-check form-check-inline mt-1">
                        <input type="checkbox" name="eliminar_galeria[]" value="{{ $img->id }}" class="form-check-input" id="del_img_{{ $img->id }}">
                        <label class="form-check-label small" for="del_img_{{ $img->id }}">Eliminar</label>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Precio</label>
        <input type="number" step="0.01" name="precio" class="form-control" value="{{ old('precio', $producto->precio ?? '') }}">
        @error('precio') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Precio oferta (opcional)</label>
        <input type="number" step="0.01" name="precio_oferta" class="form-control" value="{{ old('precio_oferta', $producto->precio_oferta ?? '') }}">
        @error('precio_oferta') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="{{ old('stock', $producto->stock ?? 0) }}">
        @error('stock') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Edad mínima</label>
        <input type="number" name="edad_minima" class="form-control" value="{{ old('edad_minima', $producto->edad_minima ?? '') }}">
        @error('edad_minima') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Piezas</label>
        <input type="number" name="piezas" class="form-control" value="{{ old('piezas', $producto->piezas ?? '') }}">
        @error('piezas') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
</div>

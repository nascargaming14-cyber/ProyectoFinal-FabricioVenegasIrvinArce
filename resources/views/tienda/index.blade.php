<x-app-layout>
    <div class="container py-4">

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Buscar producto..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todos los temas</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" @selected(request('categoria_id') == $cat->id)>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="precio_min" class="form-control" placeholder="Precio min" value="{{ request('precio_min') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="precio_max" class="form-control" placeholder="Precio max" value="{{ request('precio_max') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>

        {{-- Vistos recientemente --}}
        @php
            $idsVistos = json_decode(request()->cookie('productos_vistos', '[]'), true) ?? [];
            $vistos = \App\Models\Producto::whereIn('id', $idsVistos)->get();
        @endphp

        @if($vistos->isNotEmpty())
            <h6>Vistos recientemente</h6>
            <div class="d-flex gap-2 mb-4">
                @foreach($vistos as $p)
                    <a href="{{ route('tienda.show', $p->id) }}" class="badge bg-secondary text-decoration-none p-2">
                        {{ $p->nombre }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="row">
            @foreach($productos as $producto)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6>{{ $producto->nombre }}</h6>
                            <small class="text-muted">{{ $producto->categoria->nombre }} · +{{ $producto->edad_minima }} años</small>
                            <p class="mt-2">
                                @if($producto->precio_oferta)
                                    <span class="text-decoration-line-through text-muted">₡{{ number_format($producto->precio, 0) }}</span>
                                    <span class="text-danger fw-bold">₡{{ number_format($producto->precio_oferta, 0) }}</span>
                                @else
                                    <span class="fw-bold">₡{{ number_format($producto->precio, 0) }}</span>
                                @endif
                            </p>
                            <a href="{{ route('tienda.show', $producto->id) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $productos->links() }}
    </div>
</x-app-layout>

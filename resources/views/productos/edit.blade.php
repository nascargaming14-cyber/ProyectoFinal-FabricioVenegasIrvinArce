<x-app-layout>
    <div class="container py-4">
        <h3>Editar producto</h3>

        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('productos.form')
            <button class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</x-app-layout>

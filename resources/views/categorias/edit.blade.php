<x-app-layout>
    <div class="container py-4">
        <h3>Editar categoría</h3>

        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf @method('PUT')
            @include('categorias.form')
            <button class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</x-app-layout>

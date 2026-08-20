<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_catalogo_es_visible_sin_login(): void
    {
        $response = $this->get(route('tienda.index'));

        $response->assertStatus(200);
    }

    public function test_busqueda_filtra_por_nombre(): void
    {
        Producto::factory()->create(['nombre' => 'LEGO Star Wars Halcón Milenario']);
        Producto::factory()->create(['nombre' => 'LEGO City Camión de Bomberos']);

        $response = $this->get(route('tienda.index', ['q' => 'Star Wars']));

        $response->assertStatus(200);
        $response->assertViewHas('productos', function ($productos) {
            return $productos->total() === 1
                && str_contains($productos->first()->nombre, 'Star Wars');
        });
    }

    public function test_filtro_por_categoria(): void
    {
        $star_wars = Categoria::factory()->create(['nombre' => 'Star Wars']);
        $city = Categoria::factory()->create(['nombre' => 'City']);

        Producto::factory()->count(2)->create(['categoria_id' => $star_wars->id]);
        Producto::factory()->count(3)->create(['categoria_id' => $city->id]);

        $response = $this->get(route('tienda.index', ['categoria_id' => $star_wars->id]));

        $response->assertViewHas('productos', fn ($productos) => $productos->total() === 2);
    }

    public function test_filtro_por_rango_de_precio(): void
    {
        Producto::factory()->create(['precio' => 10000]);
        Producto::factory()->create(['precio' => 50000]);
        Producto::factory()->create(['precio' => 150000]);

        $response = $this->get(route('tienda.index', [
            'precio_min' => 20000,
            'precio_max' => 100000,
        ]));

        $response->assertViewHas('productos', fn ($productos) => $productos->total() === 1);
    }

    public function test_producto_agotado_sigue_visible_en_catalogo(): void
    {
        Producto::factory()->agotado()->create();

        $response = $this->get(route('tienda.index'));

        $response->assertViewHas('productos', fn ($productos) => $productos->total() === 1);
    }
}

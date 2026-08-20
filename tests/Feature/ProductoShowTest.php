<?php

namespace Tests\Feature;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_puede_ver_el_detalle_de_un_producto(): void
    {
        $producto = Producto::factory()->create();

        $response = $this->get(route('tienda.show', $producto));

        $response->assertStatus(200);
        $response->assertViewHas('producto', fn ($p) => $p->id === $producto->id);
    }

    public function test_visitar_un_producto_guarda_cookie_de_vistos_recientemente(): void
    {
        $producto = Producto::factory()->create();

        $response = $this->get(route('tienda.show', $producto));

        $response->assertCookie('productos_vistos');

        $idsVistos = json_decode($response->headers->getCookies()[0]->getValue(), true);
        $this->assertEquals([$producto->id], $idsVistos);
    }

    public function test_el_producto_actual_no_aparece_duplicado_en_vistos_recientemente(): void
    {
        $productos = Producto::factory()->count(3)->create();

        // Simula haber visto ya los 3 productos, en orden
        $cookieInicial = json_encode($productos->pluck('id')->reverse()->values());

        $response = $this->withUnencryptedCookie('productos_vistos', $cookieInicial)
            ->get(route('tienda.show', $productos->first()));

        $idsVistos = json_decode($response->headers->getCookies()[0]->getValue(), true);

        // El producto visitado ahora debe ir de primero y no repetirse
        $this->assertEquals($productos->first()->id, $idsVistos[0]);
        $this->assertCount(3, $idsVistos);
        $this->assertEquals(3, collect($idsVistos)->unique()->count());
    }

    public function test_solo_guarda_los_ultimos_5_productos_vistos(): void
    {
        $productos = Producto::factory()->count(7)->create();
        $vistosPrevios = $productos->take(5)->pluck('id')->toArray();

        $nuevoProducto = $productos->last();

        $response = $this->withUnencryptedCookie('productos_vistos', json_encode($vistosPrevios))
            ->get(route('tienda.show', $nuevoProducto));

        $idsVistos = json_decode($response->headers->getCookies()[0]->getValue(), true);

        $this->assertCount(5, $idsVistos);
        $this->assertEquals($nuevoProducto->id, $idsVistos[0]);
    }
}

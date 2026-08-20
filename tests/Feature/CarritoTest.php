<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CarritoItem;

class CarritoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_no_puede_agregar_al_carrito(): void
    {
        $producto = Producto::factory()->create(['stock' => 5]);

        $response = $this->post(route('carrito.agregar', $producto));

        $response->assertRedirect(route('login'));
    }

    public function test_un_usuario_logueado_puede_agregar_un_producto_al_carrito(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 5, 'precio' => 20000]);

        $response = $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $response->assertRedirect(route('carrito.index'));
        $this->assertDatabaseHas('carrito_items', [
            'producto_id'     => $producto->id,
            'cantidad'        => 1,
            'precio_unitario' => 20000,
        ]);
    }

    public function test_usa_el_precio_de_oferta_si_existe_al_agregar_al_carrito(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create([
            'stock' => 5, 'precio' => 20000, 'precio_oferta' => 15000,
        ]);

        $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $this->assertDatabaseHas('carrito_items', ['precio_unitario' => 15000]);
    }

    public function test_agregar_el_mismo_producto_dos_veces_incrementa_la_cantidad(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 5]);

        $this->actingAs($user)->post(route('carrito.agregar', $producto));
        $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $this->assertDatabaseHas('carrito_items', [
            'producto_id' => $producto->id,
            'cantidad'    => 2,
        ]);
        $this->assertDatabaseCount('carrito_items', 1);
    }

    public function test_no_se_puede_agregar_un_producto_agotado(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->agotado()->create();

        $response = $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('carrito_items', ['producto_id' => $producto->id]);
    }

    public function test_no_se_puede_superar_el_stock_disponible(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 1]);

        // Agrega 1 (llega al límite de stock)
        $this->actingAs($user)->post(route('carrito.agregar', $producto));
        // Intenta agregar una segunda unidad, ya no hay stock
        $response = $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('carrito_items', ['cantidad' => 1]);
    }

    public function test_calcula_correctamente_subtotal_impuesto_y_envio(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 10, 'precio' => 10000]);

        // 2 unidades = subtotal 20000 (menor a 50000, así que aplica envío de 3000)
        $this->actingAs($user)->post(route('carrito.agregar', $producto));
        $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $response = $this->actingAs($user)->get(route('carrito.index'));

        $response->assertViewHas('subtotal', 20000.0);
        $response->assertViewHas('impuesto', 2600.0); // 13% de 20000
        $response->assertViewHas('costoEnvio', 3000);
        $response->assertViewHas('total', 25600.0);
    }

    public function test_el_envio_es_gratis_si_el_subtotal_alcanza_el_minimo(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 10, 'precio' => 60000]);

        $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $response = $this->actingAs($user)->get(route('carrito.index'));

        $response->assertViewHas('costoEnvio', 0);
    }

    public function test_un_usuario_no_puede_actualizar_el_carrito_de_otro_usuario(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 5]);

        $this->actingAs($userA)->post(route('carrito.agregar', $producto));

        $item = CarritoItem::first();

        $response = $this->actingAs($userB)->patch(route('carrito.actualizar', $item), ['cantidad' => 2]);

        $response->assertForbidden();
    }

    public function test_checkout_falla_si_el_carrito_esta_vacio(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('carrito.checkout'), ['metodo_pago' => 'tarjeta']);

        $response->assertRedirect(route('carrito.index'));
        $response->assertSessionHas('error');
    }

    public function test_checkout_crea_el_pedido_y_descuenta_stock(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 10, 'precio' => 30000]);

        $this->actingAs($user)->post(route('carrito.agregar', $producto));
        $this->actingAs($user)->post(route('carrito.agregar', $producto)); // cantidad = 2

        $response = $this->actingAs($user)->post(route('carrito.checkout'), ['metodo_pago' => 'tarjeta']);

        $response->assertRedirect();
        $this->assertDatabaseHas('pedidos', [
            'user_id'     => $user->id,
            'estado'      => 'confirmado',
            'metodo_pago' => 'tarjeta',
        ]);
        $this->assertDatabaseHas('pedido_items', [
            'producto_id' => $producto->id,
            'cantidad'    => 2,
        ]);

        $producto->refresh();
        $this->assertEquals(8, $producto->stock); // 10 - 2

        // El carrito debe quedar vacío después del checkout
        $this->assertDatabaseCount('carrito_items', 0);
    }

    public function test_checkout_falla_si_ya_no_hay_stock_suficiente(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 2]);

        $this->actingAs($user)->post(route('carrito.agregar', $producto));
        $this->actingAs($user)->post(route('carrito.agregar', $producto)); // cantidad = 2

        // Alguien más compró y el stock bajó a 1 antes del checkout
        $producto->update(['stock' => 1]);

        $response = $this->actingAs($user)->post(route('carrito.checkout'), ['metodo_pago' => 'tarjeta']);

        $response->assertRedirect(route('carrito.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('pedidos', 0);
    }

    public function test_checkout_requiere_un_metodo_de_pago_valido(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['stock' => 5]);
        $this->actingAs($user)->post(route('carrito.agregar', $producto));

        $response = $this->actingAs($user)->post(route('carrito.checkout'), ['metodo_pago' => 'bitcoin']);

        $response->assertSessionHasErrors('metodo_pago');
    }
}

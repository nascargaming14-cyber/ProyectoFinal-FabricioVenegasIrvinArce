<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_no_puede_acceder_al_listado_de_productos(): void
    {
        $response = $this->get(route('productos.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_un_cliente_no_admin_no_puede_acceder_al_listado_de_productos(): void
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);

        $response = $this->actingAs($cliente)->get(route('productos.index'));

        $response->assertForbidden(); // 403
    }

    public function test_un_admin_puede_ver_el_listado_de_productos(): void
    {
        $admin = User::factory()->admin()->create();
        Producto::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('productos.index'));

        $response->assertStatus(200);
    }

    public function test_un_admin_puede_crear_un_producto(): void
    {
        $admin = User::factory()->admin()->create();
        $categoria = Categoria::factory()->create();

        $datos = [
            'categoria_id'  => $categoria->id,
            'nombre'        => 'LEGO Millennium Falcon',
            'descripcion'   => 'Set de colección',
            'precio'        => 150000,
            'precio_oferta' => null,
            'edad_minima'   => 16,
            'piezas'        => 7500,
            'stock'         => 10,
        ];

        $response = $this->actingAs($admin)->post(route('productos.store'), $datos);

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', ['nombre' => 'LEGO Millennium Falcon']);
    }

    public function test_no_se_puede_crear_un_producto_con_precio_oferta_mayor_o_igual_al_precio(): void
    {
        $admin = User::factory()->admin()->create();
        $categoria = Categoria::factory()->create();

        $datos = [
            'categoria_id'  => $categoria->id,
            'nombre'        => 'LEGO Producto Inválido',
            'precio'        => 50000,
            'precio_oferta' => 60000, // mayor al precio normal, debe fallar
            'edad_minima'   => 10,
            'piezas'        => 500,
            'stock'         => 5,
        ];

        $response = $this->actingAs($admin)->post(route('productos.store'), $datos);

        $response->assertSessionHasErrors('precio_oferta');
        $this->assertDatabaseMissing('productos', ['nombre' => 'LEGO Producto Inválido']);
    }

    public function test_los_campos_obligatorios_son_requeridos_al_crear(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('productos.store'), []);

        $response->assertSessionHasErrors([
            'categoria_id', 'nombre', 'precio', 'edad_minima', 'piezas', 'stock',
        ]);
    }

    public function test_un_admin_puede_editar_un_producto(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['nombre' => 'Nombre viejo']);

        $datos = [
            'categoria_id'  => $producto->categoria_id,
            'nombre'        => 'Nombre actualizado',
            'descripcion'   => $producto->descripcion,
            'precio'        => $producto->precio,
            'precio_oferta' => null,
            'edad_minima'   => $producto->edad_minima,
            'piezas'        => $producto->piezas,
            'stock'         => $producto->stock,
        ];

        $response = $this->actingAs($admin)->put(route('productos.update', $producto), $datos);

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'nombre' => 'Nombre actualizado']);
    }

    public function test_un_admin_puede_eliminar_un_producto(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create();

        $response = $this->actingAs($admin)->delete(route('productos.destroy', $producto));

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }
}

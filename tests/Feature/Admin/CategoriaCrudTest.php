<?php

namespace Tests\Feature\Admin;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_puede_crear_una_categoria(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categorias.store'), [
            'nombre' => 'Star Wars',
            'slug'   => 'star-wars',
        ]);

        $response->assertRedirect(route('categorias.index'));
        $this->assertDatabaseHas('categorias', ['slug' => 'star-wars']);
    }

    public function test_no_se_puede_repetir_el_slug_de_una_categoria(): void
    {
        $admin = User::factory()->admin()->create();
        Categoria::factory()->create(['slug' => 'city']);

        $response = $this->actingAs($admin)->post(route('categorias.store'), [
            'nombre' => 'City Duplicada',
            'slug'   => 'city',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_un_cliente_no_puede_crear_categorias(): void
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);

        $response = $this->actingAs($cliente)->post(route('categorias.store'), [
            'nombre' => 'Prueba',
            'slug'   => 'prueba',
        ]);

        $response->assertForbidden();
    }

    public function test_un_admin_puede_eliminar_una_categoria(): void
    {
        $admin = User::factory()->admin()->create();
        $categoria = Categoria::factory()->create();

        $response = $this->actingAs($admin)->delete(route('categorias.destroy', $categoria));

        $response->assertRedirect(route('categorias.index'));
        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }
}

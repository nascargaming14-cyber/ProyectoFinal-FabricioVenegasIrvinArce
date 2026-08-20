<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        $precio = $this->faker->randomFloat(2, 10000, 200000);

        return [
            'categoria_id'  => Categoria::factory(),
            'nombre'        => 'LEGO ' . $this->faker->words(3, true),
            'descripcion'   => $this->faker->paragraph(),
            'precio'        => $precio,
            'precio_oferta' => null,
            'edad_minima'   => $this->faker->numberBetween(3, 16),
            'piezas'        => $this->faker->numberBetween(50, 2000),
            'stock'         => $this->faker->numberBetween(0, 100),
        ];
    }

    // Estado: producto con oferta activa
    public function conOferta(): static
    {
        return $this->state(function (array $attributes) {
            $precio = $attributes['precio'] ?? 100000;
            return [
                'precio_oferta' => $precio - 5000,
            ];
        });
    }

    // Estado: producto sin stock
    public function agotado(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}

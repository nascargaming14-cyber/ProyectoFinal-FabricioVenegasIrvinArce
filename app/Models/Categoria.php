<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'slug'];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}

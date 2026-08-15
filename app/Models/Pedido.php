<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
   protected $fillable = [
        'user_id', 'fecha_compra', 'monto_total',
        'impuesto', 'costo_envio', 'numero_seguimiento', 'estado', 'metodo_pago'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }
}

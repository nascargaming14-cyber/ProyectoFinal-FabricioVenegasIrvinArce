<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class EsAdminTest extends TestCase
{
    public function test_un_usuario_con_rol_admin_es_administrador(): void
    {
        $user = new User(['rol' => 'admin']);

        $this->assertTrue($user->esAdmin());
    }

    public function test_un_usuario_con_rol_cliente_no_es_administrador(): void
    {
        $user = new User(['rol' => 'cliente']);

        $this->assertFalse($user->esAdmin());
    }
}

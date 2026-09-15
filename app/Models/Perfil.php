<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfis';

    protected $fillable = [
        'nome',
        'is_admin_geral',
        'permissoes',
    ];

    protected $casts = [
        'is_admin_geral' => 'boolean',
        'permissoes'     => 'array',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'perfil_id');
    }

    /**
     * Verifica se o perfil é Administrador Geral
     */
    public function isAdminGeral(): bool
    {
        if ((bool) $this->is_admin_geral) {
            return true;
        }

        $nome = mb_strtolower(trim($this->nome ?? ''));
        return ($nome === 'administrador geral' || $nome === 'administrador' || $nome === 'admin');
    }

    /**
     * Verifica se este perfil tem permissão em um módulo específico
     */
    public function temPermissao($modulo, $nivel = 'ver'): bool
    {
        if ($this->isAdminGeral()) {
            return true;
        }

        $permissoes = $this->permissoes ?? [];

        if (is_string($permissoes)) {
            $permissoes = json_decode($permissoes, true);
        }

        if (!is_array($permissoes) || !isset($permissoes[$modulo])) {
            return false;
        }

        $perm = $permissoes[$modulo];

        if ($perm === 'admin' || $perm === 1 || $perm === true || $perm === '1') {
            return true;
        }

        if ($nivel === 'ver' && ($perm === 'ver' || $perm === 'admin')) {
            return true;
        }

        return false;
    }
}
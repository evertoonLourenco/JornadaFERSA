<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha_hash',
        'perfil_id',
        'is_admin',
        'matricula',
        'provisorio',
        'data_expiracao',
    ];

    protected $hidden = [
        'senha_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    /**
     * Relacionamento com o Perfil de Acesso
     */
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    /**
     * Verifica se o usuário é Administrador Geral / Super Admin.
     * À prova de falhas: valida campo do usuário, campo do perfil E o nome do perfil.
     */
    public function isSuperAdmin(): bool
    {
        // 1. Checa a coluna is_admin na tabela usuarios
        if ((bool) $this->is_admin) {
            return true;
        }

        // 2. Checa o perfil vinculado
        if ($this->perfil) {
            // Checa flag is_admin_geral ou is_admin na tabela perfis
            if (!empty($this->perfil->is_admin_geral) || !empty($this->perfil->is_admin)) {
                return true;
            }

            // Checa pelo NOME do perfil (Garantia absoluta se a flag no banco estiver 0/null)
            $nomePerfil = mb_strtolower(trim($this->perfil->nome ?? ''));
            if ($nomePerfil === 'administrador geral' || $nomePerfil === 'administrador' || $nomePerfil === 'admin') {
                return true;
            }
        }

        return false;
    }

    /**
     * Checa permissão para determinado módulo e nível
     */
    public function pode($modulo, $nivel = 'ver'): bool
    {
        // Regra 1: Administrador Geral tem acesso TOTAL e ILIMITADO a tudo
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Regra 2: Sem perfil associado = nega acesso
        if (!$this->perfil) {
            return false;
        }

        // Regra 3: Método temPermissao no perfil (se existir)
        if (method_exists($this->perfil, 'temPermissao') && $this->perfil->temPermissao($modulo, $nivel)) {
            return true;
        }

        // Regra 4: Leitura direta das permissões do Perfil
        $permissoes = $this->perfil->permissoes ?? [];

        if (is_string($permissoes)) {
            $permissoes = json_decode($permissoes, true);
        }

        if (is_array($permissoes) && isset($permissoes[$modulo])) {
            $perm = $permissoes[$modulo];

            if ($perm === 'admin' || $perm === 1 || $perm === true || $perm === '1') {
                return true;
            }

            if ($nivel === 'ver' && ($perm === 'ver' || $perm === 'admin')) {
                return true;
            }

            if (is_array($perm) && !empty($perm[$nivel])) {
                return true;
            }
        }

        return false;
    }
}
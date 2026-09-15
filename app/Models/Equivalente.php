<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equivalente extends Model
{
    protected $table = 'equivalentes';
    protected $fillable = ['rolamento_id', 'fabricante', 'codigo_equivalente', 'observacao'];

    public function rolamento(): BelongsTo
    {
        return $this->belongsTo(Rolamento::class, 'rolamento_id');
    }
}
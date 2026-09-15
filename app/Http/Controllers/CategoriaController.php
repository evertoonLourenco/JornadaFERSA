<?php

namespace App\Http\Controllers;

use App\Models\CategoriaDocumento;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias_documento,nome',
        ], [
            'nome.unique' => 'Já existe uma categoria cadastrada com esse nome.',
        ]);

        CategoriaDocumento::create([
            'nome' => $request->nome,
        ]);

        return redirect()->back()->with('success', 'Categoria criada com sucesso!');
    }
}
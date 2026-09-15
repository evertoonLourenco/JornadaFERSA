<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Apenas o Administrador Geral pode gerenciar perfis.');
        }

        $perfis = Perfil::withCount('usuarios')->get();
        $modulos = ['documentos', 'usuarios', 'vendas', 'videos', 'cursos'];

        return view('perfis.index', compact('perfis', 'modulos'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'nome'       => 'required|string|max:255',
            'permissoes' => 'nullable|array',
        ]);

        Perfil::create([
            'nome'           => $request->nome,
            'is_admin_geral' => false,
            'permissoes'     => $request->permissoes ?? [],
        ]);

        return redirect()->back()->with('success', 'Perfil criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $perfil = Perfil::findOrFail($id);

        if ($perfil->is_admin_geral) {
            return redirect()->back()->withErrors(['O perfil Administrador Geral não pode ser alterado.']);
        }

        $perfil->update([
            'nome'       => $request->nome,
            'permissoes' => $request->permissoes ?? [],
        ]);

        return redirect()->back()->with('success', 'Perfil atualizado!');
    }
}
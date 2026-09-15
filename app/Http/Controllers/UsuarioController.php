<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        // 1. Valida se o usuário pode VER o módulo de usuários
        if (!auth()->user()->pode('usuarios', 'ver')) {
            abort(403, 'Você não tem permissão para visualizar este módulo.');
        }

        $usuarios = Usuario::with('perfil')->orderBy('created_at', 'desc')->get();
        $perfis = Perfil::all();

        return view('usuarios.index', compact('usuarios', 'perfis'));
    }

    public function store(Request $request)
    {
        // 2. Valida se o usuário tem permissão de ADMIN (cadastrar/alterar) no módulo
        if (!auth()->user()->pode('usuarios', 'admin')) {
            abort(403, 'Você não tem permissão para cadastrar usuários.');
        }

        $request->validate([
            'nome'           => 'required|string|max:255',
            'email'          => 'required|email|unique:usuarios,email',
            'senha'          => 'required|string|min:6',
            'perfil_id'      => 'required|exists:perfis,id',
            'matricula'      => 'nullable|string|max:50',
            'is_provisorio'  => 'nullable|boolean',
            'data_expiracao' => 'nullable|required_if:is_provisorio,1|date',
        ]);

        $perfil = Perfil::find($request->perfil_id);

        Usuario::create([
            'nome'           => $request->nome,
            'email'          => $request->email,
            'senha_hash'     => Hash::make($request->senha),
            'perfil_id'      => $request->perfil_id,
            'is_admin'       => $perfil?->is_admin_geral ? 1 : 0, // Define 0 se for usuário comum
            'matricula'      => $request->matricula,
            'provisorio'     => $request->boolean('is_provisorio'),
            'data_expiracao' => $request->boolean('is_provisorio') ? $request->data_expiracao : null,
        ]);

        return redirect()->back()->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->pode('usuarios', 'admin')) {
            abort(403, 'Você não tem permissão para alterar usuários.');
        }

        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nome'           => 'required|string|max:255',
            'email'          => 'required|email|unique:usuarios,email,' . $usuario->id,
            'perfil_id'      => 'required|exists:perfis,id',
            'matricula'      => 'nullable|string|max:50',
            'is_provisorio'  => 'nullable|boolean',
            'data_expiracao' => 'nullable|required_if:is_provisorio,1|date',
        ]);

        $perfil = Perfil::find($request->perfil_id);

        $dados = [
            'nome'           => $request->nome,
            'email'          => $request->email,
            'perfil_id'      => $request->perfil_id,
            'is_admin'       => $perfil?->is_admin_geral ? 1 : 0,
            'matricula'      => $request->matricula,
            'provisorio'     => $request->boolean('is_provisorio'),
            'data_expiracao' => $request->boolean('is_provisorio') ? $request->data_expiracao : null,
        ];

        if ($request->filled('senha')) {
            $dados['senha_hash'] = Hash::make($request->senha);
        }

        $usuario->update($dados);

        return redirect()->back()->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        if (!auth()->user()->pode('usuarios', 'admin')) {
            abort(403, 'Você não tem permissão para excluir usuários.');
        }

        if (auth()->id() == $id) {
            return redirect()->back()->withErrors(['Você não pode excluir o seu próprio usuário logado.']);
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return redirect()->back()->with('success', 'Usuário excluído com sucesso!');
    }
}
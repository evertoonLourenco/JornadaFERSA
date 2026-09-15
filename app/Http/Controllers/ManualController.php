<?php

namespace App\Http\Controllers;

use App\Models\Manual;
use App\Models\Perfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManualController extends Controller
{
    public function index(Request $request)
    {
        $query = Manual::with('perfil');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('descricao', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $manuais = $query->latest()->get();
        
        // Busca os perfis para preencher o select do modal
        $perfis = class_exists(Perfil::class) ? Perfil::all() : collect();

        return view('manuais.index', compact('manuais', 'perfis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|string',
            'perfil_id' => 'nullable|exists:perfis,id',
            'descricao' => 'nullable|string',
            'arquivo' => 'required|file|mimes:pdf|max:25600', // Máx 25MB
        ], [
            'titulo.required' => 'O título do manual é obrigatório.',
            'categoria.required' => 'A categoria é obrigatória.',
            'arquivo.required' => 'O arquivo PDF é obrigatório.',
            'arquivo.mimes' => 'O arquivo precisa ser no formato PDF.',
            'arquivo.max' => 'O arquivo não pode ter mais que 25MB.',
        ]);

        // Salva o arquivo PDF no storage public
        $path = $request->file('arquivo')->store('manuais', 'public');

        Manual::create([
            'titulo' => $request->titulo,
            'categoria' => $request->categoria,
            'perfil_id' => $request->perfil_id,
            'descricao' => $request->descricao,
            'arquivo_path' => $path,
            'tamanho_bytes' => $request->file('arquivo')->getSize(),
        ]);

        return redirect()->route('manuais.index')->with('success', 'Manual cadastrado com sucesso!');
    }

    public function update(Request $request, Manual $manual)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|string',
            'perfil_id' => 'nullable|exists:perfis,id',
            'descricao' => 'nullable|string',
            'arquivo' => 'nullable|file|mimes:pdf|max:25600',
        ], [
            'titulo.required' => 'O título do manual é obrigatório.',
            'categoria.required' => 'A categoria é obrigatória.',
            'arquivo.mimes' => 'O arquivo precisa ser no formato PDF.',
            'arquivo.max' => 'O arquivo não pode ter mais que 25MB.',
        ]);

        $data = [
            'titulo' => $request->titulo,
            'categoria' => $request->categoria,
            'perfil_id' => $request->perfil_id,
            'descricao' => $request->descricao,
        ];

        // Se enviou um novo arquivo PDF, substitui o antigo
        if ($request->hasFile('arquivo')) {
            if ($manual->arquivo_path && Storage::disk('public')->exists($manual->arquivo_path)) {
                Storage::disk('public')->delete($manual->arquivo_path);
            }

            $data['arquivo_path'] = $request->file('arquivo')->store('manuais', 'public');
            $data['tamanho_bytes'] = $request->file('arquivo')->getSize();
        }

        $manual->update($data);

        return redirect()->route('manuais.index')->with('success', 'Manual atualizado com sucesso!');
    }

    public function destroy(Manual $manual)
    {
        // Deleta o arquivo PDF do disco
        if ($manual->arquivo_path && Storage::disk('public')->exists($manual->arquivo_path)) {
            Storage::disk('public')->delete($manual->arquivo_path);
        }

        $manual->delete();

        return redirect()->route('manuais.index')->with('success', 'Manual removido com sucesso!');
    }
}
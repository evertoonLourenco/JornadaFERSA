<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Perfil;
use App\Models\Usuario;
use App\Models\TipoEquipamento;
use App\Models\Fabricante;
use App\Models\Modelo;
use App\Models\Rolamento;
use App\Models\Equivalente;
use App\Models\Aplicacao;
use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Models\DocumentoVersao;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Perfis e Usuário
        $perfilAdmin = Perfil::create(['nome' => 'Administrador', 'descricao' => 'Acesso total']);
        Perfil::create(['nome' => 'Técnico', 'descricao' => 'Edição técnica']);
        Perfil::create(['nome' => 'Consulta', 'descricao' => 'Apenas leitura']);

        $usuario = Usuario::create([
            'nome' => 'Suporte FERSA',
            'email' => 'admin@fersa.com',
            'senha_hash' => Hash::make('senha123'),
            'perfil_id' => $perfilAdmin->id,
            'ativo' => true
        ]);

        // 2. Tipos de Equipamento e Fabricantes
        $tipoCaminhao = TipoEquipamento::create(['nome' => 'Caminhão', 'descricao' => 'Linha pesada rodoviária']);
        $tipoAgricola = TipoEquipamento::create(['nome' => 'Máquina Agrícola', 'descricao' => 'Tratores e colheitadeiras']);

        $volvo = Fabricante::create(['nome' => 'Volvo', 'pais' => 'Suécia']);
        $johnDeere = Fabricante::create(['nome' => 'John Deere', 'pais' => 'EUA']);

        // 3. Modelos
        $modeloFH = Modelo::create([
            'fabricante_id' => $volvo->id,
            'tipo_id' => $tipoCaminhao->id,
            'modelo' => 'FH 540',
            'ano_inicio' => 2015,
            'ano_fim' => 2024,
            'descricao' => 'Caminhão pesado 6x4'
        ]);

        Modelo::create([
            'fabricante_id' => $johnDeere->id,
            'tipo_id' => $tipoAgricola->id,
            'modelo' => '5075E',
            'ano_inicio' => 2018,
            'descricao' => 'Trator utilitário'
        ]);

        // 4. Rolamentos FERSA e Equivalentes Concorrentes
        $rolamentoFersa = Rolamento::create([
            'codigo' => 'FERSA 32218',
            'descricao' => 'Rolamento de rolos cônicos',
            'tipo' => 'Cônico',
            'diametro_interno' => 90.000,
            'diametro_externo' => 160.000,
            'largura' => 42.500
        ]);

        Equivalente::create([
            'rolamento_id' => $rolamentoFersa->id,
            'fabricante' => 'SKF',
            'codigo_equivalente' => '32218 J2/Q',
            'observacao' => 'Especificação idêntica'
        ]);

        Equivalente::create([
            'rolamento_id' => $rolamentoFersa->id,
            'fabricante' => 'FAG',
            'codigo_equivalente' => '32218-A',
            'observacao' => 'Substituição direta'
        ]);

        // 5. Aplicação e Vínculo N:N com Rolamento
        $aplicacaoCubo = Aplicacao::create([
            'modelo_id' => $modeloFH->id,
            'componente' => 'Cubo da Roda',
            'posicao' => 'Dianteiro',
            'lado' => 'Ambos',
            'descricao' => 'Montagem do conjunto de rolamentos do cubo dianteiro'
        ]);

        $aplicacaoCubo->rolamentos()->attach($rolamentoFersa->id, [
            'quantidade' => 2,
            'observacao' => 'Utiliza 2 unidades por roda'
        ]);

        // 6. Categoria de Documento e Documento com Versão
        $catManual = CategoriaDocumento::create(['nome' => 'Manual de Aplicação', 'descricao' => 'Guias e especificações']);

        $documento = Documento::create([
            'categoria_id' => $catManual->id,
            'titulo' => 'Manual de Instalação Cubo Dianteiro Volvo FH',
            'descricao' => 'Procedimento de montagem e torque para rolamentos FERSA',
            'nome_arquivo' => 'manual-volvo-fh-v2.pdf',
            'caminho_arquivo' => 'documentos/manual-volvo-fh-v2.pdf',
            'versao_atual' => '2.0',
            'data_documento' => '2026-01-15',
            'usuario_id' => $usuario->id,
            'ativo' => true
        ]);

        // Vínculos N:N do Documento
        $documento->modelos()->attach($modeloFH->id);
        $documento->aplicacoes()->attach($aplicacaoCubo->id);

        // Histórico de Versões
        DocumentoVersao::create([
            'documento_id' => $documento->id,
            'versao' => '1.0',
            'nome_arquivo' => 'manual-volvo-fh-v1.pdf',
            'caminho_arquivo' => 'documentos/manual-volvo-fh-v1.pdf',
            'usuario_id' => $usuario->id,
            'observacao' => 'Versão inicial lançada em 2024',
            'atual' => false
        ]);

        DocumentoVersao::create([
            'documento_id' => $documento->id,
            'versao' => '2.0',
            'nome_arquivo' => 'manual-volvo-fh-v2.pdf',
            'caminho_arquivo' => 'documentos/manual-volvo-fh-v2.pdf',
            'usuario_id' => $usuario->id,
            'observacao' => 'Atualização de torques de aperto',
            'atual' => true
        ]);
    }
}
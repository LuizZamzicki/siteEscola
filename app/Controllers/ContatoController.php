<?php

namespace App\Controllers;

use App\Models\ContatoService;

class ContatoController
{
    /**
     * Processa o envio do formulário de contato.
     */
    public function enviar()
    {
        header('Content-Type: application/json');
        date_default_timezone_set('America/Sao_Paulo');

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
            exit;
        }

        $config = require BASE_PATH . 'Config/config.php';
        $dados = [
            'nome' => htmlspecialchars(trim($_POST['nome'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'telefone' => htmlspecialchars(trim($_POST['telefone'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'assunto' => htmlspecialchars(trim($_POST['assunto'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'turmaInteresse' => htmlspecialchars(trim($_POST['turmaInteresse'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'mensagem' => htmlspecialchars(trim($_POST['mensagem'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];

        $contatoService = new ContatoService($config);
        $validacao = $contatoService->validarDados($dados);
        if (!$validacao['success']) {
            echo json_encode($validacao);
            exit;
        }

        $turmaExibicao = $this->mapearTurma($dados['turmaInteresse']);
        $resultado = $contatoService->enviarEmail($dados, $turmaExibicao);
        echo json_encode($resultado);
    }

    /**
     * Mapeia o código da turma para o nome de exibição.
     */
    private function mapearTurma($codigoTurma)
    {
        $turmasMapeadas = [
            '6_ano_fund' => '6º Ano do Ensino Fundamental',
            '7_ano_fund' => '7º Ano do Ensino Fundamental',
            '8_ano_fund' => '8º Ano do Ensino Fundamental',
            '9_ano_fund' => '9º Ano do Ensino Fundamental',
            '1_ano_medio' => '1º Ano do Ensino Médio (Integral)',
            '2_ano_medio' => '2º Ano do Ensino Médio (Integral)',
            '3_ano_medio' => '3º Ano do Ensino Médio (Integral)',
            '1_ano_medio_noturno' => '1º Ano do Ensino Médio (Noturno)',
            '2_ano_medio_noturno' => '2º Ano do Ensino Médio (Noturno)',
            '3_ano_medio_noturno' => '3º Ano do Ensino Médio (Noturno)',
            'indiferente_turma' => 'Não tenho certeza / Outro',
        ];

        return $turmasMapeadas[$codigoTurma] ?? $codigoTurma;
    }
    }


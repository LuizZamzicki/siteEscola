<?php

namespace App\Models;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContatoService
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function validarDados($dados): array
    {
        $camposObrigatorios = ['nome', 'email', 'telefone', 'assunto', 'turmaInteresse', 'mensagem'];
        foreach ($camposObrigatorios as $campo)
        {
            if (empty($dados[$campo]))
            {
                return ['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios.'];
            }
        }
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL))
        {
            return ['success' => false, 'message' => 'O endereço de e-mail informado é inválido.'];
        }
        return ['success' => true];
    }

    public function enviarEmail($dados, $turmaExibicao): array
    {
        $mail = new PHPMailer(true);
        try
        {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['EMAIL_USERNAME'];
            $mail->Password = $this->config['EMAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($this->config['EMAIL_USERNAME'], 'Site Escola Maffei');
            $mail->addAddress($this->config['EMAIL_DESTINO']);
            $mail->addReplyTo($dados['email'], $dados['nome']);

            $mail->isHTML(true);
            $mail->Subject = 'Contato do site: ' . $dados['assunto'];
            $mail->Body = '<b>Nome:</b> ' . $dados['nome'] . '<br>' .
                '<b>E-mail:</b> ' . $dados['email'] . '<br>' .
                '<b>Telefone:</b> ' . $dados['telefone'] . '<br>' .
                '<b>Turma de Interesse:</b> ' . $turmaExibicao . '<br>' .
                '<b>Mensagem:</b><br>' . nl2br($dados['mensagem']);

            $mail->send();
            return ['success' => true, 'message' => 'Mensagem enviada com sucesso!'];
        }
        catch (Exception $e)
        {
            return ['success' => false, 'message' => 'Erro ao enviar mensagem: ' . $mail->ErrorInfo];
        }
    }
}

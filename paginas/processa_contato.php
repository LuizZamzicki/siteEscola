<?php

// Incluir os arquivos do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

// VERIFICA SE A REQUISIÇÃO É POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. COLETAR E SANITIZAR OS DADOS
    $nome           = trim(htmlspecialchars($_POST['nome'] ?? ''));
    $email          = trim(htmlspecialchars($_POST['email'] ?? ''));
    $telefone       = trim(htmlspecialchars($_POST['telefone'] ?? ''));
    $assunto        = trim(htmlspecialchars($_POST['assunto'] ?? ''));
    $turmaInteresse = trim(htmlspecialchars($_POST['turmaInteresse'] ?? ''));
    $mensagem       = trim(htmlspecialchars($_POST['mensagem'] ?? ''));

    // --- NOVO: Mapeamento dos valores das turmas (ATUALIZADO COM AS SUAS OPÇÕES) ---
    $turmasMapeadas = [
        // Ensino Fundamental II
        '6_ano_fund' => '6º Ano do Ensino Fundamental',
        '7_ano_fund' => '7º Ano do Ensino Fundamental',
        '8_ano_fund' => '8º Ano do Ensino Fundamental',
        '9_ano_fund' => '9º Ano do Ensino Fundamental',

        // Ensino Médio Integral
        '1_ano_medio' => '1º Ano do Ensino Médio (Integral)',
        '2_ano_medio' => '2º Ano do Ensino Médio (Integral)',
        '3_ano_medio' => '3º Ano do Ensino Médio (Integral)',

        // Ensino Médio Noturno
        '1_ano_medio_noturno' => '1º Ano do Ensino Médio (Noturno)',
        '2_ano_medio_noturno' => '2º Ano do Ensino Médio (Noturno)',
        '3_ano_medio_noturno' => '3º Ano do Ensino Médio (Noturno)',

        // Outras opções
        'indiferente_turma' => 'Não tenho certeza / Outro',
    ];

    // Verifica se a turma recebida existe no mapeamento, senão usa o valor original
    $turmaExibicao = $turmasMapeadas[$turmaInteresse] ?? $turmaInteresse;
    // --- FIM DO NOVO ---


    // 2. VALIDAR OS DADOS
    if (empty($nome) || empty($email) || empty($telefone) || empty($assunto) || empty($turmaInteresse) || empty($mensagem)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
        exit;
    }

    // Validação de E-mail básica de formato
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'O endereço de e-mail informado é inválido.']);
        exit;
    }

    // Validação de domínio
    if (!preg_match("/.+\@.+\..+/", $email)) {
        echo json_encode(['success' => false, 'message' => 'O endereço de e-mail informado é inválido. Ele deve conter um domínio válido (ex: exemplo@dominio.com).']);
        exit;
    }

    // 3. INICIALIZAR E USAR O PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do Servidor SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'maffei.rosa.colegio@gmail.com';
        $mail->Password   = 'axkpazaslnrhdlqg'; // Sua senha de aplicativo
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Remetente
        $mail->setFrom('maffei.rosa.colegio@gmail.com', 'Site Escola Maffei'); 

        // Destinatário
        $mail->addAddress('luiz.zamzicki@gmail.com');

        // Endereço de Resposta
        $mail->addReplyTo($email, $nome); 

        // Conteúdo do E-mail
        $mail->isHTML(true);
        $mail->Subject = 'Nova Mensagem do Site Maffei: ' . $assunto;
        
        // --- Corpo do E-mail HTML (usando $turmaExibicao) ---
        $mail->Body    = "
        <!DOCTYPE html>
        <html lang='pt-br'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Nova Mensagem do Formulário de Contato - Maffei</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333333;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e0e0e0;
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 1px solid #eeeeee;
                    margin-bottom: 20px;
                }
                .header img {
                    max-width: 150px;
                    height: auto;
                    margin-bottom: 10px;
                }
                .header h1 {
                    color: #004d40;
                    font-size: 24px;
                    margin: 0;
                }
                .content p {
                    margin-bottom: 10px;
                }
                .content strong {
                    color: #004d40;
                }
                .data-field {
                    background-color: #f9f9f9;
                    border-left: 4px solid #4CAF50;
                    margin-bottom: 10px;
                    padding: 10px 15px;
                    border-radius: 4px;
                }
                .data-field strong {
                    display: block;
                    margin-bottom: 5px;
                }
                .message-box {
                    background-color: #ffffff;
                    border: 1px solid #cccccc;
                    padding: 15px;
                    margin-top: 20px;
                    border-radius: 5px;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #eeeeee;
                    font-size: 12px;
                    color: #777777;
                }
                .footer a {
                    color: #004d40;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Nova Mensagem do Formulário de Contato</h1>
                </div>
                <div class='content'>
                    <p>Olá,</p>
                    <p>Você recebeu uma nova mensagem através do formulário de contato do site da Escola Maffei.</p>
                    
                    <div class='data-field'>
                        <strong>Nome:</strong> {$nome}
                    </div>
                    <div class='data-field'>
                        <strong>E-mail:</strong> {$email}
                    </div>
                    <div class='data-field'>
                        <strong>Telefone:</strong> {$telefone}
                    </div>
                    <div class='data-field'>
                        <strong>Assunto:</strong> {$assunto}
                    </div>
                    <div class='data-field'>
                        <strong>Turma de Interesse:</strong> {$turmaExibicao} </div>

                    <p><strong>Mensagem:</strong></p>
                    <div class='message-box'>
                        <p>{$mensagem}</p>
                    </div>

                    <p>Atenciosamente,<br>Sua Equipe de Desenvolvimento Maffei</p>
                </div>
                <div class='footer'>
                    <p>Este é um e-mail automático. Por favor, não responda diretamente a este e-mail.</p>
                    <p>&copy; " . date('Y') . " Escola Maffei. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        // --- FIM DO CORPO DO E-MAIL HTML ---

        // --- AltBody (texto puro) também usando $turmaExibicao ---
        $mail->AltBody = "Nova Mensagem do Site Maffei:\n\n" .
                         "Nome: {$nome}\n" .
                         "Email: {$email}\n" .
                         "Telefone: {$telefone}\n" .
                         "Assunto: {$assunto}\n" .
                         "Turma de Interesse: {$turmaExibicao}\n\n" . // AQUI USAMOS O VALOR MAPEADO
                         "Mensagem:\n{$mensagem}\n\n" .
                         "Este é um e-mail automático. Por favor, não responda diretamente a este e-mail.";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.']);

    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail via PHPMailer: {$mail->ErrorInfo}", 0); 
        echo json_encode(['success' => false, 'message' => 'Não foi possível enviar a mensagem no momento. Erro do servidor de e-mail.']);
    }

} else { 
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
exit;
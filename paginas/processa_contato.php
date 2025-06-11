<?php
// Ativar erros para depuração (e ver o log). REMOVER EM PRODUÇÃO.
error_reporting(E_ALL);
ini_set('display_errors', 0); // Mantém oculto na tela, mas loga
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/apache/logs/php_errors.log'); // <-- VERIFIQUE/AJUSTE O CAMINHO!

// Incluir os arquivos do PHPMailer
// ATENÇÃO: Ajuste os caminhos conforme onde você descompactou o PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

// VERIFICA SE A REQUISIÇÃO É POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. COLETAR E SANITIZAR OS DADOS (PRIMEIRO PASSO SEMPRE)
    $nome           = trim(htmlspecialchars($_POST['nome'] ?? ''));
    $email          = trim(htmlspecialchars($_POST['email'] ?? ''));
    $telefone       = trim(htmlspecialchars($_POST['telefone'] ?? ''));
    $assunto        = trim(htmlspecialchars($_POST['assunto'] ?? ''));
    $turmaInteresse = trim(htmlspecialchars($_POST['turmaInteresse'] ?? ''));
    $mensagem       = trim(htmlspecialchars($_POST['mensagem'] ?? ''));

     // 2. VALIDAR OS DADOS (ANTES DE TENTAR USAR NO EMAIL)
    if (empty($nome) || empty($email) || empty($telefone) || empty($assunto) || empty($turmaInteresse) || empty($mensagem)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios.']);
        exit;
    }

    // Validação de E-mail
    // Primeiro, uma validação básica de formato
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'O endereço de e-mail informado é inválido.']);
        exit;
    }

    // NOVO: Validação adicional para garantir que o e-mail tenha um ponto no domínio (ex: .com, .br)
    // Isso evita emails como "teste@dominio" sem o TLD
    if (!preg_match("/.+\@.+\..+/", $email)) {
        echo json_encode(['success' => false, 'message' => 'O endereço de e-mail informado é inválido. Ele deve conter um domínio válido (ex: exemplo@dominio.com).']);
        exit;
    }

    // 3. AGORA QUE OS DADOS ESTÃO SEGUROS E VÁLIDOS, INICIALIZAR E USAR O PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do Servidor SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'maffei.rosa.colegio@gmail.com';
        $mail->Password   = 'axkpazaslnrhdlqg'; // Sua senha de aplicativo
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use ENCRYPTION_STARTTLS para porta 587
        $mail->Port       = 587;

        // Remetente (O que o receptor vê como remetente - DEVE SER O MESMO DO USERNAME PARA O GMAIL)
        $mail->setFrom('maffei.rosa.colegio@gmail.com', 'Site Escola'); // Seu email Gmail, Nome que aparece

        // Destinatário (Para onde o e-mail será enviado - SEU E-MAIL DE RECEBIMENTO)
        $mail->addAddress('luiz.zamzicki@gmail.com');

        // Endereço de Resposta (Quando você clicar em "Responder", vai para o e-mail do usuário)
        $mail->addReplyTo($email, $nome); // Usar as variáveis $email e $nome coletadas acima

        // Conteúdo do E-mail
        $mail->isHTML(true);
        // Use as variáveis $assunto, $nome, $email, $telefone, etc. coletadas e sanitizadas
        $mail->Subject = 'Mensagem do Site: ' . $assunto;
        $mail->Body    = "
            <strong>Nome:</strong> {$nome}<br>
            <strong>Email:</strong> {$email}<br>
            <strong>Telefone:</strong> {$telefone}<br>
            <strong>Turma de Interesse:</strong> {$turmaInteresse}<br><br>
            <strong>Mensagem:</strong><br>{$mensagem}
        ";
        $mail->AltBody = "Nova Mensagem do Site\n\nNome: {$nome}\nEmail: {$email}\nTelefone: {$telefone}\nTurma de Interesse: {$turmaInteresse}\n\nMensagem:\n{$mensagem}";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.']);

    } catch (Exception $e) {
        // Captura qualquer erro durante o envio do e-mail
        error_log("Erro ao enviar e-mail via PHPMailer: {$mail->ErrorInfo}", 0); // Loga o erro
        echo json_encode(['success' => false, 'message' => 'Não foi possível enviar a mensagem no momento. Erro do servidor de e-mail.']);
    }

} else { // Se a requisição NÃO for POST
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
exit;
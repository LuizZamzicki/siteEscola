<?php

FuncoesUtils::adicionarCss('Assets/bootstrap.min.css');
FuncoesUtils::adicionarCss('Features_Auth/login.css');
FuncoesUtils::adicionarCss('Widgets/Botoes/botoes.css');


$error_message = $_GET['error'] ?? null;
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <img src="imagens/logo.png" alt="Logo Maffei" class="login-logo">
            <h2 class="login-title">Acesso Restrito</h2>
            <p class="login-subtitle">Faça login para acessar a área do aluno ou o painel administrativo.</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="login-body">
            <a href="?param=google-auth" class="btn-google-login">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo">
                <span>Entrar com Google</span>
            </a>

            <div class="login-divider">
                <span>ou</span>
            </div>

            <div class="login-form-placeholder">
                <p>Login com usuário e senha estará disponível em breve.</p>
            </div>
        </div>

        <div class="login-footer">
            <a href="?param=home">Voltar para o site</a>
        </div>
    </div>
</div>
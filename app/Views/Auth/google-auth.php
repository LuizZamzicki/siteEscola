<?php
require_once BASE_PATH . 'core/services/config.php';
require_once BASE_PATH . 'vendor/autoload.php';

// Inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE)
{
    session_start();
}

$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

// Define os escopos necessários para obter email, nome e foto do perfil.
$client->addScope("email");
$client->addScope("profile");

// Cria a URL de autenticação e redireciona o usuário
$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
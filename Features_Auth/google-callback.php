<?php
require_once BASE_PATH . 'core/services/config.php';
require_once BASE_PATH . 'core/services/UsuarioService.php';
require_once BASE_PATH . 'vendor/autoload.php';

// Inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE)
{
    session_start();
}

// A solução recomendada para o erro `count(null)` é executar `composer update`.
// Como alternativa, configurar manualmente o cliente HTTP com um caminho para o
// bundle de certificados SSL pode contornar o problema no XAMPP com PHP 8+.
$guzzleClient = new \GuzzleHttp\Client([
    // Caminho para o arquivo cacert.pem que você baixou.
    'verify' => BASE_PATH . 'core/cacert.pem',
]);

$client = new Google\Client();
$client->setHttpClient($guzzleClient);
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code']) || empty($_GET['code']))
{
    header('Location: ?param=login&error=' . urlencode('Código de autorização não recebido.'));
    exit();
}

try
{
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!is_array($token) || isset($token['error']))
    {
        throw new Exception($token['error_description'] ?? 'Token inválido ou expirado. Tente novamente.');
    }

    $google_oauth = new Google\Service\Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $email = $google_account_info->email;
    $avatar = $google_account_info->picture;

    $usuarioService = new UsuarioService();
    $usuarioDTO = $usuarioService->buscarUsuarioPorEmail($email);

    if (!$usuarioDTO)
    {
        throw new Exception('Seu e-mail não está cadastrado no sistema.');
    }

    // Se a foto do Google for diferente da armazenada, atualiza no banco.
    if ($avatar && $usuarioDTO->urlImgPerfil !== $avatar)
    {
        $usuarioService->updateAvatarUrl($usuarioDTO->id, $avatar);
        $usuarioDTO->urlImgPerfil = $avatar; // Atualiza o DTO para a sessão atual
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $usuarioDTO->id;
    $_SESSION['user_name'] = $usuarioDTO->nome;
    $_SESSION['user_email'] = $usuarioDTO->email;
    $_SESSION['user_role'] = $usuarioDTO->tipo;
    $_SESSION['user_avatar'] = $usuarioDTO->urlImgPerfil ?? $avatar;
    // Adiciona os dados da turma à sessão para uso em outras páginas
    $_SESSION['user_turma'] = $usuarioDTO->nome_turma ?? null;
    $_SESSION['user_turma_id'] = $usuarioDTO->id_turma ?? null;

    $redirect = $usuarioDTO->tipo === 'Aluno' ? '?param=area_aluno' : '?param=dashboard';
    header('Location: ' . $redirect);
    exit();
}
catch (Exception $e)
{
    session_unset();
    session_destroy();
    header('Location: ?param=login&error=' . urlencode('Ocorreu um erro: ' . $e->getMessage()));
    exit();
}

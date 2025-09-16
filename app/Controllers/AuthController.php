<?php

namespace App\Controllers;

use Google\Client;
use Google\Service\Oauth2;
use App\Models\UsuarioService;

class AuthController
{
    public function loginCallback()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

        $guzzleClient = new \GuzzleHttp\Client([
            'verify' => BASE_PATH . 'core/cacert.pem',
        ]);

        $client = new Client();
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
                throw new \Exception($token['error_description'] ?? 'Token inválido ou expirado. Tente novamente.');
            }

            $google_oauth = new Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();

            $email = $google_account_info->email;
            $avatar = $google_account_info->picture;

            $usuarioService = new UsuarioService();
            $usuarioDTO = $usuarioService->buscarUsuarioPorEmail($email);

            if (!$usuarioDTO)
            {
                throw new \Exception('Seu e-mail não está cadastrado no sistema.');
            }

            // Se a foto do Google for diferente da armazenada, atualiza no banco.
            if ($avatar && $usuarioDTO->urlImgPerfil !== $avatar)
            {
                $usuarioService->updateAvatarUrl($usuarioDTO->id, $avatar);
                $usuarioDTO->urlImgPerfil = $avatar;
            }

            session_regenerate_id(true);
            $_SESSION['user_id'] = $usuarioDTO->id;
            $_SESSION['user_name'] = $usuarioDTO->nome;
            $_SESSION['user_email'] = $usuarioDTO->email;
            $_SESSION['user_role'] = $usuarioDTO->tipo;
            $_SESSION['user_avatar'] = $usuarioDTO->urlImgPerfil ?? $avatar;
            $_SESSION['user_turma'] = $usuarioDTO->nome_turma ?? null;
            $_SESSION['user_turma_id'] = $usuarioDTO->id_turma ?? null;

            $redirect = $usuarioDTO->tipo === 'Aluno' ? '?param=area_aluno' : '?param=dashboard';
            header('Location: ' . $redirect);
            exit();
        }
        catch (\Exception $e)
        {
            session_unset();
            session_destroy();
            header('Location: ?param=login&error=' . urlencode(string: 'Ocorreu um erro: ' . $e->getMessage()));
            exit();
        }
    }
}

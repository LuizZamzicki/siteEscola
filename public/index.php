<?php

// Inicia a sessão em todas as requisições
session_start();

// Define constantes de caminho para facilitar a inclusão de arquivos
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH . 'app' . DIRECTORY_SEPARATOR);

// Carrega o autoloader do Composer
require_once BASE_PATH . 'vendor/autoload.php';

// Carrega funções auxiliares (como a de redirecionamento)
require_once APP_PATH . 'Utils/helpers.php';

// Habilita a exibição de erros durante o desenvolvimento
ini_set('display_errors', 1);
error_reporting(E_ALL);

use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AlunoController;

// Instancia o roteador
$router = new Router();

// --- Definição de Rotas ---

// Rota da Home 
$router->get('home', [HomeController::class, 'index']);

// Rotas da página de perfil do aluno
$router->get('perfil', [AlunoController::class, 'perfil']);
$router->post('salvar-comentario', [AlunoController::class, 'salvarComentario']);

// --- Despacho da Rota ---
$uri = $_GET['param'] ?? 'home';
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri, $method);
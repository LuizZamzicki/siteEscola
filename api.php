<?php
// api.php - API Endpoint
// This file must be self-contained in its setup, as it's called directly.

// 1. Start the session immediately to access user data.
// This must be done before any other code is executed.
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

// 2. Define the base path for consistent file includes.
if (!defined('BASE_PATH'))
{
    define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

// 3. Load Composer's autoloader. This is essential for all services.
require_once BASE_PATH . 'vendor/autoload.php';

header('Content-Type: application/json');

// Prefer action from POST for state-changing requests, fallback to GET for searches.
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (empty($action))
{
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Ação não especificada.']);
    exit;
}

// --- Security & Validation ---

// Actions that modify data must be POST
$post_actions = ['mark_notification_read', 'salvar_pais', 'salvar_horario_config', 'salvar_materia'];
if (in_array($action, $post_actions) && $_SERVER['REQUEST_METHOD'] !== 'POST')
{
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido para esta ação.']);
    exit;
}

// Actions that require authentication
$auth_required_actions = ['mark_notification_read', 'get_paises', 'salvar_pais', 'salvar_horario_config', 'salvar_materia'];
if (in_array($action, $auth_required_actions) && !isset($_SESSION['user_id']))
{
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Autenticação necessária.']);
    exit;
}

// --- Action Handling ---

// The Database class is needed for the actions below.
require_once BASE_PATH . 'core/services/database.php';
$db = new Database();
$response = [];

// Ações que requerem o parâmetro 'term' para busca
$actions_requiring_term = ['search_autores', 'search_editoras', 'search_generos'];
$term = '';
if (in_array($action, $actions_requiring_term))
{
    $term = trim($_GET['term'] ?? '');
    if (empty($term))
    {
        echo json_encode([]); // Retorna array vazio se o termo for inválido
        exit;
    }
}

try
{
    switch ($action)
    {

        case 'search_autores':
            $sql = "SELECT nome FROM autores WHERE nome LIKE :term LIMIT 5";
            $response = $db->query($sql, [':term' => '%' . $term . '%']);
            break;
        case 'search_editoras':
            $sql = "SELECT nome FROM editoras WHERE nome LIKE :term LIMIT 5";
            $response = $db->query($sql, [':term' => '%' . $term . '%']);
            break;
        case 'search_generos':
            $sql = "SELECT descricao as nome FROM generos_livros WHERE descricao LIKE :term LIMIT 5";
            $response = $db->query($sql, [':term' => '%' . $term . '%']);
            break;

        case 'get_paises':
            require_once BASE_PATH . 'core/services/PaisService.php';
            $paisService = new PaisService();
            $paises = $paisService->buscarTodos();
            $data = array_map(function ($pais)
            {
                return ['id_pais' => $pais->id, 'nome' => $pais->nome];
            }, $paises);
            $response = ['success' => true, 'data' => $data];
            break;

        case 'salvar_pais':
            require_once BASE_PATH . 'core/services/PaisService.php';
            require_once BASE_PATH . 'core/models/PaisDTO.php';
            $paisService = new PaisService();
            $nomePais = trim($_POST['country_name'] ?? '');
            if (empty($nomePais))
            {
                $response = ['success' => false, 'message' => 'O nome do país não pode ser vazio.'];
            }
            else
            {
                $paisDTO = new PaisDTO(null, $nomePais);
                $response = $paisService->salvarPais($paisDTO);
            }
            break;

        case 'salvar_materia':
            require_once BASE_PATH . 'core/services/MateriaService.php';
            require_once BASE_PATH . 'core/models/MateriaDTO.php';
            $materiaService = new MateriaService();
            $nomeMateria = trim($_POST['materia_nome'] ?? '');
            if (empty($nomeMateria))
            {
                $response = ['success' => false, 'message' => 'O nome da matéria não pode ser vazio.'];
            }
            else
            {
                $materiaDTO = new MateriaDTO(null, $nomeMateria);
                $response = $materiaService->salvar($materiaDTO);
            }
            break;

        case 'salvar_horario_config':
            // ...
            $dto = new HorarioConfigDTO(
                empty($_POST['horario-id']) ? null : (int)$_POST['horario-id'],
                $_POST['label'] ?? null,
                $_POST['tipo_horario'] ?? 'aula',
                $_POST['horario_inicio'] ?? '',
                $_POST['horario_fim'] ?? '',
                0, // A ordem é calculada no service
                empty($_POST['periodo_id']) ? null : (int)$_POST['periodo_id'] // <-- Adicione este parâmetro
            );
            $result = $horarioService->salvarHorarioConfig($dto);
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();

        case 'mark_notification_read':
            // Safely get notification_id, with fallback to 'id' for compatibility.
            $notificationId = filter_input(INPUT_POST, 'notification_id', FILTER_VALIDATE_INT);
            if ($notificationId === false || $notificationId === null)
            {
                $notificationId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            }

            $userId = $_SESSION['user_id'];

            // Check for invalid or non-existent ID
            if ($notificationId === false || $notificationId === null)
            {
                http_response_code(400);
                $response = ['status' => 'error', 'message' => 'ID de notificação inválido.'];
            }
            else
            {
                require_once BASE_PATH . 'core/services/NotificacaoService.php';
                $notificacaoService = new NotificacaoService();

                if ($notificacaoService->marcarComoLida($notificationId, $userId))
                {
                    $response = ['status' => 'success', 'success' => true];
                }
                else
                {
                    http_response_code(500);
                    $response = ['status' => 'error', 'success' => false, 'message' => 'Falha ao atualizar o status da notificação.'];
                }
            }
            break;

        default:
            http_response_code(400);
            $response = ['status' => 'error', 'message' => 'Ação inválida.'];
            break;
    }
}
catch (Exception $e)
{
    http_response_code(500);
    $response = ['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()];
    error_log($e->getMessage());
}

echo json_encode($response);

<?php
//TODO colocar dps no agendador de tarefas pra tirar as reservas expiradas automaticamente
date_default_timezone_set('America/Sao_Paulo');

if (!defined('BASE_PATH'))
{
    define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

require_once BASE_PATH . 'vendor/autoload.php';
require_once BASE_PATH . 'core/services/ReservaService.php';

echo "Iniciando processo de verificação de reservas expiradas em " . date('Y-m-d H:i:s') . "...\n";

try
{
    $reservaService = new ReservaService();
    $resultado = $reservaService->processarReservasExpiradas();
    echo "Resultado: " . $resultado['message'] . "\n";
}
catch (Exception $e)
{
    $errorMessage = "Falha crítica ao executar o script de reservas expiradas: " . $e->getMessage();
    error_log($errorMessage);
    echo $errorMessage . "\n";
}

echo "Processo finalizado.\n";

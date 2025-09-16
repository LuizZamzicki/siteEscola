<?php

namespace App\Controllers;

use Core\View;
use App\Models\AutorService;
use Core\Database;

class AutoresController
{
    private AutorService $autorService;
    private Database $db;

    public function __construct()
    {
        $this->autorService = new AutorService();
        $this->db = new Database();
    }

    public function index()
    {
        $autores = $this->autorService->buscarTodos();
        $paises = $this->db->query("SELECT id_pais, nome FROM paises ORDER BY nome");
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('adm/autores', [
            'autores' => $autores,
            'paises' => $paises,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ], 'adm');
    }

    public function salvar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $action = $_POST['action'] ?? '';
            $feedbackMessage = '';
            $feedbackType = 'success';

            if ($action === 'salvar_autor')
            {
                $autorDTO = new \App\Models\DTO\AutorDTO(
                    empty($_POST['author-id']) ? null : (int)$_POST['author-id'],
                    $_POST['author-name'] ?? '',
                    (int)($_POST['author-country'] ?? 1)
                );
                if ($this->autorService->salvarAutor($autorDTO))
                {
                    $feedbackMessage = "Autor salvo com sucesso!";
                }
                else
                {
                    $feedbackMessage = "Erro ao salvar autor.";
                    $feedbackType = 'error';
                }
            }

            if ($action === 'excluir_autor')
            {
                $id = (int)$_POST['id'];
                $errorMessage = '';
                if ($this->autorService->excluirAutor($id, $errorMessage))
                {
                    $feedbackMessage = "Autor excluído com sucesso!";
                }
                else
                {
                    $feedbackMessage = $errorMessage ?: "Erro ao excluir autor.";
                    $feedbackType = 'error';
                }
            }

            $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=autores_biblioteca&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}

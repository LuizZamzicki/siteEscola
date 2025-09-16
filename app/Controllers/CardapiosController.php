<?php

namespace App\Controllers;

use App\Models\CardapioService;
use Core\View;

class CardapiosController
{
    public function index()
    {
        $feedbackMessage = '';
        $feedbackType = 'success';
        if (isset($_GET['msg']))
        {
            $feedbackMessage = htmlspecialchars(urldecode($_GET['msg']));
            $feedbackType = $_GET['type'] ?? 'success';
        }

        $cardapioService = new CardapioService();
        $cardapios = $cardapioService->buscarTodos();

        View::render('Adm/cardapios', [
            'cardapios' => $cardapios,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvar()
    {
        $cardapioService = new CardapioService();
        $dto = new \App\Models\DTO\CardapioDTO();
        $dto->id = $_POST['id'] ?? null;
        $dto->data = $_POST['data'] ?? null;
        $dto->descricao = $_POST['descricao'] ?? null;
        $dto->tipo = $_POST['tipo'] ?? null;
        $result = $cardapioService->salvar($dto);
        $feedbackMessage = urlencode($result['message']);
        $feedbackType = $result['success'] ? 'success' : 'error';
        header("Location: ?param=cardapios&msg={$feedbackMessage}&type={$feedbackType}");
        exit();
    }

    public function excluir()
    {
        $cardapioService = new CardapioService();
        $id = (int)($_POST['cardapio-id'] ?? 0);
        $result = $cardapioService->excluir($id);
        $feedbackMessage = urlencode($result['message']);
        $feedbackType = $result['success'] ? 'success' : 'error';
        header("Location: ?param=cardapios&msg={$feedbackMessage}&type={$feedbackType}");
        exit();
    }
}

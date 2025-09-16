<?php

namespace App\Controllers;

use Core\View;
use App\Models\BibliotecaService;
use App\Models\ReservaService;
use App\Models\UsuarioService;
use App\Models\DTO\LivroDTO;
use App\Models\AutorService;
use App\Models\EditoraService;
use App\Models\GeneroService;



class BibliotecaAdmController
{
    private BibliotecaService $bibliotecaService;
    private ReservaService $reservaService;
    private UsuarioService $usuarioService;

    public function __construct()
    {
        $this->bibliotecaService = new BibliotecaService();
        $this->reservaService = new ReservaService();
        $this->usuarioService = new UsuarioService();
    }

    public function index()
    {
        $livros = $this->bibliotecaService->buscarTodosLivros();
        $emprestimos = $this->bibliotecaService->buscarEmprestimosAtivos();
        $reservas = $this->reservaService->buscarPorStatus('Aguardando Retirada');
        $alunosAtivos = array_filter($this->usuarioService->buscarTodosAlunos(), fn($aluno) => $aluno->status === 'Ativo');
        $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
        $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
        View::render('Adm/biblioteca', [
            'livros' => $livros,
            'emprestimos' => $emprestimos,
            'reservas' => $reservas,
            'alunosAtivos' => $alunosAtivos,
            'feedbackMessage' => $feedbackMessage,
            'feedbackType' => $feedbackType
        ]);
    }

    public function salvarLivro()
    {
        $autorService = new AutorService();
        $editoraService = new EditoraService();
        $generoService = new GeneroService();
        $livroDTO = LivroDTO::fromArray($_POST);
        $foto = $_FILES['book-photo'] ?? null;
        $autorNome = $_POST['book-author'] ?? '';
        $editoraNome = $_POST['book-publisher'] ?? '';
        $generoNomesStr = $_POST['book-genre'] ?? '';

        $autorId = $autorNome ? $autorService->consultarOuCriarAutor($autorNome) : null;
        $editoraId = $editoraNome ? $editoraService->consultarOuCriarEditora($editoraNome) : null;
        $generoNomes = !empty($generoNomesStr) ? array_map('trim', explode(',', $generoNomesStr)) : [];
        $generoIds = [];
        foreach ($generoNomes as $generoNome)
        {
            if (!empty($generoNome))
            {
                $generoIds[] = $generoService->consultarOuCriarGenero($generoNome);
            }
        }

        $success = $this->bibliotecaService->salvarLivro($livroDTO, $foto, $autorId, $editoraId, $generoIds);
        $msg = $success ? 'Livro salvo com sucesso!' : 'Erro ao salvar livro.';
        $type = $success ? 'success' : 'error';
        header('Location: ?param=biblioteca&msg=' . urlencode($msg) . '&type=' . $type);
        exit();
    }

    public function emprestimoManual()
    {
        $livroId = (int)($_POST['loan-book-id'] ?? 0);
        $alunoId = (int)($_POST['loan-student-id'] ?? 0);
        $dataDevolucao = $_POST['loan-return-date'] ?? '';
        $success = false;
        $json_data = [];

        if ($livroId && $alunoId && !empty($dataDevolucao))
        {
            $result = $this->bibliotecaService->emprestimoManual($livroId, $alunoId, $dataDevolucao);
            if ($result['success'])
            {
                $success = true;
                $json_data['newLoan'] = $result['newLoan'];
                $json_data['livroId'] = $livroId;
            }
        }
        $msg = $success ? 'Empréstimo manual realizado com sucesso!' : 'Erro ao realizar empréstimo manual.';
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success, 'message' => $msg], $json_data));
        exit();
    }

    public function confirmarAcao()
    {
        $sub_action = $_POST['sub_action'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        $errorMessage = '';
        $success = false;
        $json_data = [];
        $feedbackMessage = '';

        switch ($sub_action)
        {
            case 'excluir_livro':
                $success = $this->bibliotecaService->excluirLivro($id, $errorMessage);
                $feedbackMessage = $success ? 'Livro excluído com sucesso!' : ($errorMessage ?: 'Erro ao excluir livro.');
                break;
            case 'devolver_livro':
                $success = $this->bibliotecaService->devolverLivro($id);
                $feedbackMessage = $success ? 'Devolução confirmada!' : 'Erro ao confirmar devolução.';
                break;
            case 'estender_emprestimo':
                $newDate = $this->bibliotecaService->estenderEmprestimo($id, 15);
                $success = $newDate !== null;
                $feedbackMessage = $success ? 'Prazo de empréstimo estendido com sucesso!' : 'Erro ao estender o prazo.';
                if ($success)
                {
                    $json_data['new_date'] = (new \DateTime($newDate))->format('d/m/Y');
                }
                break;
            case 'aprovar_reserva':
                $success = $this->reservaService->confirmarRetirada($id);
                $feedbackMessage = $success ? 'Retirada do livro confirmada e empréstimo iniciado!' : 'Erro ao confirmar retirada.';
                break;
            case 'recusar_reserva':
                $success = $this->reservaService->cancelarReservaAdmin($id);
                $feedbackMessage = $success ? 'Reserva cancelada.' : 'Erro ao cancelar reserva.';
                break;
        }
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success, 'message' => $feedbackMessage], $json_data));
        exit();
    }

    // Métodos para salvar, editar, excluir livros, reservas, etc. devem ser implementados conforme a necessidade
}

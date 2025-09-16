<?php

namespace App\Controllers;

use Core\View;
use App\Models\BibliotecaService;
use App\Models\ReservaService;
use App\Models\AvaliacaoService;

class BibliotecaController
{
    private BibliotecaService $bibliotecaService;
    private ReservaService $reservaService;
    private AvaliacaoService $avaliacaoService;

    public function __construct()
    {
        $this->bibliotecaService = new BibliotecaService();
        $this->reservaService = new ReservaService();
        $this->avaliacaoService = new AvaliacaoService();
    }

    public function index()
    {
        $aluno_id = $_SESSION['user_id'] ?? null;
        $turma_id = $_SESSION['user_turma_id'] ?? null;

        $reservasEEmprestimos = $this->reservaService->buscarReservasEEmprestimosPorAluno($aluno_id);
        $reservationsForJs = array_map(function ($item)
        {
            $book = new \stdClass();
            $book->id = $item->id;
            $book->title = $item->titulo;
            $book->img = $item->url_foto ?? null;
            $book->author = implode(', ', $item->autores);
            if ($item->status_reserva === 'Emprestado')
            {
                $book->status = 'devolver';
                $book->date = $item->data_devolucao_prevista ? (new \DateTime($item->data_devolucao_prevista))->format('d/m/Y') : null;
            }
            elseif ($item->status_reserva === 'Aguardando Retirada')
            {
                $book->status = 'retirar';
                $book->date = $item->data_validade_reserva ? (new \DateTime($item->data_validade_reserva))->format('d/m/Y') : null;
            }
            return $book;
        }, array_filter($reservasEEmprestimos));

        function format_book_for_js($book)
        {
            if (is_object($book))
            {
                $jsBook = new \stdClass();
                $jsBook->id = $book->id ?? null;
                $jsBook->title = $book->titulo ?? 'Título não encontrado';
                $jsBook->img = $book->url_foto ?? null;
                $jsBook->author = implode(', ', $book->autores ?? []);
                $jsBook->genre = implode(', ', $book->generos ?? []);
                $jsBook->publisher = $book->nome_editora ?? 'Não informado';
                $jsBook->pubdate = $book->data_publicacao ? (new \DateTime($book->data_publicacao))->format('Y') : 'Não informado';
                $jsBook->synopsis = $book->subtitulo ?? 'Sem sinopse.';
                $jsBook->available = $book->qtde_disponivel ?? 0;
                $jsBook->rating = $book->nota_media ?? 0;
                $jsBook->total_ratings = $book->total_avaliacoes ?? 0;
                $jsBook->my_rating = $book->minha_nota ?? null;
                return $jsBook;
            }
            return null;
        }

        $libraryData = [
            'reservations' => $reservationsForJs,
            'required' => array_map('format_book_for_js', $this->bibliotecaService->buscarLeituraObrigatoriaPorTurma($turma_id)),
            'recommended' => array_map('format_book_for_js', $this->bibliotecaService->buscarRecomendados($aluno_id)),
            'all' => array_map('format_book_for_js', $this->bibliotecaService->buscarTodosLivros()),
            'read' => array_map('format_book_for_js', $this->bibliotecaService->buscarHistoricoDeLeituraPorAluno($aluno_id)),
            'pendingRatings' => $this->avaliacaoService->buscarAvaliacoesPendentesPorAluno($aluno_id)
        ];

        View::render('aluno/biblioteca', [
            'libraryData' => $libraryData
        ], 'aluno');
    }
}

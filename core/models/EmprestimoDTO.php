<?php

class EmprestimoDTO
{
    public ?int $id;
    public int $idUsuario;
    public int $idLivro;
    public string $dataEmprestimo;
    public string $dataDevolucaoPrevista;
    public ?string $dataDevolucaoReal;
    public string $status;
    public ?string $usuarioNome; // Preenchido via JOIN
    public ?string $livroTitulo; // Preenchido via JOIN

    public function __construct(
        ?int $id = null,
        int $idUsuario = 0,
        int $idLivro = 0,
        string $dataEmprestimo = '',
        string $dataDevolucaoPrevista = '',
        ?string $dataDevolucaoReal = null,
        string $status = 'Pendente',
        ?string $usuarioNome = null,
        ?string $livroTitulo = null
    ) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLivro = $idLivro;
        $this->dataEmprestimo = $dataEmprestimo;
        $this->dataDevolucaoPrevista = $dataDevolucaoPrevista;
        $this->dataDevolucaoReal = $dataDevolucaoReal;
        $this->status = $status;
        $this->usuarioNome = $usuarioNome;
        $this->livroTitulo = $livroTitulo;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_emprestimo'] ?? null,
            $data['id_usuario'] ?? 0,
            $data['id_livro'] ?? 0,
            $data['data_emprestimo'] ?? '',
            $data['data_devolucao_prevista'] ?? '',
            $data['data_devolucao_real'] ?? null,
            $data['status'] ?? 'Pendente',
            $data['nome_usuario'] ?? null, // Supondo JOIN com usuarios
            $data['titulo_livro'] ?? null  // Supondo JOIN com livros
        );
    }
}
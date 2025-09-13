<?php

class LivroDTO
{
    public ?int $id;
    public string $titulo;
    public ?string $subtitulo;
    public ?int $num_paginas;
    public ?string $url_foto;
    public int $qtde_total;
    public int $qtde_disponivel;
    public int $qtde_reservada;
    public ?int $id_editora;
    public ?string $data_publicacao; // Adicionado para a data de publicação
    public ?int $id_autor;

    // Propriedades para carregar dados de tabelas relacionadas
    public array $autores = [];
    public array $generos = [];
    public ?string $nome_editora = null;

    public function __construct(
        ?int $id = null,
        string $titulo = '',
        ?string $subtitulo = null,
        ?int $num_paginas = null,
        ?string $url_foto = null,
        int $qtde_total = 1,
        int $qtde_disponivel = 1,
        int $qtde_reservada = 0,
        ?int $id_editora = null,
        ?string $data_publicacao = null, // Adicionado ao construtor
        ?int $id_autor = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->subtitulo = $subtitulo;
        $this->num_paginas = $num_paginas;
        $this->url_foto = $url_foto;
        $this->qtde_total = $qtde_total;
        $this->qtde_disponivel = $qtde_disponivel;
        $this->qtde_reservada = $qtde_reservada;
        $this->id_editora = $id_editora;
        $this->data_publicacao = $data_publicacao; // Atribuição
        $this->id_autor = $id_autor;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_livro'] ?? null,
            $data['titulo'] ?? '',
            $data['subtitulo'] ?? null,
            isset($data['num_paginas']) ? (int)$data['num_paginas'] : null,
            $data['url_foto'] ?? null,
            (int)($data['qtde_total'] ?? 1),
            (int)($data['qtde_disponivel'] ?? 1),
            (int)($data['qtde_reservada'] ?? 0),
            isset($data['id_editora']) ? (int)$data['id_editora'] : null,
            $data['data_publicacao'] ?? null, // Popula a data de publicação
            isset($data['id_autor']) ? (int)$data['id_autor'] : null
        );
    }
}

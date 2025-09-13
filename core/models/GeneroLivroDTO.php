<?php

class GeneroLivroDTO
{
    public ?int $id;
    public string $descricao;

    public function __construct(
        ?int $id = null,
        string $descricao = ''
    ) {
        $this->id = $id;
        $this->descricao = $descricao;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_genero_livro'] ?? null,
            $data['descricao'] ?? ''
        );
    }
}
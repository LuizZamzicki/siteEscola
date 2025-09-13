<?php

class AlunoComentarioDTO
{
    public ?int $id;
    public ?string $urlFoto;
    public ?string $nome;
    public ?string $serie;
    public ?string $comentario;

    public function __construct(
        ?int $id = null,
        ?string $urlFoto = null,
        ?string $nome = null,
        ?string $serie = null,
        ?string $comentario = null
    ) {
        $this->id = $id;
        $this->urlFoto = $urlFoto;
        $this->nome = $nome;
        $this->serie = $serie;
        $this->comentario = $comentario;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_aluno_comentario'] ?? null,
            $data['url_foto'] ?? null,
            $data['nome'] ?? null,
            $data['serie'] ?? null,
            $data['comentario'] ?? null
        );
    }
}
<?php

class EstruturaDTO
{
    public ?int $id;
    public ?string $titulo;
    public ?string $descricao;
    public ?string $icone;
    public ?string $urlImagem;

    public function __construct(
        ?int $id = null,
        ?string $titulo = null,
        ?string $descricao = null,
        ?string $icone = null,
        ?string $urlImagem = null
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->icone = $icone;
        $this->urlImagem = $urlImagem;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_estrutura'] ?? null,
            $data['titulo'] ?? null,
            $data['descricao'] ?? null,
            $data['icone'] ?? null,
            $data['url_imagem'] ?? null
        );
    }
}
<?php

class AutorDTO
{
    public ?int $id;
    public string $nome;
    public int $idPais;
    public ?string $paisNome; // Preenchido via JOIN

    public function __construct(
        ?int $id = null,
        string $nome = '',
        int $idPais = 0,
        ?string $paisNome = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->idPais = $idPais;
        $this->paisNome = $paisNome;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_autor'] ?? null,
            $data['nome'] ?? '',
            $data['id_pais'] ?? 0,
            $data['nome_pais'] ?? null // Supondo um JOIN com a tabela paises
        );
    }
}
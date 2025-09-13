<?php

class MateriaDTO
{
    public ?int $id;
    public string $nome;

    public function __construct(?int $id = null, string $nome = '')
    {
        $this->id = $id;
        $this->nome = $nome;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_materia'] ?? null,
            $data['nome'] ?? ''
        );
    }
}

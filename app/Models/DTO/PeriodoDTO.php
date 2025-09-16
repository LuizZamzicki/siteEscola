<?php

namespace App\Models\DTO;

class PeriodoDTO
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
            $data['id'] ?? null,
            $data['nome'] ?? ''
        );
    }
}

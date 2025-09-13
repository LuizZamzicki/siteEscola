<?php

class DepartamentoDTO
{
    public ?int $id;
    public string $descricao;
    public string $status;

    public function __construct(
        ?int $id = null,
        string $descricao = '',
        string $status = 'Inativo'
    ) {
        $this->id = $id;
        $this->descricao = $descricao;
        $this->status = $status;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_departamento'] ?? null,
            $data['descricao'] ?? '',
            $data['status'] ?? 'Inativo'
        );
    }
}
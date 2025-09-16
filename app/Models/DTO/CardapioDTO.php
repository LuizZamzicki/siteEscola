<?php

namespace App\Models\DTO;

class CardapioDTO
{
    public ?int $id;
    public string $data;
    public ?string $lanche_manha;
    public string $almoco;
    public ?string $lanche_tarde;

    public function __construct(
        ?int $id = null,
        string $data = '',
        ?string $lanche_manha = null,
        string $almoco = '',
        ?string $lanche_tarde = null
    ) {
        $this->id = $id;
        $this->data = $data;
        $this->lanche_manha = $lanche_manha;
        $this->almoco = $almoco;
        $this->lanche_tarde = $lanche_tarde;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_cardapio'] ?? null,
            $data['data'] ?? '',
            $data['lanche_manha'] ?? null,
            $data['almoco'] ?? '',
            $data['lanche_tarde'] ?? null
        );
    }
}

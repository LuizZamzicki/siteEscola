<?php

class PaisDTO
{
    public ?int $id;
    public string $nome;

    public function __construct(?int $id, string $nome)
    {
        $this->id = $id;
        $this->nome = $nome;
    }
}
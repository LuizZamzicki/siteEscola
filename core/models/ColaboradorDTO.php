<?php

class ColaboradorDTO
{
    public ?int $id;
    public ?string $nome;
    public ?string $cargo;
    public ?int $idDepartamento;
    public ?string $urlFoto;
    public string $status;
    public ?string $departamentoNome; // Preenchido via JOIN

    public function __construct(
        ?int $id = null,
        ?string $nome = null,
        ?string $cargo = null,
        ?int $idDepartamento = null,
        ?string $urlFoto = null,
        string $status = 'Inativo',
        ?string $departamentoNome = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->cargo = $cargo;
        $this->idDepartamento = $idDepartamento;
        $this->urlFoto = $urlFoto;
        $this->status = $status;
        $this->departamentoNome = $departamentoNome;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_colaborador'] ?? null,
            $data['nome'] ?? null,
            $data['cargo'] ?? null,
            $data['id_departamento'] ?? null,
            $data['url_foto'] ?? null,
            $data['status'] ?? 'Inativo',
            $data['nome_departamento'] ?? null // Supondo um JOIN
        );
    }
}
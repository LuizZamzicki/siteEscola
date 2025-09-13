<?php

class TurmaDTO
{
    public ?int $id;
    public string $nome;
    public ?string $ensino;
    public ?string $periodo;
    public int $totalAlunos = 0;
    public int $totalProfessores = 0;

    public function __construct(
        ?int $id = null,
        string $nome = '',
        ?string $ensino = null,
        ?string $periodo = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->ensino = $ensino;
        $this->periodo = $periodo;
    }

    public static function fromArray(array $data): self
    {
        $instance = new self(
            $data['id_turma'] ?? null,
            $data['nome_turma'] ?? '',
            $data['ensino'] ?? null,
            $data['periodo'] ?? null
        );
        $instance->totalAlunos = (int) ($data['total_alunos'] ?? 0);
        $instance->totalProfessores = (int) ($data['total_professores'] ?? 0);
        return $instance;
    }
}

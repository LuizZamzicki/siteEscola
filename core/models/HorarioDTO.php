<?php

class HorarioDTO
{
    public ?int $id_horario;
    public int $id_turma;
    public string $dia_semana;
    public string $horario_inicio;
    public string $horario_fim;
    public string $materia;
    public ?int $id_professor;
    public ?string $nome_professor;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id_horario = $data['id_horario'] ?? null;
        $dto->id_turma = (int)$data['id_turma'];
        $dto->dia_semana = $data['dia_semana'];
        $dto->horario_inicio = $data['horario_inicio'];
        $dto->horario_fim = $data['horario_fim'];
        $dto->materia = $data['materia'];
        $dto->id_professor = isset($data['id_professor']) ? (int)$data['id_professor'] : null;
        $dto->nome_professor = $data['nome_professor'] ?? null;
        return $dto;
    }
}

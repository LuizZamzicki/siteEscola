<?php
class HorarioAulaDTO
{
    public ?int $id;
    public int $id_turma;
    public string $dia_semana;
    public int $id_horario_config;
    public int $id_materia;
    public int $id_professor;

    public function __construct(?int $id, int $id_turma, string $dia_semana, int $id_horario_config, int $id_materia, int $id_professor)
    {
        $this->id = $id;
        $this->id_turma = $id_turma;
        $this->dia_semana = $dia_semana;
        $this->id_horario_config = $id_horario_config;
        $this->id_materia = $id_materia;
        $this->id_professor = $id_professor;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_horario_aula'] ?? null,
            $data['id_turma'],
            $data['dia_semana'],
            $data['id_horario_config'],
            $data['id_materia'],
            $data['id_professor']
        );
    }
}

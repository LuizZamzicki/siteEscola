<?php
class HorarioConfigDTO
{
    public ?int $id;
    public ?string $label;
    public string $tipo;
    public string $horario_inicio;
    public string $horario_fim;
    public int $ordem;
    public ?int $id_periodo;

    public function __construct(?int $id = null, ?string $label = null, string $tipo = 'aula', string $horario_inicio = '', string $horario_fim = '', int $ordem = 0, ?int $id_periodo = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->tipo = $tipo;
        $this->horario_inicio = $horario_inicio;
        $this->horario_fim = $horario_fim;
        $this->ordem = $ordem;
        $this->id_periodo = $id_periodo;
    }
}

<?php

class EventoCalendarioDTO
{
    public ?int $id;
    public string $titulo;
    public string $data_inicio; // Formato: Y-m-d
    public ?string $data_fim;    // Formato: Y-m-d
    public string $tipo;
    public string $publico_alvo;
    public bool $recorrente;

    public function __construct(
        ?int $id = null,
        string $titulo = '',
        string $data_inicio = '',
        ?string $data_fim = null,
        string $tipo = 'evento',
        string $publico_alvo = 'todos',
        bool $recorrente = false
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->data_inicio = $data_inicio;
        $this->data_fim = $data_fim;
        $this->tipo = $tipo;
        $this->publico_alvo = $publico_alvo;
        $this->recorrente = $recorrente;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_evento'] ?? null,
            $data['titulo'] ?? '',
            $data['data_inicio'] ?? '',
            $data['data_fim'] ?? null,
            $data['tipo'] ?? 'evento',
            $data['publico_alvo'] ?? 'todos',
            (bool)($data['recorrente'] ?? false)
        );
    }
}

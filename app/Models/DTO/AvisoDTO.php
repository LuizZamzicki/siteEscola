<?php
namespace App\Models\DTO;

class AvisoDTO
{
    public ?int $id;
    public string $titulo;
    public string $conteudo;
    public string $publico_alvo;
    public string $data_postagem;

    public function __construct(
        ?int $id = null,
        string $titulo = '',
        string $conteudo = '',
        string $publico_alvo = 'todos',
        string $data_postagem = ''
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->conteudo = $conteudo;
        $this->publico_alvo = $publico_alvo;
        $this->data_postagem = $data_postagem;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_aviso'] ?? null,
            $data['titulo'] ?? '',
            $data['conteudo'] ?? '',
            $data['publico_alvo'] ?? 'todos',
            $data['data_postagem'] ?? ''
        );
    }

    public function getFormattedDate(): string
    {
        if (empty($this->data_postagem))
        {
            return '';
        }
        $date = new \DateTime($this->data_postagem);
        $formatter = new \IntlDateFormatter('pt_BR', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
        return $formatter->format($date);
    }
}

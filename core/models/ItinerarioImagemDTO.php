<?php

class ItinerarioImagemDTO
{
    public ?int $id;
    public int $idItinerario;
    public string $urlImagem;
    public string $legenda;

    public function __construct(
        ?int $id = null,
        int $idItinerario = 0,
        string $urlImagem = '',
        string $legenda = ''
    ) {
        $this->id = $id;
        $this->idItinerario = $idItinerario;
        $this->urlImagem = $urlImagem;
        $this->legenda = $legenda;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_itinerario_imagem'] ?? null,
            $data['id_itinerario'] ?? 0,
            $data['url_imagem'] ?? '',
            $data['legenda'] ?? ''
        );
    }
}
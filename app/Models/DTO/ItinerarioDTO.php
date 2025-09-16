<?php

class ItinerarioDTO
{
    public ?int $id;
    public string $titulo;
    public string $descricaoResumo;
    public string $descricao;
    public string $urlImgFundo;
    public string $icone;
    public array $imagens = []; // Array de ItinerarioImagemDTO
    public array $categorias = []; // Array de CategoriaItinerarioDTO

    public function __construct(
        ?int $id = null,
        string $titulo = '',
        string $descricaoResumo = '',
        string $descricao = '',
        string $urlImgFundo = '',
        string $icone = ''
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricaoResumo = $descricaoResumo;
        $this->descricao = $descricao;
        $this->urlImgFundo = $urlImgFundo;
        $this->icone = $icone;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_itinerario'] ?? null,
            $data['titulo'] ?? '',
            $data['descricao_resumo'] ?? '',
            $data['descricao'] ?? '',
            $data['url_img_fundo'] ?? '',
            $data['icone'] ?? ''
        );
    }
}
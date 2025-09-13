<?php

class FaqDTO
{
    public ?int $id;
    public string $pergunta;
    public string $resposta;
    public string $status;

    public function __construct(
        ?int $id = null,
        string $pergunta = '',
        string $resposta = '',
        string $status = 'Inativo'
    ) {
        $this->id = $id;
        $this->pergunta = $pergunta;
        $this->resposta = $resposta;
        $this->status = $status;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_faq'] ?? null,
            $data['pergunta'] ?? '',
            $data['resposta'] ?? '',
            $data['status'] ?? 'Inativo'
        );
    }
}
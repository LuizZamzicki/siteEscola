<?php

class FormacaoAcademicaDTO
{
    public ?int $id;
    public string $curso;
    public string $nivel;
    public string $instituicao;

    public function __construct(
        ?int $id = null,
        string $curso = '',
        string $nivel = '',
        string $instituicao = ''
    ) {
        $this->id = $id;
        $this->curso = $curso;
        $this->nivel = $nivel;
        $this->instituicao = $instituicao;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_formacao'] ?? null,
            $data['curso'] ?? '',
            $data['nivel'] ?? '',
            $data['instituicao'] ?? ''
        );
    }
}
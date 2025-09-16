<?php
namespace App\Models\DTO;

class UsuarioDTO
{
    public ?int $id;
    public string $nome;
    public string $email;
    public ?string $urlImgPerfil;
    public string $status;
    public string $tipo;
    public ?string $turma;
    public array $turmas = [];

    public function __construct(
        ?int $id = null,
        string $nome = '',
        string $email = '',
        ?string $urlImgPerfil = null,
        string $status = 'Ativo',
        string $tipo = 'Aluno',
        ?string $turma = null
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->urlImgPerfil = $urlImgPerfil;
        $this->status = $status;
        $this->tipo = $tipo;
        $this->turma = $turma;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_usuario'] ?? null,
            $data['nome'] ?? '',
            $data['email'] ?? '',
            $data['url_img_perfil'] ?? null,
            $data['status'] ?? 'Ativo',
            $data['tipo'] ?? 'Aluno',
            $data['turma'] ?? null
        );
    }
}

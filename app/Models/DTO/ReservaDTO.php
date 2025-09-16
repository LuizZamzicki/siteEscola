<?php
namespace App\Models\DTO;

class ReservaDTO
{
    public ?int $id;
    public int $id_livro;
    public int $id_usuario;
    public string $data_reserva;
    public string $status;
    public ?string $livroTitulo;
    public ?string $usuarioNome;

    public function __construct(
        ?int $id = null,
        int $id_livro = 0,
        int $id_usuario = 0,
        string $data_reserva = '',
        string $status = 'Pendente',
        ?string $livroTitulo = null,
        ?string $usuarioNome = null
    ) {
        $this->id = $id;
        $this->id_livro = $id_livro;
        $this->id_usuario = $id_usuario;
        $this->data_reserva = $data_reserva;
        $this->status = $status;
        $this->livroTitulo = $livroTitulo;
        $this->usuarioNome = $usuarioNome;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_reserva'] ?? null,
            $data['id_livro'] ?? 0,
            $data['id_usuario'] ?? 0,
            $data['data_reserva'] ?? '',
            $data['status'] ?? 'Pendente',
            $data['titulo_livro'] ?? null,
            $data['nome_usuario'] ?? null
        );
    }
}

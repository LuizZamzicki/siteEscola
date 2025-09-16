<?php

namespace App\Models\DTO;

use DateTime;

class AtividadeRecenteDTO
{
    public string $tipo;
    public string $titulo;
    public string $autor;
    public string $descricao;
    public string $data;
    public ?string $avatar_url;
    public string $icone;
    public string $cor_icone;
    public string $cor_fundo_icone;

    public function __construct(
        string $tipo = '',
        string $autor = '',
        string $descricao = '',
        string $data = '',
        ?string $avatar_url = null
    ) {
        $this->tipo = $tipo;
        $this->autor = $autor;
        $this->descricao = $descricao;
        $this->data = $data;
        $this->avatar_url = $avatar_url;
        $this->setAppearance();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['tipo'] ?? '',
            $data['autor'] ?? '',
            $data['descricao'] ?? '',
            $data['data'] ?? '',
            $data['avatar_url'] ?? null
        );
    }

    private function setAppearance(): void
    {
        switch ($this->tipo)
        {
            case 'reserva':
                $this->titulo = 'Nova Reserva de Livro';
                $this->icone = 'fa-solid fa-bookmark';
                $this->cor_icone = 'text-amber-500';
                $this->cor_fundo_icone = 'bg-amber-100';
                break;
            case 'aviso':
                $this->titulo = 'Novo Aviso Publicado';
                $this->icone = 'fa-solid fa-bullhorn';
                $this->cor_icone = 'text-purple-500';
                $this->cor_fundo_icone = 'bg-purple-100';
                break;
            default:
                $this->titulo = 'Atividade do Sistema';
                $this->icone = 'fa-solid fa-circle-info';
                $this->cor_icone = 'text-slate-500';
                $this->cor_fundo_icone = 'bg-slate-100';
                break;
        }
    }

    public function getTempoRelativo(): string
    {
        if (empty($this->data))
        {
            return '';
        }

        $agora = new DateTime();
        $dataAtividade = new DateTime($this->data);
        $diferenca = $agora->diff($dataAtividade);

        if ($diferenca->y > 0)
            return "há " . $diferenca->y . " ano(s)";
        if ($diferenca->m > 0)
            return "há " . $diferenca->m . " mes(es)";
        if ($diferenca->d > 0)
            return "há " . $diferenca->d . " dia(s)";
        if ($diferenca->h > 0)
            return "há " . $diferenca->h . " hora(s)";
        if ($diferenca->i > 0)
            return "há " . $diferenca->i . " minuto(s)";
        return "agora mesmo";
    }
}

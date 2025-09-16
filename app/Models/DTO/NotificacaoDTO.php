<?php
namespace App\Models\DTO;

class NotificacaoDTO
{
    public int $id;
    public int $id_usuario;
    public string $tipo;
    public string $titulo;
    public string $descricao;
    public ?string $link;
    public string $data_criacao;
    public ?string $data_leitura;

    // Propriedades visuais
    public string $icone;
    public string $cor_icone;
    public string $cor_fundo_icone;

    public function __construct(array $data)
    {
        $this->id = $data['id_notificacao'];
        $this->id_usuario = $data['id_usuario'];
        $this->tipo = $data['tipo'];
        $this->titulo = $data['titulo'];
        $this->descricao = $data['descricao'];
        $this->link = $data['link'];
        $this->data_criacao = $data['data_criacao'];
        $this->data_leitura = $data['data_leitura'];
        $this->setAppearance();
    }

    private function setAppearance(): void
    {
        switch ($this->tipo)
        {
            case 'reserva_aprovada':
            case 'reserva_recusada':
                $this->icone = 'fa-solid fa-bookmark';
                $this->cor_icone = 'text-amber-500';
                $this->cor_fundo_icone = 'bg-amber-100';
                break;
            case 'aviso':
                $this->icone = 'fa-solid fa-bullhorn';
                $this->cor_icone = 'text-purple-500';
                $this->cor_fundo_icone = 'bg-purple-100';
                break;
            case 'evento_calendario':
                $this->icone = 'fa-solid fa-calendar-check';
                $this->cor_icone = 'text-sky-500';
                $this->cor_fundo_icone = 'bg-sky-100';
                break;
            default:
                $this->icone = 'fa-solid fa-circle-info';
                $this->cor_icone = 'text-slate-500';
                $this->cor_fundo_icone = 'bg-slate-100';
                break;
        }
    }

    public function getTempoRelativo(): string
    {
        if (empty($this->data_criacao))
        {
            return '';
        }

        $agora = new \DateTime();
        $dataAtividade = new \DateTime($this->data_criacao);
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

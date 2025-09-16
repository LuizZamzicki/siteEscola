<?php
namespace App\Models;

require_once BASE_PATH . 'app/Models/DTO/EventoCalendarioDTO.php';
require_once BASE_PATH . 'core/database.php';
require_once BASE_PATH . 'app/Models/NotificacaoService.php';

use Core\Database;
use App\Models\DTO\EventoCalendarioDTO;
use App\Models\NotificacaoService;

class EventoCalendarioService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Busca todos os eventos.
     * @return EventoCalendarioDTO[]
     */
    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM eventos_calendario ORDER BY data_inicio";
        $results = $this->db->query($sql);

        $eventos = [];
        foreach ($results as $row)
        {
            $eventos[] = EventoCalendarioDTO::fromArray($row);
        }
        return $eventos;
    }
    public function buscarParaAluno($turma): array
    {
        $sql = "SELECT * FROM eventos_calendario WHERE publico_alvo = :turma or publico_alvo = 'todos' ORDER BY data_inicio";
        $params = [':turma' => $turma];
        $results = $this->db->query($sql, $params);

        $eventos = [];
        foreach ($results as $row)
        {
            $eventos[] = EventoCalendarioDTO::fromArray($row);
        }
        return $eventos;
    }

    public function salvar(EventoCalendarioDTO $evento): bool
    {
        if ($evento->id)
        {
            // Atualizar
            $sql = "UPDATE eventos_calendario SET titulo = :titulo, data_inicio = :data_inicio, data_fim = :data_fim, tipo = :tipo, publico_alvo = :publico_alvo, recorrente = :recorrente WHERE id_evento = :id";
            $params = [
                ':titulo' => $evento->titulo,
                ':data_inicio' => $evento->data_inicio,
                ':data_fim' => $evento->data_fim,
                ':tipo' => $evento->tipo,
                ':publico_alvo' => $evento->publico_alvo,
                ':recorrente' => (int)$evento->recorrente,
                ':id' => $evento->id
            ];
        }
        else
        {
            // Inserir
            $sql = "INSERT INTO eventos_calendario (titulo, data_inicio, data_fim, tipo, publico_alvo, recorrente) VALUES (:titulo, :data_inicio, :data_fim, :tipo, :publico_alvo, :recorrente)";
            $params = [
                ':titulo' => $evento->titulo,
                ':data_inicio' => $evento->data_inicio,
                ':data_fim' => $evento->data_fim,
                ':tipo' => $evento->tipo,
                ':publico_alvo' => $evento->publico_alvo,
                ':recorrente' => (int)$evento->recorrente
            ];
        }

        if ($this->db->execute($sql, $params))
        {
            // Apenas notifica na criação de um novo evento, não em atualizações
            if (!$evento->id)
            {
                require_once BASE_PATH . 'core/services/NotificacaoService.php';
                $notificacaoService = new NotificacaoService();

                $dataEvento = (new \DateTime($evento->data_inicio))->format('d/m/Y');
                $tituloNotificacao = "Novo Evento: " . $evento->titulo;
                $descricaoNotificacao = "Um novo evento foi adicionado ao calendário para o dia {$dataEvento}: <strong>{$evento->titulo}</strong>.";

                $notificacaoService->criarNotificacaoParaGrupo(
                    $evento->publico_alvo, 'evento_calendario', $tituloNotificacao, $descricaoNotificacao, '?param=calendario'
                );
            }
            return true;
        }
        return false;
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM eventos_calendario WHERE id_evento = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
}

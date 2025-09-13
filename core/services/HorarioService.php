<?php
require_once 'database.php';
require_once BASE_PATH . 'core/models/HorarioAulaDTO.php';
require_once BASE_PATH . 'core/models/HorarioConfigDTO.php';

class HorarioService
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function buscarTodosHorariosConfig(?int $periodoId = null): array
    {
        $sql = "
            SELECT hc.*, p.nome as nome_periodo
            FROM horarios_config hc
            LEFT JOIN periodos p ON hc.id_periodo = p.id
        ";
        $params = [];

        if ($periodoId)
        {
            $sql .= " WHERE hc.id_periodo = :periodoId";
            $params[':periodoId'] = $periodoId;
        }

        $sql .= " ORDER BY hc.ordem ASC";

        return $this->db->query($sql, $params);
    }

    public function salvarHorarioConfig(HorarioConfigDTO $dto)
    {
        if (empty($dto->horario_inicio) || empty($dto->horario_fim))
        {
            return ['success' => false, 'message' => 'Horário de início e fim são obrigatórios.'];
        }
        if (empty($dto->id_periodo))
        {
            return ['success' => false, 'message' => 'O período é obrigatório.'];
        }

        if (strtotime($dto->horario_inicio) >= strtotime($dto->horario_fim))
        {
            return ['success' => false, 'message' => 'O horário de início deve ser anterior ao horário de fim.'];
        }

        // Validação de sobreposição de horários para o mesmo período
        $overlapQuery = "SELECT id FROM horarios_config WHERE id_periodo = :id_periodo AND (:horario_inicio < horario_fim) AND (:horario_fim > horario_inicio)";
        $overlapParams = [
            ':id_periodo' => $dto->id_periodo,
            ':horario_inicio' => $dto->horario_inicio,
            ':horario_fim' => $dto->horario_fim,
        ];

        if ($dto->id)
        {
            $overlapQuery .= " AND id != :id";
            $overlapParams[':id'] = $dto->id;
        }

        if (!empty($this->db->query($overlapQuery, $overlapParams)))
        {
            return ['success' => false, 'message' => 'O horário informado sobrepõe um horário já existente para este período.'];
        }

        if ($dto->id)
        {
            // Update
            $query = "UPDATE horarios_config SET label = :label, tipo = :tipo, horario_inicio = :horario_inicio, horario_fim = :horario_fim, id_periodo = :id_periodo WHERE id = :id";
            $params = [
                ':label' => empty($dto->label) ? null : $dto->label,
                ':horario_inicio' => $dto->horario_inicio,
                ':tipo' => $dto->tipo,
                ':horario_fim' => $dto->horario_fim,
                ':id_periodo' => $dto->id_periodo,
                ':id' => $dto->id
            ];
        }
        else
        {
            // Insert
            $maxOrderResult = $this->db->query("SELECT MAX(ordem) as max_ordem FROM horarios_config WHERE id_periodo = :id_periodo", [':id_periodo' => $dto->id_periodo]);
            $newOrder = ($maxOrderResult[0]['max_ordem'] ?? 0) + 1;

            $query = "INSERT INTO horarios_config (label, tipo, horario_inicio, horario_fim, ordem, id_periodo) VALUES (:label, :tipo, :horario_inicio, :horario_fim, :ordem, :id_periodo)";
            $params = [
                ':label' => empty($dto->label) ? null : $dto->label,
                ':tipo' => $dto->tipo,
                ':horario_inicio' => $dto->horario_inicio,
                ':horario_fim' => $dto->horario_fim,
                ':ordem' => $newOrder,
                ':id_periodo' => $dto->id_periodo
            ];
        }

        return $this->db->execute($query, $params)
            ? ['success' => true, 'message' => 'Horário salvo com sucesso!']
            : ['success' => false, 'message' => 'Erro ao salvar horário.'];
    }

    public function excluirHorarioConfig(int $id)
    {
        try
        {
            $this->db->beginTransaction();

            // Verificar se o horário está sendo usado em alguma grade de aula
            $checkQuery = "SELECT COUNT(*) as count FROM horario_aulas WHERE id_horario_config = :id";
            $result = $this->db->query($checkQuery, [':id' => $id]);
            if ($result[0]['count'] > 0)
            {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Não é possível excluir este horário, pois ele está em uso em uma ou mais grades de horário.'];
            }

            // Obter a ordem e o período do item a ser excluído para reordenar os demais
            $horario = $this->db->query("SELECT ordem, id_periodo FROM horarios_config WHERE id = :id", [':id' => $id]);
            if (empty($horario))
            {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Horário não encontrado para exclusão.'];
            }
            $ordemExcluida = $horario[0]['ordem'];
            $periodoId = $horario[0]['id_periodo'];

            // Excluir o horário
            $query = "DELETE FROM horarios_config WHERE id = :id";
            if (!$this->db->execute($query, [':id' => $id]))
            {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Erro ao excluir horário.'];
            }

            // Reordenar os itens subsequentes dentro do mesmo período
            $reorderQuery = "UPDATE horarios_config SET ordem = ordem - 1 WHERE ordem > :ordem_excluida AND id_periodo = :id_periodo";
            $this->db->execute($reorderQuery, [':ordem_excluida' => $ordemExcluida, ':id_periodo' => $periodoId]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Horário excluído com sucesso!'];
        }
        catch (PDOException $e)
        {
            $this->db->rollBack();
            error_log("Erro ao excluir horário config: " . $e->getMessage());
            return ['success' => false, 'message' => 'Ocorreu um erro no banco de dados ao tentar excluir o horário.'];
        }
    }

    /**
     * Busca o horário completo de uma turma, com matérias e professores.
     * @param int $turmaId
     * @return array
     */
    public function buscarHorarioCompletoPorTurma(int $turmaId): array
    {
        $sql = "
            SELECT 
                ha.dia_semana,
                hc.horario_inicio,
                hc.horario_fim,
                m.nome as materia,
                m.id_materia,
                p.nome as professor,
                p.id_usuario as id_professor
            FROM horario_aulas ha
            JOIN horarios_config hc ON ha.id_horario_config = hc.id
            JOIN materias m ON ha.id_materia = m.id_materia
            JOIN usuarios p ON ha.id_professor = p.id_usuario
            WHERE ha.id_turma = :turmaId AND p.tipo = 'Professor'
        ";

        $results = $this->db->query($sql, [':turmaId' => $turmaId]);

        $horarioOrganizado = [];
        foreach ($results as $aula)
        {
            $horarioStr = date('H:i', strtotime($aula['horario_inicio'])) . ' - ' . date('H:i', strtotime($aula['horario_fim']));
            $dia = $aula['dia_semana'];

            if (!isset($horarioOrganizado[$dia]))
            {
                $horarioOrganizado[$dia] = [];
            }

            $horarioOrganizado[$dia][$horarioStr] = [
                'materia' => $aula['materia'],
                'id_materia' => $aula['id_materia'],
                'professor' => $aula['professor'],
                'id_professor' => $aula['id_professor']
            ];
        }

        return $horarioOrganizado;
    }

    public function salvarAula(int $turmaId, string $diaSemana, string $horario, int $materiaId, int $professorId): array
    {
        list($inicio, $fim) = explode(' - ', $horario);
        $sqlHorario = "SELECT id FROM horarios_config WHERE horario_inicio = :inicio AND horario_fim = :fim AND tipo = 'aula'";
        $horarioConfig = $this->db->query($sqlHorario, [':inicio' => $inicio . ':00', ':fim' => $fim . ':00']);

        if (empty($horarioConfig))
        {
            return ['success' => false, 'message' => 'Configuração de horário não encontrada.'];
        }
        $horarioConfigId = $horarioConfig[0]['id'];

        $sqlCheck = "SELECT id_horario_aula FROM horario_aulas WHERE id_turma = :turmaId AND dia_semana = :diaSemana AND id_horario_config = :horarioConfigId";
        $existing = $this->db->query($sqlCheck, [':turmaId' => $turmaId, ':diaSemana' => $diaSemana, ':horarioConfigId' => $horarioConfigId]);

        if (!empty($existing))
        {
            $sql = "UPDATE horario_aulas SET id_materia = :materiaId, id_professor = :professorId WHERE id_horario_aula = :id";
            $params = [':materiaId' => $materiaId, ':professorId' => $professorId, ':id' => $existing[0]['id_horario_aula']];
        }
        else
        {
            $sql = "INSERT INTO horario_aulas (id_turma, dia_semana, id_horario_config, id_materia, id_professor) VALUES (:turmaId, :diaSemana, :horarioConfigId, :materiaId, :professorId)";
            $params = [':turmaId' => $turmaId, ':diaSemana' => $diaSemana, ':horarioConfigId' => $horarioConfigId, ':materiaId' => $materiaId, ':professorId' => $professorId];
        }

        return $this->db->execute($sql, $params)
            ? ['success' => true, 'message' => 'Horário salvo com sucesso!']
            : ['success' => false, 'message' => 'Erro ao salvar o horário.'];
    }

    public function excluirAula(int $turmaId, string $diaSemana, string $horario): array
    {
        list($inicio, $fim) = explode(' - ', $horario);
        $sqlHorario = "SELECT id FROM horarios_config WHERE horario_inicio = :inicio AND horario_fim = :fim AND tipo = 'aula'";
        $horarioConfig = $this->db->query($sqlHorario, [':inicio' => $inicio . ':00', ':fim' => $fim . ':00']);

        if (empty($horarioConfig))
        {
            return ['success' => false, 'message' => 'Configuração de horário não encontrada.'];
        }
        $horarioConfigId = $horarioConfig[0]['id'];

        $sql = "DELETE FROM horario_aulas WHERE id_turma = :turmaId AND dia_semana = :diaSemana AND id_horario_config = :horarioConfigId";
        $params = [':turmaId' => $turmaId, ':diaSemana' => $diaSemana, ':horarioConfigId' => $horarioConfigId];

        return $this->db->execute($sql, $params)
            ? ['success' => true, 'message' => 'Aula removida com sucesso!']
            : ['success' => false, 'message' => 'Erro ao remover a aula.'];
    }
}

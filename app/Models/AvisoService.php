<?php
namespace App\Models;

require_once BASE_PATH . 'core/models/AvisoDTO.php';
require_once BASE_PATH . 'core/services/database.php';

use Core\Database;
use App\Models\DTO\AvisoDTO;

class AvisoService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * @return AvisoDTO[]
     */
    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM avisos ORDER BY data_postagem DESC";
        $results = $this->db->query($sql);

        $avisos = [];
        foreach ($results as $row)
        {
            $avisos[] = AvisoDTO::fromArray($row);
        }
        return $avisos;
    }

    public function salvar(AvisoDTO $aviso): bool
    {
        if ($aviso->id)
        {
            // Atualizar
            $sql = "UPDATE avisos SET titulo = :titulo, conteudo = :conteudo, publico_alvo = :publico_alvo WHERE id_aviso = :id";
            $params = [
                ':titulo' => $aviso->titulo,
                ':conteudo' => $aviso->conteudo,
                ':publico_alvo' => $aviso->publico_alvo,
                ':id' => $aviso->id
            ];
        }
        else
        {
            // Inserir
            $sql = "INSERT INTO avisos (titulo, conteudo, publico_alvo) VALUES (:titulo, :conteudo, :publico_alvo)";
            $params = [
                ':titulo' => $aviso->titulo,
                ':conteudo' => $aviso->conteudo,
                ':publico_alvo' => $aviso->publico_alvo
            ];
        }

        if ($this->db->execute($sql, $params))
        {
            require_once BASE_PATH . 'core/services/NotificacaoService.php';
            $notificacaoService = new NotificacaoService();
            $titulo = "Novo Aviso: " . $aviso->titulo;
            $descricao = mb_strimwidth($aviso->conteudo, 0, 100, "...");
            $notificacaoService->criarNotificacaoParaGrupo($aviso->publico_alvo, 'aviso', $titulo, $descricao, '?param=avisos');
            return true;
        }
        return false;
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM avisos WHERE id_aviso = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }
}

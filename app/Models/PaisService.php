<?php
require_once BASE_PATH . 'core/models/PaisDTO.php';
require_once BASE_PATH . 'core/services/database.php';

class PaisService
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function salvarPais(PaisDTO $paisDTO): array
    {
        try
        {
            if ($paisDTO->id)
            {
                // Atualizar país existente
                $sql = "UPDATE paises SET nome = :nome WHERE id_pais = :id";
                $params = [
                    ':nome' => $paisDTO->nome,
                    ':id' => $paisDTO->id
                ];
                $this->db->execute($sql, $params);
                return ['success' => true, 'message' => 'País salvo com sucesso!', 'id' => $paisDTO->id];
            }
            else
            {
                // Verificar se o país já existe pelo nome antes de inserir
                $sqlCheck = "SELECT id_pais FROM paises WHERE nome = :nome";
                $existing = $this->db->query($sqlCheck, [':nome' => $paisDTO->nome]);

                if (!empty($existing))
                {
                    // País já existe, retorna o ID existente
                    return ['success' => true, 'message' => 'País já cadastrado.', 'id' => $existing[0]['id_pais']];
                }

                // Inserir novo país se não existir
                $sqlInsert = "INSERT INTO paises (nome) VALUES (:nome)";
                $this->db->execute($sqlInsert, [':nome' => $paisDTO->nome]);
                $newId = $this->db->lastInsertId();
                return ['success' => true, 'message' => 'País salvo com sucesso!', 'id' => $newId];
            }
        }
        catch (PDOException $e)
        {
            // Log do erro (opcional)
            error_log("Erro ao salvar país: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao salvar país: ' . $e->getMessage()];
        }
    }

    public function buscarTodos(): array
    {
        $sql = "SELECT id_pais, nome FROM paises ORDER BY nome";
        $result = $this->db->query($sql);
        $paises = [];
        foreach ($result as $row)
        {
            $paises[] = new PaisDTO($row['id_pais'], $row['nome']);
        }
        return $paises;
    }

    public function excluirPais(int $id): array
    {
        try
        {
            // Verificar se o país está sendo usado por algum autor
            $sqlCheck = "SELECT COUNT(*) FROM autores WHERE id_pais = :id";
            $count = $this->db->query($sqlCheck, [':id' => $id])[0]['COUNT(*)'];

            if ($count > 0)
            {
                return ['success' => false, 'message' => 'Não é possível excluir o país, pois há autores associados a ele.'];
            }

            $sql = "DELETE FROM paises WHERE id_pais = :id";
            $this->db->execute($sql, [':id' => $id]);
            return ['success' => true, 'message' => 'País excluído com sucesso!'];
        }
        catch (PDOException $e)
        {
            error_log("Erro ao excluir país: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao excluir país: ' . $e->getMessage()];
        }
    }
}

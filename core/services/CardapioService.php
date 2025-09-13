<?php
require_once BASE_PATH . 'core/services/database.php';
require_once BASE_PATH . 'core/models/CardapioDTO.php';

class CardapioService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * @return CardapioDTO[]
     */
    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM cardapios ORDER BY data DESC";
        $results = $this->db->query($sql);

        $cardapios = [];
        foreach ($results as $row)
        {
            $cardapios[] = CardapioDTO::fromArray($row);
        }
        return $cardapios;
    }

    /**
     * Busca o cardápio de hoje.
     * @return CardapioDTO|null
     */
    public function buscarDeHoje(): ?CardapioDTO
    {
        $sql = "SELECT * FROM cardapios WHERE data = CURDATE()";
        $result = $this->db->query($sql);

        if (count($result) > 0)
        {
            return CardapioDTO::fromArray($result[0]);
        }
        return null;
    }

    public function salvar(CardapioDTO $cardapio): array
    {
        try
        {
            if ($cardapio->id)
            {
                // Atualizar
                $sql = "UPDATE cardapios SET data = :data, lanche_manha = :lanche_manha, almoco = :almoco, lanche_tarde = :lanche_tarde WHERE id_cardapio = :id";
                $params = [
                    ':data' => $cardapio->data,
                    ':lanche_manha' => $cardapio->lanche_manha,
                    ':almoco' => $cardapio->almoco,
                    ':lanche_tarde' => $cardapio->lanche_tarde,
                    ':id' => $cardapio->id
                ];
            }
            else
            {
                // Inserir
                $sql = "INSERT INTO cardapios (data, lanche_manha, almoco, lanche_tarde) VALUES (:data, :lanche_manha, :almoco, :lanche_tarde)";
                $params = [
                    ':data' => $cardapio->data,
                    ':lanche_manha' => $cardapio->lanche_manha,
                    ':almoco' => $cardapio->almoco,
                    ':lanche_tarde' => $cardapio->lanche_tarde
                ];
            }

            if ($this->db->execute($sql, $params))
            {
                return ['success' => true, 'message' => 'Cardápio salvo com sucesso!'];
            }
            return ['success' => false, 'message' => 'Erro ao salvar o cardápio.'];
        }
        catch (PDOException $e)
        {
            if ($e->getCode() == '23000')
            { // SQLSTATE for integrity constraint violation
                return ['success' => false, 'message' => 'Erro: Já existe um cardápio cadastrado para esta data.'];
            }
            return ['success' => false, 'message' => 'Ocorreu um erro no banco de dados ao salvar o cardápio.'];
        }
    }

    public function excluir(int $id): array
    {
        $sql = "DELETE FROM cardapios WHERE id_cardapio = :id";
        if ($this->db->execute($sql, [':id' => $id]))
        {
            return ['success' => true, 'message' => 'Cardápio excluído com sucesso!'];
        }
        else
        {
            return ['success' => false, 'message' => 'Erro ao excluir o cardápio.'];
        }
    }
}

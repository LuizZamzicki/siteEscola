<?php
require_once BASE_PATH . 'core/models/LivroDTO.php';
require_once BASE_PATH . 'core/models/EmprestimoDTO.php';
require_once BASE_PATH . 'core/models/ReservaDTO.php';
require_once BASE_PATH . 'core/services/AutorService.php';
require_once BASE_PATH . 'core/services/EditoraService.php';
require_once BASE_PATH . 'core/services/ReservaService.php';
require_once BASE_PATH . 'core/services/GeneroService.php';
require_once BASE_PATH . 'core/services/database.php';

class BibliotecaService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // --- Livros ---
    public function buscarTodosLivros(): array
    {
        $sql = "
            SELECT
                l.id_livro as id,
                l.*,
                GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as autores_nomes,
                GROUP_CONCAT(DISTINCT g.descricao SEPARATOR ', ') as generos_nomes,
                e.nome as nome_editora
            FROM livros l
            LEFT JOIN autores a ON l.id_autor = a.id_autor
            LEFT JOIN rel_livros_generos rlg ON l.id_livro = rlg.id_livro
            LEFT JOIN generos_livros g ON rlg.id_genero = g.id_genero_livro
            LEFT JOIN editoras e ON l.id_editora = e.id_editora
            GROUP BY l.id_livro
            ORDER BY l.titulo
        ";
        $results = $this->db->query($sql);
        $livros = [];
        foreach ($results as $row)
        {
            $jsBook = new stdClass();
            foreach ($row as $key => $value)
            {
                $jsBook->$key = $value;
            }
            $jsBook->autores = $row['autores_nomes'] ? explode(', ', $row['autores_nomes']) : [];
            $jsBook->generos = $row['generos_nomes'] ? explode(', ', $row['generos_nomes']) : [];

            $livros[] = $jsBook;
        }
        return $livros;
    }

    public function buscarLeituraObrigatoriaPorTurma($idturma)
    {
        $sql = "
        SELECT 
            l.id_livro as id,
            l.*,
            GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as autores_nomes,
            GROUP_CONCAT(DISTINCT g.descricao SEPARATOR ', ') as generos_nomes,
            e.nome as nome_editora
        FROM livros l
        LEFT JOIN autores a ON l.id_autor = a.id_autor
        LEFT JOIN rel_livros_generos rlg ON l.id_livro = rlg.id_livro
        LEFT JOIN generos_livros g ON rlg.id_genero = g.id_genero_livro
        LEFT JOIN editoras e ON l.id_editora = e.id_editora
        JOIN leitura_obrigatoria lo ON l.id_livro = lo.id_livro
        WHERE lo.id_turma = :idturma
        GROUP BY l.id_livro
        ORDER BY l.titulo
    ";
        $results = $this->db->query($sql, [':idturma' => $idturma]);
        $livros = [];
        foreach ($results as $row)
        {
            $jsBook = new stdClass();
            foreach ($row as $key => $value)
            {
                $jsBook->$key = $value;
            }
            $jsBook->autores = $row['autores_nomes'] ? explode(', ', $row['autores_nomes']) : [];
            $jsBook->generos = $row['generos_nomes'] ? explode(', ', $row['generos_nomes']) : [];

            $livros[] = $jsBook;
        }
        return $livros;

    }

    public function buscarRecomendados(int $alunoId, int $limit = 5): array
    {
        // 1. Obter informações da turma do aluno para usar nos filtros
        $userInfo = $this->db->query("SELECT u.turma, t.id_turma FROM usuarios u LEFT JOIN turmas t ON u.turma = t.nome_turma WHERE u.id_usuario = :alunoId", [':alunoId' => $alunoId]);
        $turmaNome = $userInfo[0]['turma'] ?? null;
        $turmaId = !empty($userInfo[0]['id_turma']) ? (int)$userInfo[0]['id_turma'] : 0;

        // 2. Construir subqueries dinamicamente para flexibilidade
        $livrosInteragidosSql = "
            SELECT id_livro FROM emprestimos WHERE id_usuario = :alunoId
            UNION
            SELECT id_livro FROM reservas WHERE id_usuario = :alunoId AND status != 'Recusada'
        ";
        if ($turmaId > 0)
        {
            $livrosInteragidosSql .= " UNION SELECT id_livro FROM leitura_obrigatoria WHERE id_turma = :turmaId";
        }

        $livrosPopularesSql = "SELECT NULL as id_livro, 0 as popularidade WHERE 1=0"; // Padrão: query vazia
        if ($turmaNome)
        {
            $livrosPopularesSql = "
                SELECT e.id_livro, COUNT(DISTINCT e.id_usuario) as popularidade
                FROM emprestimos e
                JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE u.turma = :turmaNome AND e.id_usuario != :alunoId
                GROUP BY e.id_livro
            ";
        }

        // 3. Query principal para obter IDs de livros recomendados com pontuação
        $sql = "
            WITH 
            livros_interagidos AS ( $livrosInteragidosSql ),
            generos_preferidos AS ( 
                SELECT DISTINCT rlg.id_genero
                FROM emprestimos e
                JOIN rel_livros_generos rlg ON e.id_livro = rlg.id_livro
                WHERE e.id_usuario = :alunoId
            ),
            livros_populares AS ( $livrosPopularesSql )

            SELECT
                sb.id_livro,
                SUM(sb.score) as final_score,
                MAX(sb.popularidade) as final_popularity,
                l.nota_media
            FROM (
                -- Pontuação para livros populares na turma
                SELECT pop.id_livro, 2 as score, pop.popularidade FROM livros_populares pop
                UNION ALL
                -- Pontuação para livros de gêneros preferidos
                SELECT rlg.id_livro, 1 as score, 0 as popularidade FROM rel_livros_generos rlg WHERE rlg.id_genero IN (SELECT id_genero FROM generos_preferidos)
                UNION ALL
                -- Pontuação para livros bem avaliados (nota >= 4 e com mais de 2 avaliações)
                SELECT l.id_livro, 1.5 as score, 0 as popularidade FROM livros l WHERE l.nota_media >= 4.0 AND l.total_avaliacoes > 2
            ) as sb
            JOIN livros l ON sb.id_livro = l.id_livro
            WHERE 
                l.id_livro IS NOT NULL
                AND l.id_livro NOT IN (SELECT id_livro FROM livros_interagidos WHERE id_livro IS NOT NULL)
                AND l.qtde_disponivel > 0
            GROUP BY l.id_livro, l.nota_media
            ORDER BY final_score DESC, final_popularity DESC, l.nota_media DESC, RAND()
            LIMIT " . (int)$limit . "
        ";

        $params = [':alunoId' => $alunoId];
        if ($turmaId > 0)
            $params[':turmaId'] = $turmaId;
        if ($turmaNome)
            $params[':turmaNome'] = $turmaNome;

        $recommended_ids_rows = $this->db->query($sql, $params);

        // 4. Fallback: se não houver recomendações, sugerir livros populares em geral
        if (empty($recommended_ids_rows))
        {
            $fallbackSql = "
                SELECT l.id_livro
                FROM livros l
                LEFT JOIN (SELECT id_livro, COUNT(*) as total FROM emprestimos GROUP BY id_livro) as pop ON l.id_livro = pop.id_livro
                WHERE 
                    l.qtde_disponivel > 0
                    AND l.id_livro NOT IN (SELECT id_livro FROM ($livrosInteragidosSql) as li WHERE li.id_livro IS NOT NULL)
                ORDER BY l.nota_media DESC, COALESCE(pop.total, 0) DESC, RAND()
                LIMIT " . (int)$limit . "
            ";
            $recommended_ids_rows = $this->db->query($fallbackSql, $params); // $params no longer contains :limit
        }

        if (empty($recommended_ids_rows))
            return [];

        // 5. Buscar detalhes completos dos livros recomendados, mantendo a ordem da recomendação
        $recommended_ids = array_column($recommended_ids_rows, 'id_livro');
        $placeholders = implode(',', array_fill(0, count($recommended_ids), '?'));

        $sql_details = "
            SELECT 
                l.id_livro as id,
                l.*,
                GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as autores_nomes,
                GROUP_CONCAT(DISTINCT g.descricao SEPARATOR ', ') as generos_nomes,
                e.nome as nome_editora
            FROM livros l
            LEFT JOIN autores a ON l.id_autor = a.id_autor
            LEFT JOIN rel_livros_generos rlg ON l.id_livro = rlg.id_livro
            LEFT JOIN generos_livros g ON rlg.id_genero = g.id_genero_livro
            LEFT JOIN editoras e ON l.id_editora = e.id_editora
            WHERE l.id_livro IN ($placeholders)
            GROUP BY l.id_livro
            ORDER BY FIELD(l.id_livro, $placeholders)
        ";

        $results = $this->db->query($sql_details, array_merge($recommended_ids, $recommended_ids));

        return array_map(function ($row)
        {
            $jsBook = new stdClass();
            foreach ($row as $key => $value)
            {
                $jsBook->$key = $value;
            }
            $jsBook->autores = $row['autores_nomes'] ? explode(', ', $row['autores_nomes']) : [];
            $jsBook->generos = $row['generos_nomes'] ? explode(', ', $row['generos_nomes']) : [];
            return $jsBook;
        }, $results);
    }

    /**
     * Busca o histórico de livros que um aluno já leu (emprestou e devolveu).
     * @return array
     */
    public function buscarHistoricoDeLeituraPorAluno(int $alunoId): array
    {
        $sql = "
            SELECT DISTINCT
                l.id_livro as id,
                l.*,
                GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as autores_nomes,
                GROUP_CONCAT(DISTINCT g.descricao SEPARATOR ', ') as generos_nomes,
                e.nome as nome_editora,
                (SELECT av.nota FROM avaliacoes_livros av WHERE av.id_livro = l.id_livro AND av.id_usuario = :alunoId) as minha_nota
            FROM emprestimos emp
            JOIN livros l ON emp.id_livro = l.id_livro
            LEFT JOIN autores a ON l.id_autor = a.id_autor
            LEFT JOIN rel_livros_generos rlg ON l.id_livro = rlg.id_livro
            LEFT JOIN generos_livros g ON rlg.id_genero = g.id_genero_livro
            LEFT JOIN editoras e ON l.id_editora = e.id_editora
            WHERE emp.id_usuario = :alunoId AND emp.status = 'Devolvido'
            GROUP BY l.id_livro
            ORDER BY MAX(emp.data_devolucao_real) DESC
        ";

        $results = $this->db->query($sql, [':alunoId' => $alunoId]);

        return array_map(function ($row)
        {
            $jsBook = new stdClass();
            foreach ($row as $key => $value)
            {
                $jsBook->$key = $value;
            }
            $jsBook->autores = $row['autores_nomes'] ? explode(', ', $row['autores_nomes']) : [];
            $jsBook->generos = $row['generos_nomes'] ? explode(', ', $row['generos_nomes']) : [];
            return $jsBook;
        }, $results);
    }

    public function buscarLivroPorId(int $id): ?LivroDTO
    {
        $sql = "
            SELECT l.id_livro as id, l.*, e.nome as nome_editora
            FROM livros l
            LEFT JOIN editoras e ON l.id_editora = e.id_editora
            WHERE l.id_livro = :id
        ";
        $result = $this->db->query($sql, [':id' => $id]);
        if (empty($result))
        {
            return null;
        }
        $livro = LivroDTO::fromArray($result[0]);
        $livro->nome_editora = $result[0]['nome_editora'];
        return $livro;
    }

    public function salvarLivro(LivroDTO $livro, ?array $foto, ?int $autorId, ?int $editoraId, ?array $generoIds): bool
    {
        $urlFoto = $livro->url_foto; // Mantém a foto existente por padrão

        if (isset($foto) && $foto['error'] === UPLOAD_ERR_OK)
        {
            $uploadDir = BASE_PATH . 'uploads/livros/';
            if (!is_dir($uploadDir))
            {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '-' . basename($foto['name']);
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($foto['tmp_name'], $uploadFile))
            {
                $urlFoto = 'uploads/livros/' . $fileName;
            }
        }

        $this->db->beginTransaction();
        try
        {
            // --- Salvar ou atualizar livro ---
            if ($livro->id)
            {
                $sql = "UPDATE livros 
                    SET titulo = :titulo, subtitulo = :subtitulo, num_paginas = :num_paginas, id_autor = :id_autor, id_editora = :id_editora,
                        qtde_total = :qtde_total, url_foto = :url_foto, data_publicacao = :data_publicacao
                    WHERE id_livro = :id";
                $params = [
                    ':data_publicacao' => $livro->data_publicacao,
                    ':titulo' => $livro->titulo,
                    ':subtitulo' => $livro->subtitulo,
                    ':num_paginas' => $livro->num_paginas,
                    ':id_autor' => $autorId,
                    ':id_editora' => $editoraId,
                    ':qtde_total' => $livro->qtde_total,
                    ':url_foto' => $urlFoto,
                    ':id' => $livro->id
                ];
                $this->db->execute($sql, $params);
                $livroId = $livro->id;
            }
            else
            {   // Adiciona data_publicacao ao INSERT
                $sql = "INSERT INTO livros
                    (titulo, subtitulo, num_paginas, id_autor, id_editora, qtde_total, qtde_disponivel, url_foto, data_publicacao)
                    VALUES (:titulo, :subtitulo, :num_paginas, :id_autor, :id_editora, :qtde_total, :qtde_total, :url_foto, :data_publicacao)";
                $params = [
                    ':titulo' => $livro->titulo,
                    ':subtitulo' => $livro->subtitulo,
                    ':num_paginas' => $livro->num_paginas,
                    ':id_autor' => $autorId,
                    ':id_editora' => $editoraId,
                    ':qtde_total' => $livro->qtde_total,
                    ':url_foto' => $urlFoto,
                    ':data_publicacao' => $livro->data_publicacao
                ];
                $this->db->execute($sql, $params);
                $livroId = $this->db->lastInsertId();
            }

            // Limpa os gêneros antigos para garantir consistência
            $this->db->execute("DELETE FROM rel_livros_generos WHERE id_livro = :id_livro", [':id_livro' => $livroId]);

            // Insere os novos gêneros, se houver
            if (!empty($generoIds))
            {
                $sqlRel = "INSERT INTO rel_livros_generos (id_livro, id_genero) VALUES (:id_livro, :id_genero)";
                foreach ($generoIds as $generoId)
                {
                    $this->db->execute($sqlRel, [':id_livro' => $livroId, ':id_genero' => (int)$generoId]);
                }
            }

            $this->db->commit();
            return true;
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            // log do erro se quiser: error_log($e->getMessage());
            return false;
        }
    }


    public function excluirLivro(int $id, &$errorMessage): bool
    {
        $emprestimos = $this->db->query("SELECT COUNT(*) as count FROM emprestimos WHERE id_livro = :id AND status = 'Emprestado'", [':id' => $id])[0]['count'];
        if ($emprestimos > 0)
        {
            $errorMessage = "Não é possível excluir: livro possui empréstimos ativos.";
            return false;
        }
        return $this->db->execute("DELETE FROM livros WHERE id_livro = :id", [':id' => $id]);
    }

    // --- Empréstimos ---
    public function buscarEmprestimosAtivos(): array
    {
        $sql = "SELECT e.*, u.nome as nome_usuario, l.titulo as titulo_livro
                FROM emprestimos e
                JOIN usuarios u ON e.id_usuario = u.id_usuario
                JOIN livros l ON e.id_livro = l.id_livro
                WHERE e.status = 'Emprestado' ORDER BY e.data_devolucao_prevista ASC";
        return array_map([EmprestimoDTO::class, 'fromArray'], $this->db->query($sql));
    }

    public function devolverLivro(int $id_emprestimo): bool
    {
        $this->db->beginTransaction();
        try
        {
            $emprestimo = $this->db->query("SELECT id_livro FROM emprestimos WHERE id_emprestimo = :id", [':id' => $id_emprestimo])[0] ?? null;
            if (!$emprestimo)
                throw new Exception("Empréstimo não encontrado.");

            $this->db->execute("UPDATE emprestimos SET status = 'Devolvido', data_devolucao_real = CURDATE() WHERE id_emprestimo = :id", [':id' => $id_emprestimo]);
            $this->db->execute("UPDATE livros SET qtde_disponivel = qtde_disponivel + 1 WHERE id_livro = :id_livro", [':id_livro' => $emprestimo['id_livro']]);

            $this->db->commit();
            return true;
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            return false;
        }
    }

    public function estenderEmprestimo(int $id_emprestimo, int $dias = 15): ?string
    {
        // Primeiro, busca a data atual para poder calcular a nova
        $emprestimo = $this->db->selectOne("SELECT data_devolucao_prevista FROM emprestimos WHERE id_emprestimo = :id", [':id' => $id_emprestimo]);
        if (!$emprestimo)
        {
            return null;
        }

        // Calcula a nova data
        $novaData = (new DateTime($emprestimo->data_devolucao_prevista))->modify("+$dias days")->format('Y-m-d');

        // Atualiza no banco
        $sql = "UPDATE emprestimos SET data_devolucao_prevista = :nova_data WHERE id_emprestimo = :id";
        $success = $this->db->execute($sql, [':nova_data' => $novaData, ':id' => $id_emprestimo]);

        // Retorna a nova data em caso de sucesso, ou nulo em caso de falha
        return $success ? $novaData : null;
    }
}

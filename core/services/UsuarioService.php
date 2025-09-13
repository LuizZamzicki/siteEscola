<?php
require_once BASE_PATH . 'core/models/UsuarioDTO.php';
require_once BASE_PATH . 'core/services/database.php';

class UsuarioService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Busca todos os usuários que não são alunos.
     * @return UsuarioDTO[]
     */
    public function buscarTodosAdmins(): array
    {
        $sql = "SELECT * FROM usuarios WHERE tipo != 'Aluno' ORDER BY nome";
        $results = $this->db->query($sql);

        $usuarios = [];
        foreach ($results as $row)
        {
            $usuarios[] = UsuarioDTO::fromArray($row);
        }
        return $usuarios;
    }

    /**
     * Busca todos os usuários que são professores e suas turmas.
     * @return UsuarioDTO[]
     */
    public function buscarTodosProfessores(): array
    {
        $sql = "SELECT * FROM usuarios WHERE tipo = 'Professor' ORDER BY nome";
        $professoresData = $this->db->query($sql);

        $professores = [];
        foreach ($professoresData as $profData)
        {
            $professor = UsuarioDTO::fromArray($profData);

            // Buscar turmas associadas
            $sqlTurmas = "SELECT t.id_turma, t.nome_turma 
                          FROM turmas t
                          JOIN rel_professores_turmas rpt ON t.id_turma = rpt.id_turma
                          WHERE rpt.id_usuario = :id_usuario";
            $turmasData = $this->db->query($sqlTurmas, [':id_usuario' => $professor->id]);

            $professor->turmas = $turmasData; // Add turmas ao DTO
            $professores[] = $professor;
        }
        return $professores;
    }

    /**
     * Busca todos os usuários que são Alunos.
     * @return UsuarioDTO[]
     */
    public function buscarTodosAlunos(): array
    {
        $sql = "SELECT * FROM usuarios WHERE tipo = 'Aluno' ORDER BY nome";
        $results = $this->db->query($sql);

        $alunos = [];
        foreach ($results as $row)
        {
            $alunos[] = UsuarioDTO::fromArray($row);
        }
        return $alunos;
    }

    public function buscarUsuarioPorId(int $id): ?UsuarioDTO
    {
        $sql = "SELECT u.*, t.id_turma, t.nome_turma AS turma_nome 
                FROM usuarios u
                LEFT JOIN turmas t ON u.turma = t.nome_turma
                WHERE u.id_usuario = :id";
        $result = $this->db->query($sql, [':id' => $id]);

        if (empty($result))
        {
            return null;
        }

        return UsuarioDTO::fromArray($result[0]);
    }

    public function buscarUsuarioPorEmail(string $email): ?UsuarioDTO
    {
        $sql = "SELECT u.*, t.id_turma, t.nome_turma
                FROM usuarios u
                LEFT JOIN turmas t ON u.turma = t.nome_turma
                WHERE u.email = :email";
        $result = $this->db->query($sql, [':email' => $email]);

        if (empty($result))
        {
            return null;
        }

        return UsuarioDTO::fromArray($result[0]);
    }

    public function salvar(UsuarioDTO $usuario): bool
    {
        if ($usuario->id)
        {
            // Atualizar usuário existente
            if ($usuario->tipo === 'Aluno')
            {
                // Aluno: não pode mudar email, mas pode mudar nome e turma
                $sql = "UPDATE usuarios SET nome = :nome, turma = :turma WHERE id_usuario = :id AND tipo = 'Aluno'";
                $params = [
                    ':nome' => $usuario->nome,
                    ':turma' => $usuario->turma,
                    ':id' => $usuario->id
                ];
            }
            else
            {
                // Admin: pode mudar nome, email e tipo
                $sql = "UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo WHERE id_usuario = :id";
                $params = [
                    ':nome' => $usuario->nome,
                    ':email' => $usuario->email,
                    ':tipo' => $usuario->tipo,
                    ':id' => $usuario->id
                ];
            }
        }
        else
        {
            // Inserir novo usuário
            if ($usuario->tipo === 'Aluno')
            {
                $sql = "INSERT INTO usuarios (nome, email, tipo, status, turma) VALUES (:nome, :email, 'Aluno', :status, :turma)";
                $params = [
                    ':nome' => $usuario->nome,
                    ':email' => $usuario->email,
                    ':status' => $usuario->status ?? 'Ativo',
                    ':turma' => $usuario->turma
                ];
            }
            else
            {
                $sql = "INSERT INTO usuarios (nome, email, tipo, status) VALUES (:nome, :email, :tipo, 'Ativo')";
                $params = [
                    ':nome' => $usuario->nome,
                    ':email' => $usuario->email,
                    ':tipo' => $usuario->tipo
                ];
            }
        }
        return $this->db->execute($sql, $params);
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }

    public function desativar(int $id): bool
    {
        $sql = "UPDATE usuarios SET status = 'Inativo' WHERE id_usuario = :id";
        return $this->db->execute($sql, [':id' => $id]);
    }

    public function updateAvatarUrl(int $id, string $url): bool
    {
        $sql = "UPDATE usuarios SET url_img_perfil = :url WHERE id_usuario = :id";
        return $this->db->execute($sql, [':url' => $url, ':id' => $id]);
    }

    public function salvarProfessor(UsuarioDTO $usuario, array $turmasIds): bool
    {
        $this->db->beginTransaction();

        try
        {
            if ($usuario->id)
            {
                // Atualizar
                $sql = "UPDATE usuarios SET nome = :nome WHERE id_usuario = :id AND tipo = 'Professor'";
                $params = [':nome' => $usuario->nome, ':id' => $usuario->id];
                $this->db->execute($sql, $params);
                $usuarioId = $usuario->id;
            }
            else
            {
                // Inserir
                $sql = "INSERT INTO usuarios (nome, email, tipo, status) VALUES (:nome, :email, 'Professor', 'Pendente')";
                $params = [':nome' => $usuario->nome, ':email' => $usuario->email];
                $this->db->execute($sql, $params);
                $usuarioId = $this->db->lastInsertId();
            }

            // Atualizar relacionamentos em rel_professores_turmas
            $this->db->execute("DELETE FROM rel_professores_turmas WHERE id_usuario = :id_usuario", [':id_usuario' => $usuarioId]);

            if (!empty($turmasIds))
            {
                $sqlRel = "INSERT INTO rel_professores_turmas (id_usuario, id_turma) VALUES (:id_usuario, :id_turma)";
                foreach ($turmasIds as $turmaId)
                {
                    $this->db->execute($sqlRel, [':id_usuario' => $usuarioId, ':id_turma' => (int)$turmaId]);
                }
            }

            $this->db->commit();
            return true;
        }
        catch (Exception $e)
        {
            $this->db->rollBack();
            return false;
        }
    }
}
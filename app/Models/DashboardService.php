<?php
use Core\Database;
use App\Models\DTO\AvisoDTO;
use App\Models\DTO\AtividadeRecenteDTO;
use App\Models\DTO\EventoCalendarioDTO;

class DashboardService
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getDashboardStats(): array
    {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM usuarios WHERE tipo = 'Aluno' AND status = 'Ativo') as total_alunos,
                (SELECT COUNT(*) FROM emprestimos WHERE status = 'Emprestado') as livros_emprestados,
                (SELECT COUNT(*) FROM reservas WHERE status = 'Pendente') as reservas_ativas,
                (SELECT COUNT(*) FROM usuarios WHERE tipo = 'Professor' AND status = 'Ativo') as total_professores
        ";
        $result = $this->db->query($sql);
        return $result[0] ?? [
            'total_alunos' => 0,
            'livros_emprestados' => 0,
            'reservas_ativas' => 0,
            'total_professores' => 0
        ];
    }

    public function getAtividadesRecentes(int $limit = 5): array
    {
        $sql = "
            (SELECT
                'reserva' as tipo,
                u.nome as autor,
                CONCAT('reservou o livro <strong>', l.titulo, '</strong>') as descricao,
                r.data_reserva as data,
                u.url_img_perfil as avatar_url
            FROM reservas r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            JOIN livros l ON r.id_livro = l.id_livro
            WHERE r.status = 'Pendente')

            UNION ALL

            (SELECT
                'aviso' as tipo,
                'Você' as autor,
                CONCAT('publicou um novo aviso: <strong>', a.titulo, '</strong>') as descricao,
                a.data_postagem as data,
                'https://placehold.co/40x40/d8b4fe/ffffff?text=A' as avatar_url
            FROM avisos a)

            ORDER BY data DESC
            LIMIT " . (int)$limit . "
        ";

        // A cláusula LIMIT não funciona bem com parâmetros vinculados no PDO, pois eles podem ser tratados como strings.
        // Inserir o valor como um inteiro diretamente na query é a abordagem mais segura e compatível.
        $results = $this->db->query($sql);
        $atividades = [];
        foreach ($results as $row)
        {
            $atividades[] = AtividadeRecenteDTO::fromArray($row);
        }
        return $atividades;
    }

    public function getAvisosRecentes(int $limit = 3): array
    {
        $sql = "SELECT * FROM avisos ORDER BY data_postagem DESC LIMIT " . (int)$limit;
        $results = $this->db->query($sql);

        $avisos = [];
        foreach ($results as $row)
        {
            $avisos[] = AvisoDTO::fromArray($row);
        }
        return $avisos;
    }

    public function getProximosEventos(int $dias = 7): array
    {
        $sql = "
            SELECT * FROM eventos_calendario 
            WHERE data_inicio >= CURDATE() 
              AND data_inicio <= DATE_ADD(CURDATE(), INTERVAL :dias DAY)
            ORDER BY data_inicio ASC
        ";
        $results = $this->db->query($sql, [':dias' => $dias]);

        $eventos = [];
        foreach ($results as $row)
        {
            $eventos[] = EventoCalendarioDTO::fromArray($row);
        }
        return $eventos;
    }
}
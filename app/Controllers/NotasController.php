<?php

use Core\View;

class NotasController
{
    public function index()
    {
        // Exemplo: buscar notas do aluno autenticado (substitua por integração real quando disponível)
        $notas = [
            "1" => [
                ['subject' => 'Matemática', 'n1' => 8.5, 'n2' => 9.0, 'absences' => 2],
                ['subject' => 'Português', 'n1' => 7.0, 'n2' => 8.0, 'absences' => 1],
                ['subject' => 'História', 'n1' => 9.5, 'n2' => 9.0, 'absences' => 0],
                ['subject' => 'Geografia', 'n1' => 8.0, 'n2' => 7.5, 'absences' => 1],
                ['subject' => 'Ciências', 'n1' => 9.0, 'n2' => 9.5, 'absences' => 0],
                ['subject' => 'Artes', 'n1' => 10.0, 'n2' => 9.5, 'absences' => 0],
                ['subject' => 'Ed. Física', 'n1' => 10.0, 'n2' => 10.0, 'absences' => 0],
            ],
            "2" => [
                ['subject' => 'Matemática', 'n1' => 7.5, 'n2' => 8.0, 'absences' => 1],
                ['subject' => 'Português', 'n1' => 8.0, 'n2' => 8.5, 'absences' => 0],
                ['subject' => 'História', 'n1' => 8.5, 'n2' => 9.0, 'absences' => 1],
                ['subject' => 'Geografia', 'n1' => 9.0, 'n2' => 8.0, 'absences' => 0],
                ['subject' => 'Ciências', 'n1' => 8.0, 'n2' => 8.5, 'absences' => 2],
                ['subject' => 'Artes', 'n1' => 9.0, 'n2' => 9.0, 'absences' => 0],
                ['subject' => 'Ed. Física', 'n1' => 10.0, 'n2' => 10.0, 'absences' => 0],
            ],
            "3" => [
                ['subject' => 'Matemática', 'n1' => 6.0, 'n2' => 7.5, 'absences' => 3],
                ['subject' => 'Português', 'n1' => 7.5, 'n2' => 7.0, 'absences' => 1],
                ['subject' => 'História', 'n1' => 8.0, 'n2' => 8.0, 'absences' => 0],
                ['subject' => 'Geografia', 'n1' => 7.0, 'n2' => 7.5, 'absences' => 2],
                ['subject' => 'Ciências', 'n1' => 7.5, 'n2' => 8.0, 'absences' => 1],
                ['subject' => 'Artes', 'n1' => 8.5, 'n2' => 9.0, 'absences' => 0],
                ['subject' => 'Ed. Física', 'n1' => 10.0, 'n2' => 10.0, 'absences' => 0],
            ],
            "4" => []
        ];
        View::render('Aluno/notas', ['notas' => $notas]);
    }
}

<?php

namespace App\Models;

class FuncionarioService
{
    public function getFuncionarios()
    {
        // Carrega o array de funcionários do arquivo de dados
        $funcionarios = require BASE_PATH . 'app/Data/funcionariosObj.php';
        return $funcionarios;
    }

    public function getDepartamentosUnicos(array $funcionarios): array
    {
        $departamentos = [];
        foreach ($funcionarios as $funcionario)
        {
            if (!empty($funcionario['departamento']))
            {
                $departamentos[$funcionario['departamento']] = $funcionario['departamento'];
            }
        }
        ksort($departamentos);
        return $departamentos;
    }

    public function filtrarPorDepartamento(array $funcionarios, string $departamento): array
    {
        if ($departamento === 'todos')
        {
            return $funcionarios;
        }
        return array_filter($funcionarios, function ($f) use ($departamento)
        {
            return ($f['departamento'] ?? '') === $departamento;
        });
    }
}

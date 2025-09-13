<?php
require_once BASE_PATH . 'core/services/UsuarioService.php';

$usuarioService = new UsuarioService();
$aluno = null;
$errorMessage = '';

if (isset($_SESSION['user_id']))
{
    // Assumindo que o serviço pode buscar todos os detalhes necessários do aluno.
    $aluno = $usuarioService->buscarUsuarioPorId($_SESSION['user_id']);
    if (!$aluno)
    {
        $errorMessage = 'Não foi possível carregar os dados do aluno.';
    }
}
else
{
    $errorMessage = 'Sessão de usuário não encontrada. Por favor, faça login novamente.';
}


?>

<div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
    <h2 class="text-2xl font-semibold">Meu Perfil</h2>
    <?php if ($errorMessage): ?>
        <div class="mt-6 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">
            <p><?= htmlspecialchars($errorMessage) ?></p>
        </div>
    <?php else: ?>
        <div class="mt-6 border-t border-slate-200 pt-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <p class="text-sm font-medium text-slate-500">Nome Completo</p>
                <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($aluno->nome ?? 'Não informado') ?></p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Turma</p>
                <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($aluno->turma_nome ?? 'Não enturmado') ?>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">E-mail</p>
                <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($aluno->email ?? 'Não informado') ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
require_once BASE_PATH . 'core/services/UsuarioService.php';
require_once BASE_PATH . 'core/services/ComentarioService.php';

$usuarioService = new UsuarioService();
$comentarioService = new ComentarioService();
$aluno = null;
$meuComentario = null;
$errorMessage = '';
$feedbackMessage = '';
$feedbackType = 'success';

if (isset($_SESSION['user_id']))
{
    $alunoId = $_SESSION['user_id'];

    // Lida com o envio do formulário de comentário
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'salvar_comentario')
    {
        $comentarioTexto = trim($_POST['testimonial-text'] ?? '');
        if (!empty($comentarioTexto))
        {
            if ($comentarioService->salvar($alunoId, $comentarioTexto))
            {
                $feedbackMessage = 'Seu comentário foi enviado para análise. Obrigado!';
            }
            else
            {
                $feedbackMessage = 'Ocorreu um erro ao enviar seu comentário. Tente novamente.';
                $feedbackType = 'error';
            }
        }
        else
        {
            $feedbackMessage = 'O campo de comentário não pode estar vazio.';
            $feedbackType = 'error';
        }
        // Redireciona para evitar reenvio do formulário
        $redirectUrl = strtok($_SERVER['REQUEST_URI'], '?') . '?param=perfil_aluno&msg=' . urlencode($feedbackMessage) . '&type=' . $feedbackType;
        header("Location: " . $redirectUrl);
        exit();
    }

    // Busca dados para exibição na página
    $feedbackMessage = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
    $feedbackType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success';
    $aluno = $usuarioService->buscarUsuarioPorId($alunoId);
    $meuComentario = $comentarioService->buscarPorAluno($alunoId);

    if (!$aluno)
    {
        $errorMessage = 'Não foi possível carregar os dados do aluno.';
    }
}
?>

<div class="space-y-6">
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
                    <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($aluno->nome ?? 'Não informado') ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Turma</p>
                    <p class="mt-1 font-semibold text-slate-800">
                        <?= htmlspecialchars($aluno->turma_nome ?? 'Não enturmado') ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">E-mail</p>
                    <p class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($aluno->email ?? 'Não informado') ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Seção de Comentário sobre a Escola -->
    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200">
        <h2 class="text-2xl font-semibold mb-4">Seu Comentário sobre a Escola</h2>
        <p class="text-slate-600 mb-6">Sua opinião é muito importante para nós! Deixe um comentário sobre sua
            experiência no Colégio Maffei. Os comentários selecionados poderão ser exibidos em nosso site.</p>

        <?php if ($feedbackMessage && !$errorMessage): ?>
            <div class="mb-4 <?= $feedbackType === 'success' ? 'bg-green-100 border-green-200 text-green-800' : 'bg-red-100 border-red-200 text-red-800' ?> border px-4 py-3 rounded-lg"
                role="alert">
                <span class="block sm:inline"><?= $feedbackMessage ?></span>
            </div>
        <?php endif; ?>

        <?php if ($meuComentario): ?>
            <div class="border-t border-slate-200 pt-6">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold text-slate-800">Seu comentário enviado:</h3>
                    <?php
                    $statusClasses = [
                        'pendente' => 'bg-amber-100 text-amber-800',
                        'aprovado' => 'bg-green-100 text-green-800',
                        'rejeitado' => 'bg-red-100 text-red-800',
                    ];
                    $statusClass = $statusClasses[$meuComentario['status']] ?? 'bg-slate-100 text-slate-800';
                    ?>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                        Status: <?= htmlspecialchars(ucfirst($meuComentario['status'])) ?>
                    </span>
                </div>
                <blockquote class="border-l-4 border-purple-300 pl-4 italic text-slate-600 bg-slate-50 p-4 rounded-r-lg">
                    <?= htmlspecialchars($meuComentario['comentario']) ?>
                </blockquote>
                <p class="text-sm text-slate-500 mt-4">Você pode editar e reenviar seu comentário a qualquer momento usando
                    o formulário abaixo.</p>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-6">
            <input type="hidden" name="action" value="salvar_comentario">
            <div>
                <label for="testimonial-text" class="sr-only">Seu comentário</label>
                <textarea id="testimonial-text" name="testimonial-text" rows="5"
                    class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm"
                    placeholder="Ex: O ensino integral mudou minha forma de ver a escola! As oficinas são incríveis..."><?= htmlspecialchars($meuComentario['comentario'] ?? '') ?></textarea>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    <span><?= $meuComentario ? 'Atualizar Comentário' : 'Enviar Comentário' ?></span>
                </button>
            </div>
        </form>
    </div>
</div>
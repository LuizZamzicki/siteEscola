<?php
// A view espera receber $fullSchedule e $turmaNome já preparados pelo controller.
?>

<script>
    // Garante que o objeto global studentData exista e injeta os dados do horário.
    var studentData = window.studentData || {};
    studentData.schedule = studentData.schedule || {};
    studentData.schedule.full = <?= json_encode($fullSchedule, JSON_UNESCAPED_UNICODE) ?>;
</script>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <h2 class="text-2xl font-semibold">Horário de Aulas -
        <?= htmlspecialchars($turmaNome) ?>
    </h2>
    <p class="text-slate-600 mt-1">Confira sua grade horária completa da semana.</p>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-center border-collapse">
            <thead class="border-b-2 border-slate-200">
                <tr>
                    <th class="p-3 text-sm font-semibold tracking-wide w-32">Horário</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Segunda</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Terça</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Quarta</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Quinta</th>
                    <th class="p-3 text-sm font-semibold tracking-wide">Sexta</th>
                </tr>
            </thead>
            <tbody id="schedule-table-body">
                <!-- O horário será preenchido pelo JavaScript -->
            </tbody>
        </table>
    </div>
</div>
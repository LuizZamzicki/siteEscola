<?php
// A view espera receber os dados de notas do controller.
?>
<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <h2 class="text-2xl font-semibold">Desempenho Acadêmico</h2>
    <p class="text-slate-600 mt-1">Acompanhe suas notas e faltas por bimestre. Selecione um bimestre
        para ver os detalhes e um gráfico comparativo.</p>

    <div class="mt-6 bg-ambern-50 border-l-4 border-amber-400 text-amber-800 p-4 rounded-r-lg" role="alert">
        <div class="flex">
            <div class="py-1"><i class="fa-solid fa-triangle-exclamation mr-4 text-amber-500 text-xl"></i></div>
            <div>
                <p class="font-bold">Funcionalidade em Desenvolvimento</p>
                <p class="text-sm">A integração real de notas e faltas depende de acesso a uma API externa (SEED/PR),
                    que ainda está pendente. Os dados exibidos abaixo são apenas um <strong>exemplo estático</strong>
                    para demonstrar como a tela funcionará no futuro.</p>
            </div>
        </div>
    </div>

    <div class="border-b border-slate-200 mt-6">
        <div class="overflow-x-auto">
            <nav class="-mb-px flex space-x-6" id="bimester-tabs">
                <button data-bimester="1"
                    class="bimester-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-purple-600 border-purple-500">1º
                    Bimestre</button>
                <button data-bimester="2"
                    class="bimester-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-slate-500 border-transparent hover:text-slate-700 hover:border-slate-300">2º
                    Bimestre</button>
                <button data-bimester="3"
                    class="bimester-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-slate-500 border-transparent hover:text-slate-700 hover:border-slate-300">3º
                    Bimestre</button>
                <button data-bimester="4"
                    class="bimester-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-slate-500 border-transparent hover:text-slate-700 hover:border-slate-300">4º
                    Bimestre</button>
            </nav>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4" id="grades-chart-title">Notas do 1º Bimestre</h3>
        <div class="chart-container">
            <canvas id="gradesChart"></canvas>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-slate-300">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-0">
                                Matéria</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                Nota 1</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                Nota 2</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                Média</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                Faltas</th>
                        </tr>
                    </thead>
                    <tbody id="grades-table-body" class="divide-y divide-slate-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
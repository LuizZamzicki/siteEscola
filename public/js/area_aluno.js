document.addEventListener("DOMContentLoaded", function () {
  // A variável `studentData` é injetada pelo PHP em cada página da área do aluno.
  // A lógica da barra lateral foi centralizada em /Widgets/barra_lateral/barra_lateral.js

  // --- Lógica de Horário ---
  const scheduleTableBody = document.getElementById("schedule-table-body");
  const todayScheduleList = document.getElementById("today-schedule");
  const subjectColors = {
    Matemática: "bg-blue-50 text-blue-800",
    Português: "bg-red-50 text-red-800",
    História: "bg-green-50 text-green-800",
    Geografia: "bg-yellow-50 text-yellow-800",
    Ciências: "bg-indigo-50 text-indigo-800",
    Artes: "bg-pink-50 text-pink-800",
    "Ed. Física": "bg-orange-50 text-orange-800",
    Redação: "bg-rose-50 text-rose-800",
    Inglês: "bg-sky-50 text-sky-800",
    Filosofia: "bg-purple-50 text-purple-800",
    Intervalo: "bg-slate-100 text-slate-600",
  };

  const eventColors = {
    reuniao: "bg-red-100 text-red-700",
    feriado: "bg-green-100 text-green-700",
    ferias: "bg-orange-100 text-orange-700",
    prova: "bg-blue-100 text-blue-700",
    evento: "bg-purple-100 text-purple-700",
  };

  function renderFullSchedule() {
    if (!scheduleTableBody) return;
    if (
      typeof studentData?.schedule?.full === "undefined" ||
      studentData.schedule.full.length === 0
    ) {
      scheduleTableBody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-slate-500">Nenhum horário cadastrado para sua turma.</td></tr>`;
      return;
    }

    scheduleTableBody.innerHTML = ""; // Limpa a tabela
    const scheduleData = studentData.schedule.full;
    const days = ["Segunda", "Terça", "Quarta", "Quinta", "Sexta"];

    scheduleData.forEach((row_data) => {
      const row = document.createElement("tr");
      if (row_data.type) {
        // Interval
        row.innerHTML = `<td class="p-3 text-sm font-bold">${row_data.time}</td><td colspan="5" class="p-3 text-sm font-semibold ${subjectColors.Intervalo}">${row_data.type}</td>`;
      } else {
        const timeCell = `<td class="p-3 text-sm font-semibold">${row_data.time}</td>`;
        const subjectCells = days
          .map((day) => {
            const subject = row_data[day];
            return `<td class="p-3 text-sm font-medium ${
              subjectColors[subject] || ""
            }">${subject || ""}</td>`;
          })
          .join("");
        row.innerHTML = timeCell + subjectCells;
      }
      scheduleTableBody.appendChild(row);
    });
  }

  function renderTodaySchedule() {
    if (!todayScheduleList) return;
    if (
      typeof studentData?.schedule?.today === "undefined" ||
      studentData.schedule.today.length === 0
    ) {
      todayScheduleList.innerHTML =
        '<li class="text-center text-slate-500 py-4">Sem aulas hoje.</li>';
      return;
    }

    todayScheduleList.innerHTML = ""; // Limpa a lista
    const todayScheduleData = studentData.schedule.today;

    todayScheduleData.forEach((item) => {
      const subject = item.subject;
      todayScheduleList.innerHTML += `<li class="flex items-center justify-between p-3 ${
        subjectColors[subject] || ""
      } rounded-lg"><span class="font-medium">${
        item.time
      }</span><span class="font-semibold">${subject}</span></li>`;
    });
  }

  // --- Inicialização ---
  function initializeApp() {
    // Verifica se studentData existe antes de inicializar os componentes
    if (typeof studentData === "undefined") {
      console.error(
        "Objeto studentData não encontrado. A inicialização dos componentes foi abortada."
      );
      return;
    }

    // Inicializa componentes de cada página
    renderFullSchedule();
    renderTodaySchedule();
  }

  initializeApp();
});

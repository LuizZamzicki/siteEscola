document.addEventListener("DOMContentLoaded", function () {
  // A variável `studentData` é injetada pelo PHP.
  if (typeof studentData === "undefined" || !studentData.grades) {
    // Não executa a lógica de notas se os dados não estiverem presentes.
    return;
  }

  let gradesChart;
  const gradesChartCanvas = document.getElementById("gradesChart");
  const gradesTableBody = document.getElementById("grades-table-body");
  const bimesterTabs = document.querySelectorAll(".bimester-tab");
  const gradesChartTitle = document.getElementById("grades-chart-title");

  function getGradeColor(grade) {
    if (grade >= 9) return "bg-emerald-100 text-emerald-800";
    if (grade >= 7) return "bg-blue-100 text-blue-800";
    if (grade >= 5) return "bg-amber-100 text-amber-800";
    return "bg-red-100 text-red-800";
  }

  function updateGradesView(bimester) {
    // Defensive check: only run if grades data and elements are on the page.
    if (!studentData?.grades || !gradesChartTitle || !gradesTableBody) {
      return;
    }

    const data = studentData.grades[bimester] || [];
    gradesChartTitle.textContent = `Notas do ${bimester}º Bimestre`;
    gradesTableBody.innerHTML = "";

    if (gradesChart) {
      gradesChart.destroy();
    }

    if (data.length === 0) {
      gradesTableBody.innerHTML =
        '<tr><td colspan="5" class="text-center py-4 text-slate-500">Dados ainda não disponíveis.</td></tr>';
    } else {
      data.forEach((item) => {
        const average = ((item.n1 + item.n2) / 2).toFixed(1);
        const row = `
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-0">${
                          item.subject
                        }</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">${item.n1.toFixed(
                          1
                        )}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">${item.n2.toFixed(
                          1
                        )}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold"><span class="px-2 py-1 rounded-full ${getGradeColor(
                          average
                        )}">${average}</span></td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">${
                          item.absences
                        }</td>
                    </tr>
                `;
        gradesTableBody.insertAdjacentHTML("beforeend", row);
      });

      if (gradesChartCanvas) {
        const chartLabels = data.map((item) => item.subject);
        const chartData = data.map((item) => (item.n1 + item.n2) / 2);

        gradesChart = new Chart(gradesChartCanvas, {
          type: "bar",
          data: {
            labels: chartLabels,
            datasets: [
              {
                label: "Média Bimestral",
                data: chartData,
                backgroundColor: "rgba(124, 58, 237, 0.6)",
                borderColor: "rgba(124, 58, 237, 1)",
                borderWidth: 1,
                borderRadius: 6,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: { beginAtZero: true, max: 10, grid: { color: "#e2e8f0" } },
              x: { grid: { display: false } },
            },
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: { label: (c) => `Média: ${c.raw.toFixed(1)}` },
              },
            },
          },
        });
      }
    }
  }

  if (bimesterTabs.length > 0) {
    bimesterTabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        bimesterTabs.forEach((t) => {
          t.classList.remove("text-purple-600", "border-purple-500");
          t.classList.add(
            "text-slate-500",
            "border-transparent",
            "hover:text-slate-700",
            "hover:border-slate-300"
          );
        });
        tab.classList.add("text-purple-600", "border-purple-500");
        tab.classList.remove(
          "text-slate-500",
          "border-transparent",
          "hover:text-slate-700",
          "hover:border-slate-300"
        );
        updateGradesView(tab.dataset.bimester);
      });
    });

    // Initial call to render the first bimester's data
    updateGradesView("1");
  }
});

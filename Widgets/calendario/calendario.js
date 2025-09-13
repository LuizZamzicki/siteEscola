class Calendario {
  constructor(config) {
    this.config = {
      containerId: "calendar-container",
      titleId: "calendar-title",
      prevBtnId: "prev-month-btn",
      nextBtnId: "next-month-btn",
      gridId: "calendar-grid",
      listId: "calendar-list",
      isAdmin: false,
      dbEvents: [],
      onEventClick: (event) => {
        console.log("Event clicked:", event);
      },
      onDayClick: (date, events) => {
        console.log("Day clicked:", date, events);
      },
      ...config,
    };

    this.elements = {
      container: document.getElementById(this.config.containerId),
      title: document.getElementById(this.config.titleId),
      prevBtn: document.getElementById(this.config.prevBtnId),
      nextBtn: document.getElementById(this.config.nextBtnId),
      grid: document.getElementById(this.config.gridId),
      list: document.getElementById(this.config.listId),
    };

    if (!this.elements.container || !this.elements.grid) {
      console.error(
        "Elementos essenciais do calendário não encontrados. Verifique os IDs."
      );
      return;
    }

    this.currentDate = new Date();
    this.currentDate.setDate(1);

    this.eventColors = {
      reuniao: "bg-red-100 text-red-700",
      feriado: "bg-green-100 text-green-700",
      ferias: "bg-orange-100 text-orange-700",
      prova: "bg-blue-100 text-blue-700",
      evento: "bg-purple-100 text-purple-700",
    };

    this.staticEvents = [
      {
        id: "h1",
        day: 1,
        month: 1,
        title: "Confraternização",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h2",
        day: 21,
        month: 4,
        title: "Tiradentes",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h3",
        day: 1,
        month: 5,
        title: "Dia do Trabalho",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h4",
        day: 7,
        month: 9,
        title: "Independência",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h5",
        day: 12,
        month: 10,
        title: "N. Sra. Aparecida",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h6",
        day: 2,
        month: 11,
        title: "Finados",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h7",
        day: 15,
        month: 11,
        title: "Procl. da República",
        type: "feriado",
        recurring: true,
      },
      {
        id: "h8",
        day: 25,
        month: 12,
        title: "Natal",
        type: "feriado",
        recurring: true,
      },
      { id: "h9", date: "2025-03-04", title: "Carnaval", type: "feriado" },
      {
        id: "h10",
        date: "2025-04-18",
        title: "Sexta-feira Santa",
        type: "feriado",
      },
      {
        id: "h11",
        date: "2025-06-19",
        title: "Corpus Christi",
        type: "feriado",
      },
    ];

    this.init();
  }

  init() {
    this.bindEvents();
    this.render();
  }

  processDbEvents() {
    return (this.config.dbEvents || []).map((evento) => {
      const [year, month, day] = evento.data_inicio.split("-").map(Number);
      return {
        id: evento.id,
        title: evento.titulo,
        date: evento.recorrente ? null : evento.data_inicio,
        startDate: evento.tipo === "ferias" ? evento.data_inicio : null,
        endDate: evento.tipo === "ferias" ? evento.data_fim : null,
        day: evento.recorrente ? day : null,
        month: evento.recorrente ? month : null,
        type: evento.tipo,
        target: evento.publico_alvo,
        recurring: evento.recorrente,
        isDbEvent: true, // Flag para identificar eventos do banco
      };
    });
  }

  getAllEvents() {
    return [...this.staticEvents, ...this.processDbEvents()];
  }

  bindEvents() {
    this.elements.prevBtn.addEventListener("click", () => this.changeMonth(-1));
    this.elements.nextBtn.addEventListener("click", () => this.changeMonth(1));

    this.elements.container.addEventListener("click", (e) => {
      const eventEl = e.target.closest(".calendar-event");
      const dayEl = e.target.closest(".calendar-day");

      if (eventEl) {
        e.stopPropagation(); // Impede que o clique no evento dispare o clique no dia
        const eventId = eventEl.dataset.eventId;
        const isDbEvent = eventEl.dataset.isDbEvent === "true";
        if (this.config.isAdmin && isDbEvent) {
          this.config.onEventClick(eventId);
        } else {
          // Para alunos, ou eventos não editáveis, o comportamento é o mesmo que clicar no dia
          if (dayEl) this.handleDayClick(dayEl);
        }
      } else if (dayEl) {
        this.handleDayClick(dayEl);
      }
    });
  }

  handleDayClick(dayEl) {
    const dateStr = dayEl.dataset.date;
    if (!dateStr) return;

    const allEvents = this.getAllEvents();
    const dayEvents = this.getEventsForDate(dateStr, allEvents);

    if (dayEvents.length > 0) {
      this.config.onDayClick(dateStr, dayEvents);
    }
  }

  changeMonth(direction) {
    this.currentDate.setMonth(this.currentDate.getMonth() + direction);
    this.render();
  }

  getEventsForDate(dateStr, allEvents) {
    const [year, month, day] = dateStr.split("-").map(Number);
    const currentDateObj = new Date(dateStr + "T00:00:00");

    const nonRecurring = allEvents.filter(
      (e) => e.date === dateStr && !e.recurring
    );
    const recurring = allEvents.filter(
      (e) => e.recurring && e.day === day && e.month === month
    );
    const range = allEvents.filter((e) => {
      if (e.type !== "ferias" || !e.startDate) return false;
      const start = new Date(e.startDate + "T00:00:00");
      const end = e.endDate ? new Date(e.endDate + "T00:00:00") : start;
      return currentDateObj >= start && currentDateObj <= end;
    });

    return [...nonRecurring, ...recurring, ...range];
  }

  render() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();

    this.elements.title.textContent = new Date(year, month)
      .toLocaleString("pt-BR", {
        month: "long",
        year: "numeric",
      })
      .replace(/^\w/, (c) => c.toUpperCase());

    this.elements.grid.innerHTML = "";
    if (this.elements.list) this.elements.list.innerHTML = "";

    const allEvents = this.getAllEvents();

    this.renderGridView(year, month, allEvents);

    if (this.config.isAdmin && this.elements.list) {
      this.renderListView(year, month, allEvents);
    }
  }

  renderGridView(year, month, allEvents) {
    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    // Dias do mês anterior
    for (let i = firstDayOfMonth; i > 0; i--) {
      this.elements.grid.insertAdjacentHTML(
        "beforeend",
        `<div class="bg-slate-50 text-slate-400 p-2 h-28 flex items-start justify-end"><span class="text-sm">${
          daysInPrevMonth - i + 1
        }</span></div>`
      );
    }

    // Dias do mês atual
    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(
        day
      ).padStart(2, "0")}`;
      const today = new Date();
      const isToday =
        day === today.getDate() &&
        month === today.getMonth() &&
        year === today.getFullYear();

      const dayEvents = this.getEventsForDate(dateStr, allEvents);
      const hasEvents = dayEvents.length > 0;

      let eventsHtml = "";
      const maxEventsToShow = this.config.isAdmin ? 100 : 2; // Mostrar mais no admin se couber

      eventsHtml = dayEvents
        .slice(0, maxEventsToShow)
        .map(
          (event) => `
                <div class="calendar-event mt-1 text-xs p-1 rounded ${
                  this.eventColors[event.type] || "bg-slate-100"
                } cursor-pointer fade-truncate"
                    data-event-id="${event.id}" 
                    data-is-db-event="${!!event.isDbEvent}"
                    title="${event.title}">
                    ${event.title}
                </div>
            `
        )
        .join("");

      if (dayEvents.length > maxEventsToShow) {
        eventsHtml += `<div class="mt-1 text-xs text-purple-600 font-semibold cursor-pointer">+${
          dayEvents.length - maxEventsToShow
        } mais</div>`;
      }

      const dayNumberClass = isToday
        ? "bg-purple-600 text-white rounded-full w-6 h-6 flex items-center justify-center"
        : "text-slate-800";
      const dayCellClasses = `calendar-day p-1 h-28 border-t bg-white flex flex-col ${
        hasEvents ? "cursor-pointer hover:bg-slate-50 transition-colors" : ""
      }`;

      this.elements.grid.insertAdjacentHTML(
        "beforeend",
        `
                <div class="${dayCellClasses}" data-date="${dateStr}">
                    <span class="font-semibold ${dayNumberClass}">${day}</span>
                    <div class="overflow-hidden">${eventsHtml}</div>
                </div>
            `
      );
    }

    // Dias do próximo mês
    const totalCells = firstDayOfMonth + daysInMonth;
    const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let i = 1; i <= remainingCells; i++) {
      this.elements.grid.insertAdjacentHTML(
        "beforeend",
        `<div class="bg-slate-50 text-slate-400 p-2 h-28 flex items-start justify-end"><span class="text-sm">${i}</span></div>`
      );
    }
  }

  renderListView(year, month, allEvents) {
    let listHtml = "";
    let hasEventsThisMonth = false;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(
        day
      ).padStart(2, "0")}`;
      const dayEvents = this.getEventsForDate(dateStr, allEvents);

      if (dayEvents.length > 0) {
        hasEventsThisMonth = true;
        const dayOfWeek = new Date(year, month, day)
          .toLocaleString("pt-BR", { weekday: "long" })
          .replace(/^\w/, (c) => c.toUpperCase());

        listHtml += `
                    <div class="py-3 border-b border-slate-100 last:border-b-0">
                        <div class="flex items-baseline gap-2 px-1">
                            <span class="text-lg font-bold text-purple-600">${String(
                              day
                            ).padStart(2, "0")}</span>
                            <span class="font-semibold text-slate-700">${dayOfWeek}</span>
                        </div>
                        <ul class="mt-2 space-y-2">
                `;

        dayEvents.forEach((event) => {
          listHtml += `
                        <li class="calendar-event flex items-center gap-3 p-3 rounded-lg ${
                          this.eventColors[event.type] || "bg-slate-100"
                        } cursor-pointer"
                            data-event-id="${event.id}"
                            data-is-db-event="${!!event.isDbEvent}">
                            <span class="flex-grow font-medium text-sm">${
                              event.title
                            }</span>
                        </li>
                    `;
        });

        listHtml += `</ul></div>`;
      }
    }

    if (!hasEventsThisMonth) {
      this.elements.list.innerHTML =
        '<div class="text-center text-slate-500 py-10">Nenhum evento para este mês.</div>';
    } else {
      this.elements.list.innerHTML = listHtml;
    }
  }
}

/**
 * Funções de utilidade para modais.
 * Podem ser movidas para um arquivo de utilidades global mais tarde.
 */
const ModalHandler = {
  open: (modal) => {
    if (!modal) return;
    modal.classList.remove("pointer-events-none", "hidden");
    setTimeout(() => {
      modal.classList.remove("opacity-0");
      const panel = modal.querySelector(".modal-panel");
      if (panel) {
        panel.classList.remove("-translate-y-10");
      }
    }, 10);
  },
  close: (modal) => {
    if (!modal) return;
    const panel = modal.querySelector(".modal-panel");
    if (panel) {
      panel.classList.add("-translate-y-10");
    }
    modal.classList.add("opacity-0");
    setTimeout(() => modal.classList.add("pointer-events-none", "hidden"), 300);
  },
  init(modalId, closeTriggersSelector = ".close-modal-btn, .cancel-modal-btn") {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        ModalHandler.close(modal);
      }
    });

    modal.querySelectorAll(closeTriggersSelector).forEach((btn) => {
      btn.addEventListener("click", () => ModalHandler.close(modal));
    });
  },
};

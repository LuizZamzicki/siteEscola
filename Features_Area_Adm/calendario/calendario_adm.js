document.addEventListener("DOMContentLoaded", () => {
  // --- Elements ---
  const addEventBtns = document.querySelectorAll(".add-event-btn");
  const addEventModal = document.getElementById("add-event-modal");
  const confirmationModal = document.getElementById(
    "confirmation-modal-calendario"
  );
  const dayEventsModal = document.getElementById("day-events-modal");

  const eventForm = document.getElementById("event-form");
  const eventModalTitle = document.getElementById("event-modal-title");
  const eventIdInput = document.getElementById("event-id");
  const eventTitleInput = document.getElementById("event-title");
  const eventDateInput = document.getElementById("event-date");
  const eventEndDateInput = document.getElementById("event-end-date");
  const eventTypeInput = document.getElementById("event-type");
  const eventTargetInput = document.getElementById("event-target");
  const eventRecurringInput = document.getElementById("event-recurring");
  const deleteEventBtn = document.getElementById("delete-event-btn");

  const eventDateLabel = document.getElementById("event-date-label");
  const eventEndDateWrapper = document.getElementById("event-end-date-wrapper");
  const eventRecurringWrapper = document.getElementById(
    "event-recurring-wrapper"
  );

  const dayEventsModalTitle = document.getElementById("day-events-modal-title");
  const dayEventsModalContent = document.getElementById(
    "day-events-modal-content"
  );

  // Dados injetados pelo PHP através do objeto window
  const eventosDoBanco = window.adminCalendarData?.dbEvents || [];

  // --- Modal Handling (assumindo que ModalHandler é global, vindo de calendario.js) ---
  if (typeof ModalHandler !== "undefined") {
    ModalHandler.init("add-event-modal");
    ModalHandler.init("confirmation-modal-calendario");
    ModalHandler.init("day-events-modal");
  } else {
    console.error(
      "ModalHandler não foi encontrado. Verifique se Widgets/calendario/calendario.js foi carregado corretamente."
    );
    return;
  }

  // Adiciona um listener de evento para clicar em eventos dentro do modal de listagem de dias.
  dayEventsModalContent.addEventListener("click", (e) => {
    // Encontra o item de evento clicado que pode ser editado (possui data-event-id)
    const eventItem = e.target.closest("[data-event-id]");
    if (!eventItem) return;

    const eventId = eventItem.dataset.eventId;
    if (eventId) {
      // Fecha o modal de listagem de eventos do dia
      ModalHandler.close(dayEventsModal);
      // Abre o modal de edição para o evento clicado
      handleEventClick(eventId);
    }
  });

  // --- Calendar Logic ---

  function handleEventClick(eventId) {
    const dbEvent = eventosDoBanco.find((e) => e.id == eventId);

    if (dbEvent) {
      eventModalTitle.textContent = "Editar Evento";
      eventForm.reset();
      eventIdInput.value = dbEvent.id;
      eventTitleInput.value = dbEvent.titulo;
      eventDateInput.value = dbEvent.data_inicio;
      eventEndDateInput.value = dbEvent.data_fim;
      eventTypeInput.value = dbEvent.tipo;
      eventTargetInput.value = dbEvent.publico_alvo;
      eventRecurringInput.checked = dbEvent.recorrente;

      updateModalFields(dbEvent.tipo);
      deleteEventBtn.classList.remove("hidden");
      ModalHandler.open(addEventModal);
    }
  }

  function handleDayClick(dateStr, dayEvents) {
    const [year, month, day] = dateStr.split("-").map(Number);
    const formattedDate = new Date(year, month - 1, day).toLocaleDateString(
      "pt-BR",
      { day: "2-digit", month: "long", year: "numeric" }
    );
    dayEventsModalTitle.textContent = `Eventos para ${formattedDate}`;

    const eventColors = {
      reuniao: "bg-red-100 text-red-700",
      feriado: "bg-green-100 text-green-700",
      ferias: "bg-orange-100 text-orange-700",
      prova: "bg-blue-100 text-blue-700",
      evento: "bg-purple-100 text-purple-700",
    };

    let eventsHtml = dayEvents
      .map((event) => {
        // Apenas eventos do banco de dados (que têm `isDbEvent: true`) são editáveis.
        const isEditable = event.isDbEvent === true;
        const itemClasses = `flex items-start gap-3 p-3 rounded-lg ${
          eventColors[event.type] || "bg-slate-100"
        } ${
          isEditable
            ? "cursor-pointer hover:bg-slate-200 transition-colors"
            : ""
        }`;
        const dataAttrs = isEditable ? `data-event-id="${event.id}"` : "";

        return `
          <div class="${itemClasses}" ${dataAttrs}>
              <div class="flex-grow">
                  <p class="font-medium text-sm">${event.title}</p>
                  ${
                    event.target
                      ? `<p class="text-xs text-slate-500 mt-1">Público: <span class="font-semibold">${event.target}</span></p>`
                      : ""
                  }
              </div>
          </div>
      `;
      })
      .join("");
    dayEventsModalContent.innerHTML = eventsHtml;

    ModalHandler.open(dayEventsModal);
  }

  if (typeof Calendario !== "undefined") {
    const calendario = new Calendario({
      containerId: "calendar-container",
      titleId: "calendar-title",
      prevBtnId: "prev-month-btn",
      nextBtnId: "next-month-btn",
      gridId: "calendar-grid",
      listId: "calendar-list",
      isAdmin: true,
      dbEvents: eventosDoBanco,
      onEventClick: handleEventClick,
      onDayClick: handleDayClick,
    });
  } else {
    console.error(
      "A classe Calendario não foi encontrada. Verifique se Widgets/calendario/calendario.js foi carregado corretamente."
    );
  }

  // --- Form and Modal Logic ---

  function updateModalFields(type) {
    if (type === "ferias") {
      eventEndDateWrapper.classList.remove("hidden");
      eventRecurringWrapper.classList.add("hidden");
      eventRecurringInput.checked = false;
      eventDateLabel.textContent = "Data de Início";
    } else {
      eventEndDateWrapper.classList.add("hidden");
      eventEndDateInput.value = "";
      eventRecurringWrapper.classList.remove("hidden");
      eventDateLabel.textContent = "Data";
    }
  }

  eventTypeInput.addEventListener("change", (e) =>
    updateModalFields(e.target.value)
  );

  addEventBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      eventModalTitle.textContent = "Adicionar Novo Evento";
      eventForm.reset();
      eventIdInput.value = "";
      deleteEventBtn.classList.add("hidden");
      updateModalFields(eventTypeInput.value);
      ModalHandler.open(addEventModal);
    });
  });

  deleteEventBtn.addEventListener("click", (e) => {
    e.preventDefault();
    const eventId = eventIdInput.value;
    const eventTitle = eventTitleInput.value;

    const confirmForm = document.getElementById("delete-event-form");
    document.getElementById("confirmation-title-calendario").textContent =
      "Excluir Evento";
    document.getElementById(
      "confirmation-message-calendario"
    ).innerHTML = `Tem certeza que deseja excluir o evento <strong>"${eventTitle}"</strong>?`;
    confirmForm.querySelector("#event-id-delete").value = eventId;

    ModalHandler.open(confirmationModal);
  });
});

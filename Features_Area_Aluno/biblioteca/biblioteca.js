document.addEventListener("DOMContentLoaded", function () {
  // A variável `studentData` é injetada pelo PHP.
  if (typeof studentData === "undefined" || !studentData.library) {
    // Não executa a lógica da biblioteca se os dados não estiverem presentes.
    return;
  }

  let libraryBooksMap = new Map();

  function createBookCard(book, cardType = "default") {
    const imgSrc = book.img
      ? book.img.replace(/\\/g, "/")
      : "imagens/book-placeholder.png";

    let statusHtml = "";
    if (cardType === "reservation") {
      if (book.status === "devolver") {
        statusHtml = `<div class="text-center bg-green-100 text-green-800 font-semibold py-1.5 px-2 rounded-lg text-xs w-full">Devolver até: ${book.date}</div>`;
      } else if (book.status === "retirar") {
        statusHtml = `<div class="text-center bg-amber-100 text-amber-800 font-semibold py-1.5 px-2 rounded-lg text-xs w-full">Retirar até: ${book.date}</div>`;
      }
    } else if (cardType === "read") {
      if (book.my_rating) {
        let stars = "";
        for (let i = 1; i <= 5; i++) {
          stars += `<i class="fa-solid fa-star ${
            i <= book.my_rating ? "text-amber-400" : "text-slate-300"
          }"></i>`;
        }
        statusHtml = `<div class="text-center text-xs w-full">Sua nota: <span class="flex justify-center mt-1">${stars}</span></div>`;
      } else {
        // Este botão abrirá o modal de detalhes, que já tem o formulário de avaliação.
        statusHtml = `<button class="w-full bg-sky-500 text-white font-bold py-1.5 px-3 rounded-md hover:bg-sky-600 transition-colors duration-200 text-xs">Avaliar</button>`;
      }
    } else {
      // Verifica se o usuário já tem uma reserva para este livro.
      const hasActiveReservation = studentData.library.reservations.some(
        (r) => String(r.id) === String(book.id)
      );

      if (hasActiveReservation) {
        statusHtml = `<button class="w-full bg-red-600 text-white font-bold py-1.5 px-3 rounded-md hover:bg-red-700 transition-colors duration-200 text-xs cancel-reservation-btn">Cancelar Reserva</button>`;
      } else if (book.available > 0) {
        statusHtml = `<button class="w-full bg-purple-600 text-white font-bold py-1.5 px-3 rounded-md hover:bg-purple-700 transition-colors duration-200 text-xs reserve-btn">Reservar</button>`;
      } else {
        statusHtml = `<button class="w-full bg-slate-200 text-slate-500 font-bold py-1.5 px-3 rounded-md cursor-not-allowed text-xs" disabled>Indisponível</button>`;
      }
    }

    return `
      <div class="carousel-item w-40 md:w-48 flex-shrink-0" data-book-id="${book.id}">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full cursor-pointer transform hover:-translate-y-1 transition-transform duration-300">
          <img src="${imgSrc}" alt="Capa do livro ${book.title}" class="w-full h-56 object-cover">
          <div class="p-3 flex flex-col flex-grow">
            <h4 class="font-bold text-sm flex-grow" title="${book.title}">${book.title}</h4>
            <p class="text-xs text-slate-500 mb-3 truncate" title="${book.author}">${book.author}</p>
            ${statusHtml}
          </div>
        </div>
      </div>
    `;
  }

  function renderAllBooks() {
    const { library } = studentData;
    if (!library || !library.all) return;

    const searchInput = document.getElementById("search-book-input");
    const genreSelect = document.getElementById("genre-filter-select");
    const container = document.querySelector(
      // This is for the filtered list
      "#all-books-list-wrapper .carousel-container"
    );
    const emptyMsg = document.querySelector(
      "#all-books-list-wrapper .empty-carousel-msg"
    );

    if (!container || !emptyMsg || !searchInput || !genreSelect) return;

    const searchTerm = searchInput.value.toLowerCase().trim();
    const selectedGenre = genreSelect.value;

    const filteredBooks = library.all.filter((book) => {
      if (!book) return false;

      const matchesSearch =
        searchTerm === "" ||
        (book.title && book.title.toLowerCase().includes(searchTerm)) ||
        (book.author && book.author.toLowerCase().includes(searchTerm));

      const matchesGenre =
        selectedGenre === "" ||
        (book.genre && book.genre.split(", ").includes(selectedGenre));

      return matchesSearch && matchesGenre;
    });

    if (filteredBooks.length > 0) {
      container.innerHTML = filteredBooks
        .map((book) => createBookCard(book, false))
        .join("");
      container.classList.remove("hidden");
      emptyMsg.classList.add("hidden");
    } else {
      container.innerHTML = ""; // Clear container
      container.classList.add("hidden");
      emptyMsg.classList.remove("hidden");
      emptyMsg.textContent =
        "Nenhum livro encontrado com os filtros aplicados.";
    }
  }

  function handleReservation(bookId, buttonElement) {
    if (!bookId || !buttonElement || buttonElement.disabled) return;

    const originalButtonHTML = buttonElement.innerHTML;
    buttonElement.disabled = true;
    buttonElement.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i>Aguarde`;

    const formData = new FormData();
    formData.append("action", "reservar_livro");
    formData.append("book_id", bookId);

    fetch("api.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Desabilita o botão para evitar cliques duplos enquanto a UI atualiza.
          buttonElement.disabled = true;

          // Atualiza os dados da biblioteca localmente para refletir a nova reserva
          if (data.newReservation) {
            updateLibraryDataAfterReservation(bookId, data.newReservation);
          }

          // Fecha o modal de detalhes se a reserva foi feita a partir dele
          if (buttonElement.id === "modal-reserve-btn") {
            closeModal();
          }
          // Exibe uma notificação de sucesso em um modal
          showInfoModal(
            "Sucesso!",
            data.message || "Livro reservado com sucesso!"
          );
        } else {
          buttonElement.disabled = false;
          buttonElement.innerHTML = originalButtonHTML;
          showInfoModal(
            "Erro",
            data.message || "Não foi possível reservar o livro."
          );
        }
      })
      .catch((error) => {
        console.error("Erro na requisição de reserva:", error);
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalButtonHTML;
        showInfoModal(
          "Erro de Comunicação",
          "Ocorreu um erro de comunicação. Tente novamente mais tarde."
        );
      });
  }

  function handleCancelReservation(bookId, buttonElement) {
    if (!bookId || !buttonElement || buttonElement.disabled) return;

    const originalButtonHTML = buttonElement.innerHTML;
    buttonElement.disabled = true;
    buttonElement.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i>Aguarde`;

    const formData = new FormData();
    formData.append("action", "cancelar_reserva_aluno");
    formData.append("book_id", bookId);

    fetch("api.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          buttonElement.disabled = true; // A UI será atualizada, desabilitar para segurança

          // Atualiza os dados locais para refletir o cancelamento
          updateLibraryDataAfterCancellation(bookId);

          // Fecha o modal de detalhes se a ação foi feita a partir dele
          if (buttonElement.id === "modal-reserve-btn") {
            closeModal();
          }

          // Exibe uma notificação de sucesso em um modal
          showInfoModal(
            "Sucesso!",
            data.message || "Reserva cancelada com sucesso!"
          );
        } else {
          buttonElement.disabled = false;
          buttonElement.innerHTML = originalButtonHTML;
          showInfoModal(
            "Erro",
            data.message || "Não foi possível cancelar a reserva."
          );
        }
      })
      .catch((error) => {
        console.error("Erro na requisição de cancelamento:", error);
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalButtonHTML;
        showInfoModal(
          "Erro de Comunicação",
          "Ocorreu um erro de comunicação. Tente novamente mais tarde."
        );
      });
  }

  function updateLibraryDataAfterReservation(bookId, newReservation) {
    // 1. Atualiza a contagem no acervo completo
    const bookInAllList = studentData.library.all.find(
      (b) => String(b.id) === String(bookId)
    );
    if (bookInAllList) {
      bookInAllList.available = Math.max(0, bookInAllList.available - 1);
    }

    // 2. Adiciona a nova reserva à lista de reservas do aluno
    studentData.library.reservations.unshift(newReservation);

    // 3. Repopula os carrosséis para refletir os dados atualizados
    populateLibraryCarousels();

    // 4. Se a aba "Acervo Completo" estiver visível, renderiza novamente para atualizar o estado do botão
    if (
      !document.getElementById("all-books-section").classList.contains("hidden")
    ) {
      renderAllBooks();
    }
  }

  function updateLibraryDataAfterCancellation(bookId) {
    // 1. Atualiza a contagem no acervo completo (opcional, mas bom para a UI)
    const bookInAllList = studentData.library.all.find(
      (b) => String(b.id) === String(bookId)
    );
    if (bookInAllList) {
      // Apenas incrementa se a quantidade for um número
      if (typeof bookInAllList.available === "number") {
        bookInAllList.available++;
      }
    }

    // 2. Remove a reserva da lista de reservas do aluno
    studentData.library.reservations = studentData.library.reservations.filter(
      (r) => String(r.id) !== String(bookId)
    );

    // 3. Repopula os carrosséis
    populateLibraryCarousels();

    // 4. Se a aba "Acervo Completo" estiver visível, renderiza novamente
    if (
      !document.getElementById("all-books-section").classList.contains("hidden")
    ) {
      renderAllBooks();
    }
  }

  function updateLibraryDataAfterRating(bookId, rating, apiData) {
    // 1. Remove from pending ratings
    studentData.library.pendingRatings =
      studentData.library.pendingRatings.filter(
        (p) => String(p.id_livro) !== String(bookId)
      );

    // 2. Update the book in the "read" list with the new rating
    const bookInReadList = studentData.library.read.find(
      (b) => String(b.id) === String(bookId)
    );
    if (bookInReadList) {
      bookInReadList.my_rating = rating;
    }

    // 3. Update the book in the "all" list with the new average rating
    const bookInAllList = studentData.library.all.find(
      (b) => String(b.id) === String(bookId)
    );
    if (bookInAllList && apiData.new_average_rating) {
      bookInAllList.rating = apiData.new_average_rating;
      bookInAllList.total_ratings = apiData.new_total_ratings;
    }

    // 4. Re-render the carousels that might have changed
    populateLibraryCarousels();
    if (
      !document.getElementById("all-books-section").classList.contains("hidden")
    ) {
      renderAllBooks();
    }
  }

  function populateLibraryCarousels() {
    const { library } = studentData;
    if (!library) return;

    // Clear and populate the book map for efficient lookup in the modal.
    libraryBooksMap.clear();
    Object.values(library)
      .flat()
      .forEach((book) => {
        if (book && book.id) {
          libraryBooksMap.set(String(book.id), book);
        }
      });

    // Populate genre filter dropdown
    const genreSelect = document.getElementById("genre-filter-select");
    if (genreSelect && library.all) {
      const allGenres = new Set();
      library.all.forEach((book) => {
        if (book && book.genre) {
          book.genre
            .split(",")
            .map((g) => g.trim())
            .forEach((g) => {
              if (g) allGenres.add(g);
            });
        }
      });

      genreSelect.innerHTML = '<option value="">Todos os Gêneros</option>';
      [...allGenres].sort().forEach((genre) => {
        const option = document.createElement("option");
        option.value = genre;
        option.textContent = genre;
        genreSelect.appendChild(option);
      });
    }

    const sections = {
      reservations: {
        section: document.getElementById("my-reservations-section"),
        container: document.querySelector(
          "#my-reservations-section .carousel-container"
        ),
        data: library.reservations,
        cardType: "reservation",
      },
      read: {
        section: document.getElementById("read-books-section"),
        container: document.querySelector(
          "#read-books-section .carousel-container"
        ),
        data: library.read,
        cardType: "read",
      },
      required: {
        section: document.getElementById("required-reading-section"),
        container: document.querySelector(
          "#required-reading-section .carousel-container"
        ),
        data: library.required,
        cardType: "default",
      },
      recommended: {
        section: document.getElementById("recommended-section"),
        container: document.querySelector(
          "#recommended-section .carousel-container"
        ),
        data: library.recommended,
        cardType: "default",
      },
      all: {
        section: document.getElementById("all-books-section"),
        container: document.querySelector(
          "#all-books-list-wrapper .carousel-container"
        ),
        data: library.all,
        cardType: "default",
      },
    };

    for (const key in sections) {
      const section = sections[key];
      const tabButton = document.querySelector(
        `#library-category-tabs [data-category="${key}"]`
      );

      if (section.container && section.section) {
        if (section.data && section.data.length > 0) {
          section.container.innerHTML = section.data
            .map((book) => createBookCard(book, section.cardType))
            .join("");
          section.container.classList.remove("hidden");
          section.section
            .querySelector(".empty-carousel-msg")
            .classList.add("hidden");
        } else {
          // If the section is empty
          section.container.classList.add("hidden");
          section.section
            .querySelector(".empty-carousel-msg")
            .classList.remove("hidden");

          // Special case: hide the 'recommended' section entirely if it's empty,
          // as it's just a sub-part of the 'all' tab.
          if (key === "recommended") {
            section.section.classList.add("hidden");
          }
        }
      }
    }
  }

  function setupCarouselNavigation() {
    document.querySelectorAll("section[id$='-section']").forEach((section) => {
      const container = section.querySelector(".carousel-container");
      const prevBtn = section.querySelector(".carousel-btn-prev");
      const nextBtn = section.querySelector(".carousel-btn-next");

      if (!container || !prevBtn || !nextBtn) return;

      const scrollAmount =
        container.querySelector(".carousel-item")?.clientWidth + 16 || 208; // 16px is the gap

      prevBtn.addEventListener("click", () => {
        container.scrollBy({ left: -scrollAmount, behavior: "smooth" });
      });

      nextBtn.addEventListener("click", () => {
        container.scrollBy({ left: scrollAmount, behavior: "smooth" });
      });
    });
  }

  const bookModal = document.getElementById("book-modal");
  const modalContent = document.getElementById("modal-content");
  const closeModalBtn = document.getElementById("close-modal-btn");

  // Evento de clique para abrir o modal ou reservar diretamente do card
  document
    .querySelector("#biblioteca-content")
    ?.addEventListener("click", (e) => {
      // Lida com o clique no botão de "Cancelar Reserva"
      const cancelBtnOnCard = e.target.closest(
        ".carousel-item button.cancel-reservation-btn"
      );
      if (cancelBtnOnCard) {
        e.stopPropagation();
        const bookId = cancelBtnOnCard.closest(".carousel-item").dataset.bookId;
        handleCancelReservation(bookId, cancelBtnOnCard);
        return;
      }

      // Verifica se o clique foi no botão de reservar do card
      const reserveBtnOnCard = e.target.closest(
        ".carousel-item button.reserve-btn"
      );

      if (reserveBtnOnCard) {
        e.stopPropagation(); // Impede que o clique abra o modal
        const bookId =
          reserveBtnOnCard.closest(".carousel-item").dataset.bookId;
        handleReservation(bookId, reserveBtnOnCard);
        return;
      }

      // Lógica para abrir o modal de detalhes
      const card = e.target.closest(".carousel-item");
      if (card) {
        bookModal.dataset.currentBookId = card.dataset.bookId; // Armazena o ID no modal
        const bookId = card.dataset.bookId;
        const book = libraryBooksMap.get(bookId);

        if (book) {
          document.getElementById("modal-title").textContent = book.title;
          document.getElementById("modal-img").src = book.img
            ? book.img.replace(/\\/g, "/")
            : "imagens/book-placeholder.png";
          document.getElementById("modal-author").textContent = book.author;
          document.getElementById("modal-genre").textContent = book.genre;
          document.getElementById("modal-publisher").textContent =
            book.publisher;
          document.getElementById("modal-pubdate").textContent = book.pubdate;
          document.getElementById("modal-synopsis").textContent = book.synopsis;

          // --- Rating Section Logic ---
          const userRatingForm = document.getElementById(
            "modal-user-rating-form"
          );
          const userRatingPrompt =
            document.getElementById("user-rating-prompt");

          // Reset form state
          userRatingForm.classList.add("hidden");
          userRatingPrompt.classList.add("hidden");
          document.getElementById("user-rating-comment").disabled = false;
          document.getElementById("user-rating-comment").value = "";
          submitRatingBtn.disabled = false;
          submitRatingBtn.textContent = "Enviar Avaliação";

          // Check if the user has read this book
          const hasReadBook = studentData.library.read.some(
            (b) => String(b.id) === String(bookId)
          );
          const myRating = book.my_rating || null;

          renderAverageRating(book.rating, book.total_ratings);

          if (hasReadBook) {
            userRatingPrompt.classList.add("hidden");
            userRatingForm.classList.remove("hidden");

            if (myRating) {
              // User has already rated, show their rating and disable form
              currentRating = myRating;
              updateStarDisplay(myRating);
              document.getElementById("user-rating-comment").disabled = true;
              submitRatingBtn.disabled = true;
              submitRatingBtn.textContent = "Avaliação Enviada";
            } else {
              // User can rate
              currentRating = 0;
              updateStarDisplay(0);
            }
          } else {
            // User has not read the book, cannot rate
            userRatingForm.classList.add("hidden");
            userRatingPrompt.classList.remove("hidden");
            userRatingPrompt.textContent =
              "Você precisa ter lido este livro para poder avaliá-lo.";
          }

          document.getElementById("modal-synopsis").textContent = book.synopsis;

          const availabilityDiv = document.getElementById("modal-availability");
          const reserveBtn = document.getElementById("modal-reserve-btn");

          // Reseta o botão para o estado padrão de "Reservar"
          reserveBtn.innerHTML = "Reservar Livro";
          reserveBtn.classList.remove(
            "cancel-reservation-btn",
            "bg-red-600",
            "hover:bg-red-700"
          );

          const hasActiveReservation = studentData.library.reservations.some(
            (r) => String(r.id) === String(book.id)
          );

          if (hasActiveReservation) {
            availabilityDiv.innerHTML = `<span class="text-blue-600">●</span> Você já possui uma reserva`;
            reserveBtn.disabled = false;
            reserveBtn.innerHTML = "Cancelar Reserva";
            reserveBtn.classList.add(
              "cancel-reservation-btn",
              "bg-red-600",
              "text-white",
              "hover:bg-red-700"
            );
            reserveBtn.classList.remove(
              "bg-purple-600",
              "hover:bg-purple-700",
              "bg-slate-200",
              "text-slate-500",
              "cursor-not-allowed"
            );
          } else if (book.available > 0) {
            availabilityDiv.innerHTML = `<span class="text-green-600">●</span> ${book.available} disponíveis`;
            reserveBtn.disabled = false;
            reserveBtn.classList.remove(
              "bg-slate-200",
              "text-slate-500",
              "cursor-not-allowed"
            );
            reserveBtn.classList.add(
              "bg-purple-600",
              "text-white",
              "hover:bg-purple-700"
            );
          } else {
            availabilityDiv.innerHTML = `<span class="text-red-500">●</span> Indisponível`;
            reserveBtn.disabled = true;
            reserveBtn.classList.add(
              "bg-slate-200",
              "text-slate-500",
              "cursor-not-allowed"
            );
            reserveBtn.classList.remove(
              "bg-purple-600",
              "text-white",
              "hover:bg-purple-700"
            );
          }

          bookModal.classList.remove("hidden");
          bookModal.classList.add("flex");
          setTimeout(() => {
            bookModal.classList.remove("opacity-0");
            modalContent.classList.remove("scale-95");
          }, 10);
        }
      }
    });

  // Evento de clique para o botão de reservar DENTRO do modal
  const modalReserveBtn = document.getElementById("modal-reserve-btn");
  if (modalReserveBtn) {
    modalReserveBtn.addEventListener("click", () => {
      const bookId = bookModal.dataset.currentBookId;
      if (modalReserveBtn.classList.contains("cancel-reservation-btn")) {
        handleCancelReservation(bookId, modalReserveBtn);
      } else {
        handleReservation(bookId, modalReserveBtn);
      }
    });
  }

  // --- Lógica para Avaliação de Livros ---
  const userRatingStarsContainer = document.getElementById("user-rating-stars");
  const submitRatingBtn = document.getElementById("submit-rating-btn");
  let currentRating = 0;

  if (userRatingStarsContainer) {
    userRatingStarsContainer.addEventListener("click", (e) => {
      const star = e.target.closest(".fa-star");
      if (star) {
        currentRating = parseInt(star.dataset.value, 10);
        updateStarDisplay(currentRating);
      }
    });

    userRatingStarsContainer.addEventListener("mouseover", (e) => {
      const star = e.target.closest(".fa-star");
      if (star) {
        const hoverValue = parseInt(star.dataset.value, 10);
        updateStarDisplay(hoverValue, true);
      }
    });

    userRatingStarsContainer.addEventListener("mouseout", () => {
      updateStarDisplay(currentRating);
    });
  }

  function updateStarDisplay(value, isHover = false) {
    const stars = userRatingStarsContainer.querySelectorAll(".fa-star");
    stars.forEach((star) => {
      const starValue = parseInt(star.dataset.value, 10);
      if (starValue <= value) {
        star.classList.add("text-amber-400");
        star.classList.remove("text-slate-300");
      } else {
        star.classList.remove("text-amber-400");
        star.classList.add("text-slate-300");
      }
    });
  }

  function renderAverageRating(rating, total) {
    const container = document.getElementById("modal-avg-rating-stars");
    const text = document.getElementById("modal-avg-rating-text");
    if (!container || !text) return;

    const ratingValue = parseFloat(rating) || 0;
    const totalValue = parseInt(total) || 0;

    let starsHtml = "";
    for (let i = 1; i <= 5; i++) {
      if (i <= ratingValue) {
        starsHtml += '<i class="fa-solid fa-star"></i>';
      } else if (i - 0.5 <= ratingValue) {
        starsHtml += '<i class="fa-solid fa-star-half-stroke"></i>';
      } else {
        starsHtml += '<i class="fa-regular fa-star"></i>';
      }
    }
    container.innerHTML = starsHtml;

    if (totalValue > 0) {
      text.textContent = `${ratingValue.toFixed(
        1
      )} de 5 (${totalValue} avaliações)`;
    } else {
      text.textContent = "Ainda não há avaliações.";
    }
  }

  if (submitRatingBtn) {
    submitRatingBtn.addEventListener("click", () => {
      const bookId = bookModal.dataset.currentBookId;
      const comment = document.getElementById("user-rating-comment").value;

      if (currentRating > 0) {
        handleRatingSubmit(bookId, currentRating, comment, submitRatingBtn);
      } else {
        showInfoModal(
          "Atenção",
          "Por favor, selecione uma nota de 1 a 5 estrelas."
        );
      }
    });
  }

  function closeModal() {
    modalContent.classList.add("scale-95");
    bookModal.classList.add("opacity-0");
    setTimeout(() => {
      bookModal.classList.add("hidden");
      bookModal.classList.remove("flex");
    }, 300);
  }

  function handleRatingSubmit(bookId, rating, comment, buttonElement) {
    const originalButtonHTML = buttonElement.innerHTML;
    buttonElement.disabled = true;
    buttonElement.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i>Enviando...`;

    const formData = new FormData();
    formData.append("action", "avaliar_livro");
    formData.append("book_id", bookId);
    formData.append("rating", rating);
    formData.append("comment", comment);

    fetch("api.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showInfoModal("Obrigado!", data.message);
          updateLibraryDataAfterRating(bookId, rating, data);
          document
            .getElementById("modal-user-rating-form")
            .classList.add("hidden");
          const prompt = document.getElementById("user-rating-prompt");
          prompt.textContent = "Obrigado pela sua avaliação!";
          prompt.classList.remove("hidden");
        } else {
          showInfoModal("Erro", data.message);
        }
      })
      .catch((error) => {
        console.error("Erro ao enviar avaliação:", error);
        showInfoModal(
          "Erro de Comunicação",
          "Não foi possível enviar sua avaliação. Tente novamente."
        );
      })
      .finally(() => {
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalButtonHTML;
      });
  }

  if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
  if (bookModal)
    bookModal.addEventListener(
      "click",
      (e) => e.target === bookModal && closeModal()
    );

  // --- Lógica para o Modal de Informação ---
  const infoModal = document.getElementById("info-modal");
  const infoModalTitle = document.getElementById("info-modal-title");
  const infoModalMessage = document.getElementById("info-modal-message");
  const infoModalCloseBtn = document.getElementById("info-modal-close-btn");

  function showInfoModal(title, message) {
    if (!infoModal || !infoModalTitle || !infoModalMessage) return;
    infoModalTitle.textContent = title;
    infoModalMessage.innerHTML = message;

    infoModal.classList.remove("hidden");
    infoModal.classList.add("flex");
    setTimeout(() => {
      infoModal.classList.remove("opacity-0");
      infoModal.querySelector("div").classList.remove("scale-95");
    }, 10);
  }

  function closeInfoModal() {
    if (!infoModal) return;
    infoModal.querySelector("div").classList.add("scale-95");
    infoModal.classList.add("opacity-0");
    setTimeout(() => {
      infoModal.classList.add("hidden");
      infoModal.classList.remove("flex");
    }, 300);
  }

  if (infoModalCloseBtn)
    infoModalCloseBtn.addEventListener("click", closeInfoModal);
  if (infoModal)
    infoModal.addEventListener(
      "click",
      (e) => e.target === infoModal && closeInfoModal()
    );

  // --- Lógica para o Pop-up de Avaliação ---
  function showRatingPrompt() {
    if (sessionStorage.getItem("ratingPromptDismissed") === "true") {
      return;
    }

    const pendingRatings = studentData.library.pendingRatings;
    if (!pendingRatings || pendingRatings.length === 0) {
      return;
    }

    const bookToRate = pendingRatings[0];
    const modal = document.getElementById("rating-prompt-modal");
    const message = document.getElementById("rating-prompt-message");
    const submitBtn = document.getElementById("rating-prompt-submit-btn");
    const dismissBtn = document.getElementById("rating-prompt-dismiss-btn");
    const starsContainer = document.getElementById("prompt-rating-stars");
    const commentInput = document.getElementById("prompt-rating-comment");

    if (!modal || !message || !submitBtn || !dismissBtn || !starsContainer)
      return;

    modal.dataset.bookId = bookToRate.id_livro;
    message.innerHTML = `Vimos que você leu o livro <strong>${bookToRate.titulo}</strong>. Gostaria de avaliá-lo?`;

    let promptRating = 0;
    const promptStars = starsContainer.querySelectorAll(".fa-star");

    const updatePromptStars = (value) => {
      promptStars.forEach((star) => {
        const starValue = parseInt(star.dataset.value, 10);
        star.classList.toggle("text-amber-400", starValue <= value);
        star.classList.toggle("text-slate-300", starValue > value);
      });
    };

    starsContainer.addEventListener("click", (e) => {
      const star = e.target.closest(".fa-star");
      if (star) {
        promptRating = parseInt(star.dataset.value, 10);
        updatePromptStars(promptRating);
      }
    });

    const closePrompt = () => {
      modal.querySelector("div").classList.add("scale-95");
      modal.classList.add("opacity-0");
      setTimeout(() => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
      }, 300);
    };

    dismissBtn.addEventListener("click", () => {
      sessionStorage.setItem("ratingPromptDismissed", "true");
      closePrompt();
    });

    submitBtn.addEventListener("click", () => {
      if (promptRating === 0) {
        showInfoModal(
          "Atenção",
          "Por favor, selecione uma nota de 1 a 5 estrelas."
        );
        return;
      }
      handleRatingSubmit(
        modal.dataset.bookId,
        promptRating,
        commentInput.value,
        submitBtn
      );
      sessionStorage.setItem("ratingPromptDismissed", "true");
      closePrompt();
    });

    showInfoModal(modal); // Reutilizando a função de abrir modal
  }

  // --- Inicialização da Biblioteca ---
  function initializeLibrary() {
    const { library } = studentData;
    if (!library) return;

    populateLibraryCarousels();

    setupCarouselNavigation();

    const categoryTabsContainer = document.getElementById(
      "library-category-tabs"
    );
    const categoryTabs = categoryTabsContainer.querySelectorAll(
      ".library-category-tab"
    );
    const carouselSections = document.querySelectorAll(
      ".library-carousel-section"
    );

    categoryTabsContainer.addEventListener("click", (e) => {
      const tab = e.target.closest(".library-category-tab");
      if (!tab) return;

      const category = tab.dataset.category;

      categoryTabs.forEach((t) => {
        t.classList.remove("bg-purple-600", "text-white");
        t.classList.add("text-slate-600", "hover:bg-slate-100");
      });
      tab.classList.add("bg-purple-600", "text-white");
      tab.classList.remove("text-slate-600", "hover:bg-slate-100");

      carouselSections.forEach((section) => {
        section.classList.toggle(
          "hidden",
          section.dataset.category !== category
        );
      });
    });

    const searchInput = document.getElementById("search-book-input");
    const genreSelect = document.getElementById("genre-filter-select");

    if (searchInput && genreSelect) {
      // Use 'input' for search for real-time feedback
      searchInput.addEventListener("input", renderAllBooks);
      genreSelect.addEventListener("change", renderAllBooks);
    }

    const allBooksTab = categoryTabsContainer.querySelector(
      '[data-category="all"]'
    );
    if (allBooksTab) {
      allBooksTab.click();
    }

    showRatingPrompt();
  }

  initializeLibrary();
});

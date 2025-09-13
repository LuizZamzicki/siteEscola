document.addEventListener("DOMContentLoaded", function () {
  // A variável `studentData` é injetada pelo PHP.
  if (typeof studentData === "undefined" || !studentData.library) {
    // Não executa a lógica da biblioteca se os dados não estiverem presentes.
    return;
  }

  let libraryBooksMap = new Map();

  function createBookCard(book, isReservation = false) {
    const imgSrc = book.img
      ? book.img.replace(/\\/g, "/")
      : "uploads/livros/placeholder.png";

    let statusHtml = "";
    if (isReservation) {
      if (book.status === "devolver" && book.date) {
        statusHtml = `<div class="text-center bg-green-100 text-green-800 font-semibold py-2 rounded-lg text-sm">Devolver até: ${book.date}</div>`;
      } else if (book.status === "retirar" && book.date) {
        statusHtml = `<div class="text-center bg-amber-100 text-amber-800 font-semibold py-2 rounded-lg text-sm">Retirar até: ${book.date}</div>`;
      }
    } else {
      if (book.available > 0) {
        statusHtml = `<button class="bg-[#F2C94C] text-slate-900 hover:bg-amber-400 w-full rounded-md py-1.5 text-sm font-semibold">Reservar</button>`;
      } else {
        statusHtml = `<button class="bg-slate-200 text-slate-500 w-full rounded-md py-1.5 text-sm font-semibold cursor-not-allowed" disabled>Indisponível</button>`;
      }
    }

    return `
      <div class="carousel-item w-60 flex-shrink-0" data-book-id="${book.id}">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full cursor-pointer">
          <img src="${imgSrc}" alt="Capa do livro ${book.title}" class="w-full h-48 object-cover">
          <div class="p-4 flex flex-col flex-grow">
            <h4 class="font-bold text-md flex-grow">${book.title}</h4>
            <p class="text-sm text-slate-500 mb-4">${book.author}</p>
            ${statusHtml}
          </div>
        </div>
      </div>
    `;
  }

  function populateLibrary() {
    const { library } = studentData;
    if (!library) return;

    // Clear and populate the book map for efficient lookup in the modal.
    libraryBooksMap.clear();
    [
      ...(library.reservations || []),
      ...(library.required || []),
      ...(library.recommended || []),
      ...(library.all || []),
    ].forEach((book) => {
      if (book && book.id) {
        libraryBooksMap.set(String(book.id), book);
      }
    });

    const sections = {
      reservations: {
        container: document.querySelector(
          "#my-reservations-section .carousel-container"
        ),
        data: library.reservations,
        isReservation: true,
        emptyMsg: "Nenhuma reserva ativa.",
      },
      required: {
        container: document.querySelector(
          "#required-reading-section .carousel-container"
        ),
        data: library.required,
        isReservation: false,
        emptyMsg: "Nenhum livro obrigatório para sua turma.",
      },
      recommended: {
        container: document.querySelector(
          "#recommended-section .carousel-container"
        ),
        data: library.recommended,
        isReservation: false,
        emptyMsg: "Nenhuma recomendação para você no momento.",
      },
      all: {
        container: document.querySelector(
          "#all-books-section .carousel-container"
        ),
        data: library.all,
        isReservation: false,
        emptyMsg: "Nenhum livro encontrado no acervo.",
      },
    };

    for (const key in sections) {
      const section = sections[key];
      const sectionElement = section.container?.closest("section");

      if (section.container && sectionElement) {
        if (section.data && section.data.length > 0) {
          sectionElement.classList.remove("hidden");
          section.container.innerHTML = section.data
            .map((book) => createBookCard(book, section.isReservation))
            .join("");
        } else {
          if (key === "reservations" || key === "required") {
            sectionElement.classList.add("hidden");
          } else {
            sectionElement.classList.remove("hidden");
            section.container.innerHTML = `<div class="text-center text-slate-500 py-4 w-full">${section.emptyMsg}</div>`;
          }
        }
      }
    }
  }

  // --- Lógica de Filtros da Biblioteca ---
  const filterTitleInput = document.getElementById("filter-title-input");
  const filterGenreCheckboxes = document.querySelectorAll(
    'input[name="genre[]"]'
  );
  const filterAuthorSelect = document.getElementById("filter-author-select");
  const allBooksContainer = document.querySelector(
    "#all-books-section .carousel-container"
  );

  function applyLibraryFilters() {
    // Only run if the elements exist on the page
    if (
      !allBooksContainer ||
      !studentData.library?.all ||
      !filterTitleInput ||
      !filterAuthorSelect
    ) {
      return;
    }

    const allBooks = studentData.library.all;
    const titleFilter = filterTitleInput.value.toLowerCase().trim();
    const authorFilter = filterAuthorSelect.value;
    const selectedGenres = Array.from(filterGenreCheckboxes)
      .filter((cb) => cb.checked)
      .map((cb) => cb.value);

    const filteredBooks = allBooks.filter((book) => {
      const titleMatch = book.title.toLowerCase().includes(titleFilter);

      const bookAuthors = book.author
        ? book.author.split(",").map((a) => a.trim())
        : [];
      const authorMatch = !authorFilter || bookAuthors.includes(authorFilter);

      const bookGenres = book.genre
        ? book.genre.split(",").map((g) => g.trim())
        : [];
      const genreMatch =
        selectedGenres.length === 0 ||
        selectedGenres.some((selectedGenre) =>
          bookGenres.includes(selectedGenre)
        );

      return titleMatch && authorMatch && genreMatch;
    });

    if (filteredBooks.length > 0) {
      allBooksContainer.innerHTML = filteredBooks
        .map((book) => createBookCard(book, false))
        .join("");
    } else {
      allBooksContainer.innerHTML = `<div class="text-center text-slate-500 py-4 w-full">Nenhum livro encontrado com os filtros aplicados.</div>`;
    }
  }

  if (filterTitleInput) {
    filterTitleInput.addEventListener("input", applyLibraryFilters);
  }
  if (filterAuthorSelect) {
    filterAuthorSelect.addEventListener("change", applyLibraryFilters);
  }
  if (filterGenreCheckboxes.length > 0) {
    filterGenreCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", applyLibraryFilters);
    });
  }

  const bookModal = document.getElementById("book-modal");
  const modalContent = document.getElementById("modal-content");
  const closeModalBtn = document.getElementById("close-modal-btn");

  document
    .querySelector("#biblioteca-content")
    ?.addEventListener("click", (e) => {
      const card = e.target.closest(".carousel-item");
      if (card) {
        const bookId = card.dataset.bookId;
        // Refactored: Use the map for a quick and reliable lookup.
        const book = libraryBooksMap.get(bookId);

        if (book) {
          document.getElementById("modal-title").textContent = book.title;
          document.getElementById("modal-img").src =
            book.img || "imagens/book-placeholder.png";
          document.getElementById("modal-author").textContent = book.author;
          document.getElementById("modal-genre").textContent = book.genre;
          document.getElementById("modal-publisher").textContent =
            book.publisher;
          document.getElementById("modal-pubdate").textContent = book.pubdate;
          document.getElementById("modal-synopsis").textContent = book.synopsis;

          const availabilityDiv = document.getElementById("modal-availability");
          const reserveBtn = document.querySelector("#modal-reserve-btn");

          if (book.available > 0) {
            availabilityDiv.innerHTML = `<span class="text-green-600">●</span> ${book.available} disponíveis`;
            reserveBtn.disabled = false;
            reserveBtn.classList.remove(
              "bg-slate-200",
              "text-slate-500",
              "cursor-not-allowed"
            );
            reserveBtn.classList.add(
              "bg-[#F2C94C]",
              "text-slate-900",
              "hover:bg-amber-400"
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
              "bg-[#F2C94C]",
              "text-slate-900",
              "hover:bg-amber-400"
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

  function closeModal() {
    modalContent.classList.add("scale-95");
    bookModal.classList.add("opacity-0");
    setTimeout(() => {
      bookModal.classList.add("hidden");
      bookModal.classList.remove("flex");
    }, 300);
  }

  if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
  if (bookModal)
    bookModal.addEventListener(
      "click",
      (e) => e.target === bookModal && closeModal()
    );

  // Carousel button logic
  document.querySelectorAll(".carousel-wrapper").forEach((wrapper) => {
    const container = wrapper.querySelector(".carousel-container");
    const prevBtn = wrapper.querySelector(".carousel-prev");
    const nextBtn = wrapper.querySelector(".carousel-next");

    if (prevBtn)
      prevBtn.addEventListener("click", () =>
        container.scrollBy({ left: -300, behavior: "smooth" })
      );
    if (nextBtn)
      nextBtn.addEventListener("click", () =>
        container.scrollBy({ left: 300, behavior: "smooth" })
      );
  });

  // --- Inicialização da Biblioteca ---
  populateLibrary();
});

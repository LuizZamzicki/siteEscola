document.addEventListener("DOMContentLoaded", function () {
  // Formata o campo de telefone ao digitar
  const telefoneInput = document.getElementById("telefone");
  telefoneInput.addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");
    let formattedValue = "";
    if (value.length > 0) formattedValue = "(" + value.substring(0, 2);
    if (value.length >= 3) formattedValue += ") " + value.substring(2, 7);
    if (value.length >= 8) formattedValue += "-" + value.substring(7, 11);
    e.target.value = formattedValue;
  });

  const contactForm = document.getElementById("contactForm");
  const formMessages = document.getElementById("form-messages");
  const emailInput = document.getElementById("email");

  contactForm.addEventListener("submit", function (event) {
    event.preventDefault();
    formMessages.style.display = "none";
    formMessages.className = "mb-3";

    // Validação HTML5
    if (!contactForm.checkValidity()) {
      formMessages.textContent =
        "Por favor, preencha todos os campos obrigatórios corretamente.";
      formMessages.className = "mb-3 alert alert-danger";
      formMessages.style.display = "block";
      contactForm.classList.add("was-validated");

      const firstInvalidField = contactForm.querySelector(":invalid");
      if (firstInvalidField) {
        firstInvalidField.focus();
      }
      return;
    }

    contactForm.classList.remove("was-validated");

    const submitButton = contactForm.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = "Enviando...";

    const formData = new FormData(contactForm);
    const phpScriptUrl = contactForm.action;

    fetch(phpScriptUrl, {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Erro de rede ou servidor. Status: " + response.status
          );
        }
        return response.json();
      })
      .then((data) => {
        submitButton.disabled = false;
        submitButton.textContent = "Enviar Mensagem";
        formMessages.style.display = "block";
        formMessages.textContent = data.message;

        if (data.success) {
          formMessages.className = "mb-3 alert alert-success";
          contactForm.reset();
        } else {
          formMessages.className = "mb-3 alert alert-danger";
          if (data.message.includes("e-mail informado é inválido")) {
            emailInput.focus();
          } else {
            document.getElementById("nome").focus();
          }
        }
      })
      .catch((error) => {
        submitButton.disabled = false;
        submitButton.textContent = "Enviar Mensagem";
        formMessages.style.display = "block";
        formMessages.textContent =
          "Ocorreu um erro ao enviar sua mensagem: " + error.message;
        formMessages.className = "mb-3 alert alert-danger";
        console.error("Erro no envio do formulário:", error);
      });
  });
});

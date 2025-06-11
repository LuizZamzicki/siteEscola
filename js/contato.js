document.addEventListener("DOMContentLoaded", function () {
  const telefoneInput = document.getElementById("telefone");
  telefoneInput.addEventListener("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");
    let formattedValue = "";
    if (value.length > 0) formattedValue = "(" + value.substring(0, 2);
    if (value.length >= 3) formattedValue += ") " + value.substring(2, 7);
    if (value.length >= 8) formattedValue += "-" + value.substring(7, 11);
    e.target.value = formattedValue;
  });
});
document.addEventListener("DOMContentLoaded", function () {
  // Script de formatação de telefone (mantenha este)
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
  const emailInput = document.getElementById("email"); // Referência ao campo de e-mail

  contactForm.addEventListener("submit", function (event) {
    event.preventDefault();

    // Limpa mensagens anteriores
    formMessages.style.display = "none";
    formMessages.className = "mb-3"; // Reset classes

    // Validação básica do lado do cliente (HTML5)
    if (!contactForm.checkValidity()) {
      formMessages.textContent =
        "Por favor, preencha todos os campos obrigatórios corretamente.";
      formMessages.className = "mb-3 alert alert-danger";
      formMessages.style.display = "block";
      contactForm.classList.add("was-validated"); // Adiciona classe para exibir feedbacks do Bootstrap

      // Foca no primeiro campo inválido do HTML5
      const firstInvalidField = contactForm.querySelector(":invalid");
      if (firstInvalidField) {
        firstInvalidField.focus();
      }

      return; // Interrompe o envio se a validação falhar
    }
    contactForm.classList.remove("was-validated"); // Remove a classe se for válido

    // Desabilita o botão
    const submitButton = contactForm.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = "Enviando...";

    // Coleta os dados do formulário
    const formData = new FormData(contactForm);
    const phpScriptUrl = contactForm.action; // Pega o valor do action do formulário

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
        return response.json(); // Espera uma resposta JSON do PHP
      })
      .then((data) => {
        // Reabilita o botão e restaura o texto
        submitButton.disabled = false;
        submitButton.textContent = "Enviar Mensagem";

        // Exibe a mensagem de feedback
        formMessages.style.display = "block";
        formMessages.textContent = data.message;

        if (data.success) {
          formMessages.className = "mb-3 alert alert-success";
          contactForm.reset(); // Limpa o formulário em caso de sucesso
          // Opcional: focar de volta no primeiro campo do formulário vazio para um novo preenchimento
          // document.getElementById("nome").focus();
        } else {
          formMessages.className = "mb-3 alert alert-danger";

          // NOVO: Foca no campo de e-mail se a mensagem indicar erro de e-mail
          if (data.message.includes("e-mail informado é inválido")) {
            emailInput.focus();
          } else {
            // Caso contrário, foca no primeiro campo do formulário ou outro campo relevante
            // Por exemplo, focar no campo 'nome' se não for um erro de e-mail específico
            document.getElementById("nome").focus();
          }
        }
      })
      .catch((error) => {
        // Reabilita o botão e restaura o texto
        submitButton.disabled = false;
        submitButton.textContent = "Enviar Mensagem";

        // Exibe mensagem de erro geral
        formMessages.style.display = "block";
        formMessages.textContent =
          "Ocorreu um erro ao enviar sua mensagem: " + error.message;
        formMessages.className = "mb-3 alert alert-danger";
        console.error("Erro no envio do formulário:", error);
      });
  });
});

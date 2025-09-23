jQuery(document).ready(function($) {
  // Elementos do DOM
  const valorPersonalizado = document.getElementById("valorPersonalizado");
  const gerarPixBtn = document.getElementById("gerarPixBtn");
  const valoresBtns = document.querySelectorAll(".valor-pix");
  const pixResult = document.getElementById("pix-result");
  const pixValor = document.getElementById("pixValor");
  const pixBeneficiario = document.getElementById("pixBeneficiario");
  const pixCopyCola = document.getElementById("pixCopyCola");
  const pixQrCodeContainer = document.getElementById("pixQrCodeContainer");
  const copiarPixBtn = document.getElementById("copiarPixBtn");
  const voltarValoresBtn = document.getElementById("voltarValoresBtn");
  const errorContainer = document.getElementById("error-container");
  const errorDetails = document.getElementById("error-details");

  let qrcode = null;

  function mostrarSecaoValores() {
    document.querySelector(".pix-values").classList.remove("d-none");
    pixResult.classList.add("d-none");
    voltarValoresBtn.style.display = "none";
    errorContainer.classList.add("d-none");
  }

  function mostrarResultadoPix() {
    document.querySelector(".pix-values").classList.add("d-none");
    pixResult.classList.remove("d-none");
    voltarValoresBtn.style.display = "block";
  }

  function mostrarErro(mensagem, detalhes = null) {
    errorContainer.classList.remove("d-none");
    errorDetails.innerHTML = mensagem;

    if (detalhes) {
      const detalhesHTML = document.createElement("pre");
      detalhesHTML.className = "error-json mt-2";
      detalhesHTML.style.fontSize = "12px";
      detalhesHTML.style.maxHeight = "150px";
      detalhesHTML.style.overflow = "auto";
      detalhesHTML.innerText = JSON.stringify(detalhes, null, 2);
      errorDetails.appendChild(detalhesHTML);
    }
  }

  function gerarQRCode(codigoPix) {
    pixQrCodeContainer.innerHTML = "";

    if (qrcode !== null) {
      qrcode.clear();
      qrcode = null;
    }

    try {
      console.log("Gerando QR Code para:", codigoPix);
      qrcode = new QRCode(pixQrCodeContainer, {
        text: codigoPix,
        width: 180,
        height: 180,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });

      console.log("QR Code gerado com sucesso");
    } catch (error) {
      console.error("Erro ao gerar QR Code:", error);
      pixQrCodeContainer.innerHTML = '<div class="alert alert-danger">Erro ao gerar QR Code</div>';
      mostrarErro("Falha ao gerar o QR Code: " + error.message);
    }
  }

    function gerarPix(valor) {
      valor = parseFloat(valor);
      if (isNaN(valor) || valor < 1 || valor > 1000) {
        alert("Por favor, informe um valor entre R$ 1,00 e R$ 1.000,00");
        return;
      }
    
      // Limpa dados anteriores
      pixValor.textContent = "";
      pixBeneficiario.textContent = "";
      pixCopyCola.value = "";
      errorContainer.classList.add("d-none");
      pixQrCodeContainer.innerHTML = '<div class="spinner-border text-primary" role="status" aria-live="polite"><span class="visually-hidden">Carregando...</span></div>';
    
      pixResult.classList.remove("d-none");
      document.querySelector(".pix-values").classList.add("d-none");
      voltarValoresBtn.style.display = "block";
    
      console.log("Enviando requisição para gerar PIX com valor:", valor);

    $.ajax({
      url: baseGPix.ajaxUrl,
      type: "POST",
      data: {
        action: "base_g_pix_generate",
        valor: valor,
        nonce: baseGPix.nonce
      },
      success: function(response) {
        console.log("Resposta recebida:", response);

        if (response.success && response.data) {
          pixValor.textContent = response.data.valorFormatado;
          pixBeneficiario.textContent = response.data.beneficiario;
          pixCopyCola.value = response.data.codigo;

          gerarQRCode(response.data.codigo);
          mostrarResultadoPix();
        } else {
          console.error("Resposta do servidor não contém código PIX:", response);
          pixQrCodeContainer.innerHTML = '<div class="alert alert-danger">Resposta inválida do servidor</div>';
          mostrarErro("Resposta do servidor não contém código PIX", response);
        }
      },
      error: function(xhr, status, error) {
        console.error("Erro ao gerar PIX:", error);
        console.error("Resposta:", xhr.responseText);

        let errorMsg = "Erro ao gerar PIX";
        let detalhes = null;

        try {
          const response = JSON.parse(xhr.responseText);
          errorMsg = response.message || (response.data && response.data.message) || errorMsg;
          detalhes = response;
        } catch (e) {
          detalhes = { error: error, text: xhr.responseText };
        }

        pixQrCodeContainer.innerHTML = '<div class="alert alert-danger">Erro ao gerar PIX</div>';
        mostrarErro(errorMsg, detalhes);
      }
    });
  }

  // Listeners
  valoresBtns.forEach(btn => {
    btn.addEventListener("click", function () {
      gerarPix(this.dataset.valor);
    });
  });

  if (gerarPixBtn) {
    gerarPixBtn.addEventListener("click", function () {
      if (valorPersonalizado && valorPersonalizado.value) {
        gerarPix(valorPersonalizado.value);
      } else {
        alert("Por favor, informe um valor para doação.");
      }
    });
  }

  if (valorPersonalizado) {
    valorPersonalizado.addEventListener("keypress", function (e) {
      if (e.key === "Enter" && this.value) {
        gerarPix(this.value);
      }
    });
  }

  if (voltarValoresBtn) {
    voltarValoresBtn.addEventListener("click", function () {
      mostrarSecaoValores();
    });
  }

  if (copiarPixBtn) {
    copiarPixBtn.addEventListener("click", async function () {
      try {
        if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(pixCopyCola.value);
        } else {
          pixCopyCola.select();
          document.execCommand("copy");
        }

        const originalText = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check-lg"></i> Copiado!';
        setTimeout(() => {
          this.innerHTML = originalText;
        }, 2000);
      } catch (err) {
        console.error("Erro ao copiar PIX:", err);
        alert("Não foi possível copiar o código PIX.");
      }
    });
  }

  // Resetar modal
  $("#baseGPixModal").on("hidden.bs.modal", function () {
    mostrarSecaoValores();
    if (valorPersonalizado) {
      valorPersonalizado.value = "";
    }
  });

  // Debug
  console.log("Script Base G PIX carregado");
  console.log("URL AJAX:", baseGPix?.ajaxUrl);
});

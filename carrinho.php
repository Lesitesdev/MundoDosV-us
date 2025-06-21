<?php include 'includes/header.php'; ?>

<main class="max-w-3xl mx-auto mt-6 p-4 bg-white shadow-md rounded">
  <h2 class="text-xl font-bold mb-4">Seu Carrinho</h2>

  <div class="mt-4 mb-6">
    <a href="produtos.php" class="flex items-center text-purple-600 hover:text-purple-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7 7-7" />
      </svg>
      Voltar
    </a>
  </div>

  <ul id="itens-carrinho" class="space-y-3"></ul>

  <div class="mt-6 flex justify-between items-center border-t pt-4">
    <span class="text-lg font-semibold">Subtotal dos Produtos:</span>
    <span id="subtotal-carrinho" class="text-2xl font-bold text-purple-700">R$ 0,00</span>
  </div>

  <div class="mt-2 flex justify-between items-center">
    <span class="text-lg font-semibold">Valor do Frete:</span>
    <span id="valor-frete" class="text-2xl font-bold text-purple-700">R$ 0,00</span>
  </div>

  <div class="mt-2 flex justify-between items-center border-t pt-4">
    <span class="text-lg font-semibold">Subtotal + Frete:</span>
    <span id="subtotal-frete" class="text-2xl font-bold text-purple-700">R$ 0,00</span>
  </div>

  <div class="mt-6">
    <label for="cep-frete" class="block text-sm font-semibold">Digite seu CEP para cotar o frete:</label>
    <div class="flex items-center gap-4">
      <input type="text" id="cep-frete" name="cep" class="w-full p-2 border rounded" placeholder="Digite seu CEP" required>
      <button type="button" id="calcular-frete" class="bg-purple-700 text-white px-6 py-2 rounded hover:bg-purple-800 transition">Calcular Frete</button>
    </div>
    <div id="resultado-frete" class="mt-4 text-lg font-semibold text-purple-700"></div>
  </div>

  <div class="desconto-container mt-4">
    <label for="codigo-desconto" class="block mb-1 font-semibold">Código de Desconto:</label>
    <input type="text" id="codigo-desconto" placeholder="Digite o código" class="border p-2 rounded w-48" />
    <button id="aplicar-desconto" class="bg-purple-700 text-white px-4 py-2 rounded ml-2 hover:bg-purple-900">Aplicar</button>
    <p id="resultado-desconto" class="mt-2"></p>
  </div>

  <div class="mt-6 flex justify-between items-center border-t pt-4">
    <span class="text-lg font-semibold">Total Final:</span>
    <div>
      <span id="total-antigo" class="text-xl text-gray-500 line-through mr-2 hidden"></span>
      <span id="total-final" class="text-2xl font-bold text-purple-700">R$ 0,00</span>
    </div>
  </div>

  <div class="mt-6 text-right">
    <a href="dadosclientes.php" class="bg-purple-700 text-white px-6 py-2 rounded hover:bg-purple-800 transition">Finalizar Compra</a>
  </div>
  <!-- Trocar link para um botão -->
<button id="finalizar-compra" class="bg-purple-700 text-white px-6 py-2 rounded hover:bg-purple-800 transition">
  Finalizar Compra
</button>

</main>

<?php include 'includes/footer.php'; ?>

<script>
(() => {
  let subtotalCarrinho = 0;
  let frete = 0;
  let desconto = 0;
  let cupomAplicado = false;

  const formatarValor = valor => 
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);

  document.addEventListener('DOMContentLoaded', () => {
    atualizarCarrinho();

    document.getElementById('calcular-frete').addEventListener('click', handleFreteClick);
    document.getElementById('aplicar-desconto').addEventListener('click', aplicarDesconto);
  });

  async function calcularFrete(cepDestino) {
    // Simulação - substitua pela API real se quiser
    return 15.00;
  }

  async function handleFreteClick() {
    let cep = document.getElementById('cep-frete').value.trim();
    const resultadoFrete = document.getElementById('resultado-frete');

    cep = cep.replace(/\D/g, ''); // remove tudo que não é dígito

    if (cep.length !== 8) {
      alert("Digite um CEP válido com 8 dígitos (somente números).");
      return;
    }

    frete = await calcularFrete(cep);
    document.getElementById('valor-frete').textContent = formatarValor(frete);
    resultadoFrete.textContent = `Frete calculado: ${formatarValor(frete)}`;
    atualizarSubtotalFrete();
    atualizarTotalFinal();
  }

  document.getElementById('finalizar-compra').addEventListener('click', async () => {
  // Dados que você quer enviar para sessão
  const dadosCompra = {
    subtotal: subtotalCarrinho,
    frete: frete,
    desconto: desconto,
    totalFinal: (subtotalCarrinho + frete) * (1 - desconto / 100)
  };

  try {
    const response = await fetch('salvar_sessao_compra.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dadosCompra)
    });

    const result = await response.json();
    if (result.sucesso) {
      // Redireciona para página dos dados do cliente
      window.location.href = 'dadosclientes.php';
    } else {
      alert('Erro ao salvar os dados da compra no servidor.');
    }
  } catch (error) {
    alert('Erro de conexão com o servidor.');
  }
});


  function aplicarDesconto() {
    if (cupomAplicado) {
      alert('Você já aplicou um cupom nesta compra.');
      return;
    }

    const codigo = document.getElementById('codigo-desconto').value.trim();
    const resultado = document.getElementById('resultado-desconto');

    if (!codigo) {
      resultado.textContent = "Digite um código de cupom.";
      resultado.classList.remove('text-green-600');
      resultado.classList.add('text-red-600');
      return;
    }

    fetch('validar_cupom.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `codigo=${encodeURIComponent(codigo)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.sucesso) {
        desconto = data.desconto_percent;
        resultado.textContent = data.mensagem;
        resultado.classList.remove('text-red-600');
        resultado.classList.add('text-green-600');
        cupomAplicado = true;
        document.getElementById('codigo-desconto').disabled = true;
        document.getElementById('aplicar-desconto').disabled = true;
      } else {
        desconto = 0;
        resultado.textContent = data.mensagem;
        resultado.classList.remove('text-green-600');
        resultado.classList.add('text-red-600');
      }
      atualizarTotalFinal();
    })
    .catch(() => {
      resultado.textContent = "Erro ao validar o cupom.";
      resultado.classList.add('text-red-600');
      desconto = 0;
      atualizarTotalFinal();
    });
  }

  function atualizarSubtotalFrete() {
    const subtotalFrete = subtotalCarrinho + frete;
    document.getElementById('subtotal-frete').textContent = formatarValor(subtotalFrete);
  }

  function atualizarCarrinho() {
    const itens = JSON.parse(localStorage.getItem('carrinho')) || [];
    const lista = document.getElementById('itens-carrinho');
    let subtotal = 0;
    lista.innerHTML = '';

    itens.forEach((item, index) => {
      const li = document.createElement('li');
      li.classList.add("flex", "justify-between", "items-center", "bg-purple-50", "px-3", "py-2", "rounded-md");
      li.innerHTML = `
        <div class="flex-1">
          <span>${item.nome} x${item.quantidade} - ${formatarValor(item.preco * item.quantidade)}</span>
        </div>
        <div class="flex gap-2 items-center ml-2">
          <button class="btn-quantidade" data-index="${index}" data-delta="-1" aria-label="Diminuir quantidade">−</button>
          <button class="btn-quantidade" data-index="${index}" data-delta="1" aria-label="Aumentar quantidade">+</button>
          <button class="btn-remover" data-index="${index}" aria-label="Remover item">🗑️</button>
        </div>
      `;
      lista.appendChild(li);
      subtotal += item.preco * item.quantidade;
    });

    subtotalCarrinho = subtotal;
    document.getElementById('subtotal-carrinho').textContent = formatarValor(subtotalCarrinho);
    atualizarSubtotalFrete();
    atualizarTotalFinal();

    // Adiciona eventos aos botões depois de criar os elementos
    document.querySelectorAll('.btn-quantidade').forEach(btn => {
      btn.addEventListener('click', e => {
        const idx = Number(e.currentTarget.dataset.index);
        const delta = Number(e.currentTarget.dataset.delta);
        alterarQuantidade(idx, delta);
      });
    });

    document.querySelectorAll('.btn-remover').forEach(btn => {
      btn.addEventListener('click', e => {
        const idx = Number(e.currentTarget.dataset.index);
        removerItem(idx);
      });
    });
  }

  function alterarQuantidade(index, delta) {
    const itens = JSON.parse(localStorage.getItem('carrinho')) || [];
    if (itens[index]) {
      itens[index].quantidade += delta;
      if (itens[index].quantidade <= 0) {
        itens.splice(index, 1);
      }
      localStorage.setItem('carrinho', JSON.stringify(itens));
      atualizarCarrinho();
    }
  }

  function removerItem(index) {
    const itens = JSON.parse(localStorage.getItem('carrinho')) || [];
    if (itens[index]) {
      itens.splice(index, 1);
      localStorage.setItem('carrinho', JSON.stringify(itens));
      atualizarCarrinho();
    }
  }

  function atualizarTotalFinal() {
    const totalAntesDesconto = subtotalCarrinho + frete;
    const totalComDesconto = totalAntesDesconto * (1 - desconto / 100);

    const totalFinalEl = document.getElementById('total-final');
    const totalAntigoEl = document.getElementById('total-antigo');

    if (desconto > 0) {
      totalAntigoEl.textContent = formatarValor(totalAntesDesconto);
      totalAntigoEl.classList.remove('hidden');
      totalFinalEl.textContent = formatarValor(totalComDesconto);
    } else {
      totalAntigoEl.classList.add('hidden');
      totalFinalEl.textContent = formatarValor(totalAntesDesconto);
    }
  }
})();
</script>

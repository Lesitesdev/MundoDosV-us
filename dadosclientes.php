

<?php
session_start(); // Sempre no topo, antes de qualquer HTML ou include!
require 'conexao.php'; // Ajuste o caminho se necessário
include 'includes/header.php';

// Se o cliente não estiver logado, redireciona para login
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

$subtotal = $_SESSION['subtotal'] ?? 0;
$frete = $_SESSION['frete'] ?? 0;
$desconto = $_SESSION['desconto'] ?? 0;
$totalFinal = $_SESSION['totalFinal'] ?? 0;


$id_cliente = $_SESSION['cliente_id'];

// Busca dados do cliente (somente para preencher o formulário via PHP, mas vamos usar AJAX para isso no JS)
// Por via das dúvidas, podemos deixar este código para casos em que o JS falhe
$stmt = $pdo->prepare("SELECT nome, email, telefone FROM clientes WHERE id = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT rua AS logradouro, numero, bairro, cidade, estado, cep FROM enderecos WHERE cliente_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$id_cliente]);
$endereco = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<main class="max-w-3xl mx-auto mt-6 p-6 bg-white shadow-md rounded">
  <h2 class="text-2xl font-bold mb-6 text-center">Dados do Cliente</h2>

  <div id="resumo-compra" class="mb-8 p-4 border rounded bg-purple-50">
    <p>Subtotal: R$ <span id="subtotal"><?= number_format($subtotal, 2, ',', '.') ?></span></p>
<p>Frete: R$ <span id="frete"><?= number_format($frete, 2, ',', '.') ?></span></p>
<p>Desconto: R$ <span id="desconto"><?= number_format($desconto, 2, ',', '.') ?></span></p>
<p class="font-bold text-lg mt-2">Total: R$ <span id="totalFinal"><?= number_format($totalFinal, 2, ',', '.') ?></span></p>

  </div>

  <!-- Note que removemos o action do formulário -->
  <form method="POST" class="space-y-6" id="formCompra">
    <section>
      <h3 class="text-xl font-semibold mb-4">Dados Pessoais</h3>
      <input type="text" name="nome" placeholder="Nome completo" required
        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600 mb-4"
        value="<?= htmlspecialchars($cliente['nome'] ?? '') ?>" />
      <input type="email" name="email" placeholder="E-mail" required
        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600 mb-4"
        value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" />
      <input type="tel" name="telefone" placeholder="Telefone" required
        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600"
        value="<?= htmlspecialchars($cliente['telefone'] ?? '') ?>" />
    </section>

    <section>
      <h3 class="text-xl font-semibold my-4">Endereço</h3>
      <input type="text" name="logradouro" placeholder="Rua" required
        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600 mb-4"
        value="<?= htmlspecialchars($endereco['logradouro'] ?? '') ?>" />
      <div class="flex gap-4">
        <input type="text" name="numero" placeholder="Número" required
          class="flex-1 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600"
          value="<?= htmlspecialchars($endereco['numero'] ?? '') ?>" />
        <input type="text" name="bairro" placeholder="Bairro" required
          class="flex-1 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600"
          value="<?= htmlspecialchars($endereco['bairro'] ?? '') ?>" />
      </div>
      <div class="flex gap-4 mt-4">
        <input type="text" name="cidade" placeholder="Cidade" required
          class="flex-1 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600"
          value="<?= htmlspecialchars($endereco['cidade'] ?? '') ?>" />
        <input type="text" name="estado" placeholder="Estado" required
          class="w-24 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600"
          value="<?= htmlspecialchars($endereco['estado'] ?? '') ?>" />
        <input type="text" name="cep" placeholder="CEP" required
          class="flex-1 p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600"
          value="<?= htmlspecialchars($endereco['cep'] ?? '') ?>" />
      </div>
    </section>

    <section>
      <h3 class="text-xl font-semibold my-4">Forma de Pagamento</h3>
      <select name="formaPagamento" required
        class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-600">
        <option value="" disabled selected>Selecione a forma de pagamento</option>
        <option value="boleto">Boleto</option>
        <option value="cartao">Cartão</option>
        <option value="pix">PIX</option>
      </select>
    </section>

    <section>
      <h3 class="text-xl font-semibold my-4">Observações</h3>
      <textarea name="observacoes" placeholder="Observações" rows="4"
        class="w-full p-3 border border-gray-300 rounded resize-none focus:outline-none focus:ring-2 focus:ring-purple-600"></textarea>
    </section>

    <div class="flex justify-between items-center">
      <button type="button" onclick="history.back()"
        class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400 transition font-semibold">
        ← Voltar
      </button>

      <!-- Botão que dispara o JS, não envia o form diretamente -->
      <button id="btnFinalizarCompra" type="button"
        class="px-6 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 transition font-semibold">
        Finalizar Compra
      </button>
    </div>
  </form>
</main>

<?php include 'includes/footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Recupera valores do localStorage
    const subtotal = parseFloat(localStorage.getItem('subtotalCarrinho')) || 0;
    const frete = parseFloat(localStorage.getItem('frete')) || 0;
    const desconto = parseFloat(localStorage.getItem('desconto')) || 0;
    const totalFinal = parseFloat(localStorage.getItem('totalFinal')) || 0;

    // Preenche os campos no resumo
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('frete').textContent = frete.toFixed(2);
    document.getElementById('desconto').textContent = desconto.toFixed(2);
    document.getElementById('totalFinal').textContent = totalFinal.toFixed(2);

    // 2) Buscar dados do cliente no banco e preencher o formulário via AJAX (opcional)
    (async () => {
      try {
        const resp = await fetch('buscar_dados_cliente.php');
        const dados = await resp.json();

        if (dados.sucesso) {
          document.querySelector('input[name="nome"]').value = dados.cliente.nome || '';
          document.querySelector('input[name="email"]').value = dados.cliente.email || '';
          document.querySelector('input[name="telefone"]').value = dados.cliente.telefone || '';

          document.querySelector('input[name="logradouro"]').value = dados.endereco.logradouro || '';
          document.querySelector('input[name="numero"]').value = dados.endereco.numero || '';
          document.querySelector('input[name="bairro"]').value = dados.endereco.bairro || '';
          document.querySelector('input[name="cidade"]').value = dados.endereco.cidade || '';
          document.querySelector('input[name="estado"]').value = dados.endereco.estado || '';
          document.querySelector('input[name="cep"]').value = dados.endereco.cep || '';
        } else {
          console.warn('Não foi possível carregar dados do cliente:', dados.mensagem);
        }
      } catch (err) {
        console.error('Erro ao buscar dados do cliente:', err);
      }
    })();

    // 3) Listener do botão "Finalizar Compra"
    const btnFinalizar = document.getElementById('btnFinalizarCompra');
    btnFinalizar.addEventListener('click', async () => {
      const form = document.querySelector('form');

      // Se o formulário não for válido, mostra alerta nativo
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      // Monta dados do cliente a partir dos inputs
      const formData = new FormData(form);
      const dadosCliente = {
        nome: formData.get('nome'),
        email: formData.get('email'),
        telefone: formData.get('telefone'),
        endereco: {
          logradouro: formData.get('logradouro'),
          numero: formData.get('numero'),
          bairro: formData.get('bairro'),
          cidade: formData.get('cidade'),
          estado: formData.get('estado'),
          cep: formData.get('cep'),
        },
        formaPagamento: formData.get('formaPagamento'),
        observacoes: formData.get('observacoes'),
      };

      // Monta resumo da compra a partir do localStorage
      const resumoCompra = {
        subtotal: subtotal,
        frete: frete,
        desconto: desconto,
        totalFinal: totalFinal,
      };

      // Itens do carrinho vindos do localStorage
      const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
      if (carrinho.length === 0) {
        alert('Seu carrinho está vazio.');
        return;
      }

      // Monta o objeto completo do pedido
      const pedido = {
        cliente: dadosCliente,
        resumo: resumoCompra,
        itens: carrinho,
        dataPedido: new Date().toISOString(),
        status: 'pendente',
      };

      try {
        const response = await fetch('finalizar_compra.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(pedido),
        });

        const resultado = await response.json();
        console.log("Resposta recebida do PHP:", resultado);

        if (resultado.sucesso) {
          alert('Compra finalizada com sucesso!');
          localStorage.clear();
          window.location.href = 'agradecimento.php?pedido_id=' + resultado.pedido_id;
        } else {
          alert('Erro ao finalizar a compra: ' + (resultado.mensagem || 'Erro desconhecido.'));
        }
      } catch (error) {
        console.error("Erro na requisição:", error);
        alert('Erro na comunicação com o servidor: ' + error.message);
      }
    });
  });
</script>

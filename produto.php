<?php
include 'includes/header.php';
include 'admin/includes/conexao_admin.php';


// Verifica slug
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    echo "<div class='text-center text-red-500 mt-10'>Produto não encontrado.</div>";
    include 'includes/footer.php';
    exit;
}

// Busca produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE slug = :slug LIMIT 1");
$stmt->execute(['slug' => $slug]);
$produto = $stmt->fetch();


if (!$produto) {
    echo "<div class='text-center text-red-500 mt-10'>Produto não encontrado.</div>";
    include 'includes/footer.php';
    exit;
}

// Busca imagens extras
$stmtFotos = $pdo->prepare("SELECT arquivo FROM fotos_produto WHERE id_produto = :id");
$stmtFotos->execute(['id' => $produto['id']]);
$fotos = $stmtFotos->fetchAll();
?>

<main class="max-w-7xl mx-auto p-6 mt-8 bg-white rounded-xl shadow">
  <!-- Botão Voltar -->
  <button onclick="history.back()" class="mb-6 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-5 rounded-xl transition-colors">
    &larr; Voltar
  </button>

  <div class="grid md:grid-cols-2 gap-10">
    <!-- Galeria de imagens -->
    <div>
      <img id="imagem-principal" src="<?= htmlspecialchars($produto['imagem_principal']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="w-full h-auto rounded-xl shadow-lg mb-4 object-cover">


      <?php if ($fotos): ?>
      <div class="flex gap-3 overflow-x-auto">
        <?php foreach ($fotos as $foto): ?>
          <img src="uploads/produtos/<?= htmlspecialchars($foto['arquivo']) ?>" alt="Imagem extra" class="w-24 h-24 object-cover rounded cursor-pointer border hover:border-purple-600 transition-colors" onclick="document.getElementById('imagem-principal').src = this.src;">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Informações do produto -->
    <section>
      <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($produto['nome']) ?></h1>
      <p class="text-sm text-gray-500 mb-3">Categoria: <?= ucfirst(htmlspecialchars($produto['categoria'])) ?></p>
      <p class="text-2xl text-purple-700 font-semibold mb-5">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
      <p class="text-gray-700 leading-relaxed mb-6"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>

      <button class="adicionar-ao-carrinho bg-purple-700 hover:bg-purple-800 text-white font-semibold py-2 px-5 rounded-xl mb-4" data-nome="<?= htmlspecialchars($produto['nome']) ?>" data-preco="<?= $produto['preco'] ?>">
        Adicionar ao Carrinho
      </button>

      <button id="btn-calcular-frete" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl mb-4">
        Calcular Frete
      </button>

      <!-- Área de cálculo de frete -->
      <div id="calc-frete-container" class="hidden mt-4">
        <label for="cep" class="block mb-2 font-semibold">Digite seu CEP:</label>
        <input type="text" id="cep" maxlength="9" placeholder="Ex: 12345-678" class="border border-gray-300 rounded px-3 py-2 mb-3 w-full max-w-xs">
        <button id="btn-consultar-frete" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded">Consultar</button>
        <div id="resultado-frete" class="mt-4 text-gray-700 font-semibold"></div>
      </div>
    </section>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

<!-- Scripts -->
<script>
const carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];
const itensCarrinho = document.getElementById("itens-carrinho");
const totalCarrinho = document.getElementById("total-carrinho");
const carrinhoLateral = document.getElementById("carrinho-lateral");
const fecharCarrinhoBtn = document.getElementById("fechar-carrinho");

function adicionarAoCarrinho(produto) {
  const existente = carrinho.find(item => item.nome === produto.nome);
  if (existente) {
    existente.quantidade++;
  } else {
    carrinho.push({ ...produto, quantidade: 1 });
  }
  atualizarCarrinho();
  salvarCarrinho();
  exibirCarrinho();
}

function atualizarCarrinho() {
  if (!itensCarrinho || !totalCarrinho) return;
  itensCarrinho.innerHTML = "";
  let total = 0;

  carrinho.forEach((item, index) => {
    const subtotal = item.preco * item.quantidade;
    total += subtotal;

    const li = document.createElement("li");
    li.className = "flex justify-between items-center border-b pb-1";
    li.innerHTML = `
      <div>
        <p class="font-medium">${item.nome}</p>
        <small class="text-gray-500">Qtd: ${item.quantidade} x R$ ${item.preco.toFixed(2)}</small>
      </div>
      <strong class="text-purple-700">R$ ${subtotal.toFixed(2)}</strong>
      <div class="flex items-center space-x-2 ml-4">
        <button onclick="ajustarQuantidade(${index}, -1)" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">-</button>
        <span class="font-medium">${item.quantidade}</span>
        <button onclick="ajustarQuantidade(${index}, 1)" class="bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">+</button>
      </div>
    `;
    itensCarrinho.appendChild(li);
  });

  totalCarrinho.textContent = total.toFixed(2);
}

function ajustarQuantidade(index, delta) {
  const item = carrinho[index];
  if (!item) return;
  item.quantidade += delta;
  if (item.quantidade < 1) {
    carrinho.splice(index, 1);
  }
  atualizarCarrinho();
  salvarCarrinho();
}

function salvarCarrinho() {
  localStorage.setItem("carrinho", JSON.stringify(carrinho));
}

function exibirCarrinho() {
  if (carrinho.length > 0 && carrinhoLateral) {
    carrinhoLateral.classList.remove("hidden");
  }
}

function fecharCarrinho() {
  if (carrinhoLateral) carrinhoLateral.classList.add("hidden");
}

if (fecharCarrinhoBtn) fecharCarrinhoBtn.addEventListener("click", fecharCarrinho);

document.addEventListener('DOMContentLoaded', () => {
  atualizarCarrinho();
  if (carrinho.length > 0) exibirCarrinho();

  document.querySelectorAll('.adicionar-ao-carrinho').forEach(button => {
    button.addEventListener('click', () => {
      const nome = button.dataset.nome;
      const preco = parseFloat(button.dataset.preco);
      adicionarAoCarrinho({ nome, preco });
    });
  });

  document.getElementById("btn-calcular-frete")?.addEventListener("click", () => {
    document.getElementById("calc-frete-container")?.classList.remove("hidden");
  });
});
</script>

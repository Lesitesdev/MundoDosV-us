<?php
include 'includes/header.php';
include 'admin/includes/conexao_admin.php';

// Buscar todos os produtos
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome");
$produtos = $stmt->fetchAll();
?>

<section class="p-6 bg-white max-w-7xl mx-auto mt-8">
  <h1 class="text-2xl font-bold mb-4">Todos os Véus</h1>

  <!-- Filtros -->
  <div class="flex flex-wrap gap-4 mb-6">
    <button type="button" class="filtro-btn px-4 py-2 bg-purple-700 text-white rounded-full" onclick="filtrarProdutos('todos', this)">Todos</button>
    <?php
    $categorias = $pdo->query("SELECT DISTINCT categoria FROM produtos ORDER BY categoria")->fetchAll();
    foreach ($categorias as $cat):
      $categoria = htmlspecialchars($cat['categoria']);
      $categoriaId = strtolower(str_replace(' ', '-', $categoria));
    ?>
      <button type="button" class="filtro-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-full" onclick="filtrarProdutos('<?= $categoriaId ?>', this)">
        <?= ucfirst($categoria) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Lista de Produtos -->
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 text-sm" id="lista-produtos">
   <?php foreach ($produtos as $produto):
  $nome = htmlspecialchars($produto['nome']);
  $categoria = htmlspecialchars($produto['categoria']);
  $slug = htmlspecialchars($produto['slug']);
  $imagem = htmlspecialchars($produto['imagem_principal']); // corrigido aqui
  $preco = number_format($produto['preco'], 2, ',', '.');
  $categoriaId = strtolower(str_replace(' ', '-', $categoria));
?>
  <div data-categoria="<?= $categoriaId ?>" class="produto border rounded-xl shadow p-2 hover:shadow-lg transition">
    <div class="w-full h-48 flex items-center justify-center overflow-hidden bg-gray-100 rounded-lg border mb-2">
  <img src="<?= $imagem ?>" alt="<?= $nome ?>" class="object-cover w-full h-full transition-transform duration-300 hover:scale-105" />
</div>

    <h2 class="font-semibold"><?= $nome ?></h2>
    <p class="text-xs text-gray-500 mb-1"><?= ucfirst($categoria) ?></p>
    <p class="text-gray-600">R$ <?= $preco ?></p>
    <a href="produto.php?slug=<?= $slug ?>" class="text-purple-700 hover:underline font-semibold">Ver mais</a>
    <button type="button"
      class="adicionar-ao-carrinho mt-2 px-2 py-1 bg-purple-700 text-white rounded text-xs w-full"
      data-nome="<?= $nome ?>"
      data-preco="<?= $produto['preco'] ?>">
      Adicionar ao carrinho
    </button>
  </div>
<?php endforeach; ?>

  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script type="module" src="/header.js"></script>

<script>
// Filtro de Produtos
function filtrarProdutos(categoria, botao) {
  document.querySelectorAll('.produto').forEach(produto => {
    const cat = produto.dataset.categoria;
    produto.style.display = (categoria === 'todos' || cat === categoria) ? 'block' : 'none';
  });

  document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.classList.remove('bg-purple-700', 'text-white');
    btn.classList.add('bg-gray-200', 'text-gray-800');
  });

  botao.classList.add('bg-purple-700', 'text-white');
  botao.classList.remove('bg-gray-200', 'text-gray-800');
}

// Mostrar todos os produtos ao carregar e marcar botão ativo corretamente
document.addEventListener('DOMContentLoaded', () => {
  const btnTodos = document.querySelector('.filtro-btn');
  filtrarProdutos('todos', btnTodos);

  // Configurar evento dos botões "Adicionar ao carrinho"
  document.querySelectorAll('.adicionar-ao-carrinho').forEach(button => {
    button.addEventListener('click', () => {
      const nome = button.dataset.nome;
      const preco = parseFloat(button.dataset.preco);
      adicionarAoCarrinho({ nome, preco });
    });
  });

  atualizarCarrinho();
  if (carrinho.length > 0) exibirCarrinho();
});
</script>

<script>
// Carrinho de Compras
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
  salvarCarrinho();
  atualizarCarrinho();
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
  salvarCarrinho();
  atualizarCarrinho();
  if (carrinho.length === 0) fecharCarrinho();
}

function salvarCarrinho() {
  localStorage.setItem("carrinho", JSON.stringify(carrinho));
}

function exibirCarrinho() {
  if (carrinhoLateral) carrinhoLateral.classList.remove("hidden");
}

function fecharCarrinho() {
  if (carrinhoLateral) carrinhoLateral.classList.add("hidden");
}

if (fecharCarrinhoBtn) fecharCarrinhoBtn.addEventListener("click", fecharCarrinho);
</script>

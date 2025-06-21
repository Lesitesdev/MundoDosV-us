<?php 
include 'includes/header.php';
include 'conexao.php';

// Ativa depuração de imagem se necessário
$mostrarDebugImagem = true;

// Busca os modelos cadastrados (usados como "Nossos Produtos" na Home)
try {
 $stmt = $pdo->query("SELECT id, titulo AS nome, link, imagem AS imagem_principal FROM nossos_produtos ORDER BY id DESC");
$nossosProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $nossosProdutos = [];
  if ($mostrarDebugImagem) {
    echo "<!-- Erro ao buscar modelos: " . $e->getMessage() . " -->";
  }
}
?>

<!-- Banner com Swiper -->
<section class="relative w-full h-[450px]">
  <div class="swiper h-full">
    <div class="swiper-wrapper">
      <!-- Slide 1 -->
      <div class="swiper-slide relative bg-cover bg-center" style="background-image: url('imagens/sobre 1.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center px-4">
          <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Coleção Exclusiva</h1>
          <p class="text-lg text-white mb-6">Véus com detalhes únicos pra sua fé e estilo</p>
          <a href="produtos.php" class="bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-lg text-lg transition">
            Compre Agora
          </a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="swiper-slide relative bg-cover bg-center" style="background-image: url('imagens/frete gratis.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center px-4">
          <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Frete Grátis</h1>
          <p class="text-lg text-white mb-6">Para todo o Brasil em pedidos acima de R$150</p>
          <a href="produtos.php" class="bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-lg text-lg transition">
            Ver Ofertas
          </a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="swiper-slide relative bg-cover bg-center" style="background-image: url('imagens/Novidades.jpg');">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center px-4">
          <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Novidades Toda Semana</h1>
          <p class="text-lg text-white mb-6">Lançamentos especiais direto da nossa coleção</p>
          <a href="produtos.php" class="bg-purple-700 hover:bg-purple-800 text-white font-semibold py-3 px-6 rounded-lg text-lg transition">
            Confira
          </a>
        </div>
      </div>
    </div>

    <!-- Botões de navegação -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>

    <!-- Paginação -->
    <div class="swiper-pagination"></div>
  </div>
</section>

<!-- Destaques -->
<section class="flex flex-col md:flex-row items-center justify-around py-8 bg-purple-50 text-purple-900 text-center gap-6">
  <div>
    <div class="text-3xl mb-2">🚚</div>
    <p class="font-semibold">Frete Grátis</p>
    <p class="text-sm">A partir de R$150</p>
  </div>
  <div>
    <div class="text-3xl mb-2">🆕</div>
    <p class="font-semibold">Lançamentos</p>
    <p class="text-sm">Toda semana novidades</p>
  </div>
  <div>
    <div class="text-3xl mb-2">💰</div>
    <p class="font-semibold">Promoções</p>
    <p class="text-sm">Descontos imperdíveis</p>
  </div>
</section>

<!-- Nossos Produtos (Modelos dinâmicos) -->
<section class="py-10 bg-gray-50 text-center">
  <h2 class="text-3xl font-bold mb-8 text-purple-800">Nossos Produtos</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-6xl mx-auto px-4">

    <?php if (empty($nossosProdutos)): ?>
      <p class="col-span-full">Nenhum produto encontrado.</p>
    <?php else: ?>
      <?php foreach ($nossosProdutos as $produto): ?>

       <?php
  $imagemBanco = ltrim($produto['imagem_principal'] ?? '', '/\\');
  $pastaBase = '/mundodosveus/uploads/modelos/'; // OU apenas '/uploads/modelos/' se estiver na raiz do site
  $url_imagem = 'uploads/modelos/' . $produto['imagem_principal'];


  $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
  $caminho_absoluto = $documentRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'mundodosveus/uploads/modelos/' . $imagemBanco);

  $existe_imagem = file_exists($caminho_absoluto) && is_file($caminho_absoluto);
?>


        <a href="<?= htmlspecialchars($produto['link'] ?? '#') ?>" class="group bg-white rounded-xl border hover:shadow-xl transition duration-300 flex flex-col items-center p-4">
         <div class="w-full h-48 flex items-center justify-center overflow-hidden bg-gray-100 rounded-lg border mb-3">
  <img src="<?= htmlspecialchars($url_imagem) ?>" 
       alt="<?= htmlspecialchars($produto['nome'] ?? '') ?>" 
       class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105" />


            <?php 
              if ($existe_imagem && $imagemBanco !== '') {
                echo "<img src='" . htmlspecialchars($url_imagem) . "' alt='" . htmlspecialchars($produto['nome'] ?? '') . "' class='w-full h-full object-cover transition-transform duration-300 group-hover:scale-105' />";
              } else {
                echo "<p class='text-red-500 text-sm'>Imagem não encontrada</p>";
              }

              if ($mostrarDebugImagem) {
                echo "<!-- URL imagem: {$url_imagem} -->";
                echo "<!-- Caminho no servidor: {$caminho_absoluto} -->";
                echo "<!-- Arquivo existe? " . ($existe_imagem ? 'SIM' : 'NÃO') . " -->";
              }
            ?>
          </div>
          <h3 class="text-base font-medium text-gray-800 group-hover:text-purple-700">
            <?= htmlspecialchars($produto['nome'] ?? '') ?>
          </h3>
        </a>

      <?php endforeach ?>
    <?php endif ?>

  </div>
</section>






<?php
// Buscar produtos em destaque
$stmt = $pdo->prepare("SELECT id, nome AS titulo, preco, imagem_principal AS imagem FROM produtos WHERE destaque_home = 1 ORDER BY id DESC");
$stmt->execute();
$produtos = $stmt->fetchAll();
?>

<!-- Produtos em destaque -->
<section class="py-12 bg-white text-center" id="destaques">
  <h2 class="text-3xl font-bold mb-8 text-purple-800">Produtos em Destaque</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto px-4">
    <?php foreach ($produtos as $p): 
      // Certifique-se que $p['slug'] existe e não está vazio
      $slug = !empty($p['slug']) ? htmlspecialchars($p['slug']) : null;

      // Se não existir slug, cria um link vazio ou para outra página (ex: '#')
      $urlProduto = $slug ? "produto.php?slug=$slug" : "#";
    ?>
      <div class="bg-white border rounded-xl shadow-md p-4 flex flex-col items-center relative group overflow-hidden">
        <a href="<?= $urlProduto ?>" class="absolute inset-0 z-10" aria-label="Ver detalhes do produto <?= htmlspecialchars($p['titulo']) ?>"></a>

        <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="mb-4 rounded-lg w-52 h-52 object-cover relative z-20" />
        <h3 class="text-xl font-semibold text-purple-700 relative z-20"><?= htmlspecialchars($p['titulo']) ?></h3>
        <p class="text-lg text-gray-700 my-2 relative z-20">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>

        <button class="adicionar-ao-carrinho bg-purple-600 text-white py-2 px-6 rounded-full hover:bg-purple-700 relative z-30 mt-2"
                data-nome="<?= htmlspecialchars($p['titulo']) ?>"
                data-preco="<?= number_format($p['preco'], 2, '.', '') ?>">
          Adicionar ao Carrinho
        </button>
      </div>
    <?php endforeach; ?>
  </div>
</section>


     
      

</section>

<section class="bg-purple-50 py-12 text-center">
  <h2 class="text-3xl font-bold mb-8 text-purple-800">O que dizem nossas clientes</h2>
  <div class="max-w-4xl mx-auto grid gap-8 md:grid-cols-3 px-4">
    <div class="bg-white p-6 rounded-xl shadow">
      <p class="text-gray-700 mb-4 italic">"Amei o véu! Chegou super rápido e é ainda mais lindo pessoalmente."</p>
      <div class="text-yellow-400 text-xl">★★★★★</div>
      <p class="font-semibold mt-2">Juliana R.</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow">
      <p class="text-gray-700 mb-4 italic">"Excelente qualidade e acabamento impecável. Recomendo demais!"</p>
      <div class="text-yellow-400 text-xl">★★★★★</div>
      <p class="font-semibold mt-2">Camila S.</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow">
      <p class="text-gray-700 mb-4 italic">"Comprei para um presente e foi sucesso! Atendimento maravilhoso."</p>
      <div class="text-yellow-400 text-xl">★★★★★</div>
      <p class="font-semibold mt-2">Renata M.</p>
    </div>
  </div>
</section>

<section class="bg-white py-12 text-center">
  <h2 class="text-3xl font-bold mb-8 text-purple-800">Por que comprar no Mundo dos Véus?</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-6xl mx-auto px-4">
    <div>
      <div class="text-3xl mb-2">🌎</div>
      <p class="font-semibold">Entrega para todo Brasil</p>
    </div>
    <div>
      <div class="text-3xl mb-2">💳</div>
      <p class="font-semibold">Pagamento via Mercado Pago</p>
    </div>
    <div>
      <div class="text-3xl mb-2">📞</div>
      <p class="font-semibold">Atendimento personalizado</p>
    </div>
    <div>
      <div class="text-3xl mb-2">🔄</div>
      <p class="font-semibold">Troca fácil</p>
    </div>
  </div>
</section>

<section class="bg-purple-100 py-12 text-center">
  <h2 class="text-3xl font-bold text-purple-800 mb-4">Ganhe 10% de desconto!</h2>
  <p class="text-gray-700 mb-6">Cadastre-se na nossa newsletter e receba um cupom exclusivo + novidades semanais.</p>
  <form class="flex flex-col md:flex-row justify-center items-center gap-4 max-w-xl mx-auto px-4">
    <a href="telacadastro.php" class="bg-purple-700 hover:bg-purple-800 text-white font-semibold px-6 py-2 rounded-lg inline-block">
      Cadastrar
    </a>
  </form>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Carrinho de Compras -->
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
    if (carrinho.length > 0) {
      carrinhoLateral.classList.remove("hidden");
    }
  }

  function fecharCarrinho() {
    carrinhoLateral.classList.add("hidden");
  }

  fecharCarrinhoBtn.addEventListener("click", fecharCarrinho);

  atualizarCarrinho();

  document.querySelectorAll('.adicionar-ao-carrinho').forEach(button => {
    button.addEventListener('click', () => {
      const nome = button.getAttribute('data-nome');
      const preco = parseFloat(button.getAttribute('data-preco'));
      const produto = { nome, preco };
      adicionarAoCarrinho(produto);
    });
  });

  if (carrinho.length > 0) {
    exibirCarrinho();
  }
</script>

<!-- Swiper.js -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
  const swiper = new Swiper('.swiper', {
    loop: true,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
  });
</script>

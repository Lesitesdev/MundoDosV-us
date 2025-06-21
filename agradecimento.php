<?php 
include 'conexao.php'; // Conexão com PDO
include 'includes/header.php';  // Cabeçalho

$pedido_id = $_GET['pedido_id'] ?? null;

if (!$pedido_id) {
    echo '<main class="max-w-3xl mx-auto mt-6 p-6 bg-white shadow-md rounded">';
    echo '<p class="text-red-500 font-semibold">Número do pedido não informado.</p>';
    echo '</main>';
    include 'includes/footer.php';
    exit;
}

// Buscar pedido com PDO
$sql = "SELECT * FROM pedidos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $pedido_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    echo '<main class="max-w-3xl mx-auto mt-6 p-6 bg-white shadow-md rounded">';
    echo '<p class="text-red-500 font-semibold">Pedido não encontrado.</p>';
    echo '</main>';
    include 'includes/footer.php';
    exit;
}

$itens = json_decode($pedido['itens'], true);
$enderecoFormatado = "{$pedido['logradouro']}, {$pedido['numero']} - {$pedido['bairro']}, {$pedido['cidade']} - {$pedido['estado']}, CEP: {$pedido['cep']}";

// Cálculos de valores
$subtotal = 0;
foreach ($itens as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}

$frete = floatval($pedido['frete'] ?? 15.00); // Valor do frete salvo no pedido ou padrão
$desconto = floatval($pedido['desconto'] ?? 0.00); // Valor do desconto salvo no pedido (cupom)
$total_final = $subtotal + $frete - $desconto;
?>

<main class="max-w-3xl mx-auto mt-6 p-6 bg-white shadow-md rounded">
  <h1 class="text-2xl font-bold mb-6 text-purple-700">Obrigado pela sua compra!</h1>

  <section id="resumo-pedido" class="text-gray-800">
    <p class="text-lg font-semibold mb-4"><strong>Número do Pedido:</strong> <?= htmlspecialchars($pedido_id) ?></p>

    <h2 class="text-xl font-semibold mb-4">Resumo do Pedido</h2>

    <div id="itens-pedido" class="mb-4">
      <?php
      foreach ($itens as $item) {
          $itemTotal = $item['preco'] * $item['quantidade'];
          echo "<p>{$item['nome']} — {$item['quantidade']}x R$ " . number_format($item['preco'], 2, ',', '.') . " = R$ " . number_format($itemTotal, 2, ',', '.') . "</p>";
      }
      ?>
    </div>

    <p class="font-semibold">Subtotal: <span id="subtotal">R$ <?= number_format($subtotal, 2, ',', '.') ?></span></p>
    <p>Frete: <span id="frete">R$ <?= number_format($frete, 2, ',', '.') ?></span></p>

    <?php if ($desconto > 0): ?>
      <p class="text-green-600 font-semibold">Desconto: - R$ <?= number_format($desconto, 2, ',', '.') ?></p>
    <?php endif; ?>

    <p class="text-lg font-bold mt-2">Total com desconto: <span id="total">R$ <?= number_format($total_final, 2, ',', '.') ?></span></p>

    <hr class="my-6">

    <h3 class="text-lg font-semibold mb-2">Dados para Entrega</h3>
    <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['nome']) ?></p>
    <p><strong>E-mail:</strong> <?= htmlspecialchars($pedido['email']) ?></p>
    <p><strong>Telefone:</strong> <?= htmlspecialchars($pedido['telefone']) ?></p>
    <p><strong>Endereço:</strong> <?= htmlspecialchars($enderecoFormatado) ?></p>

    <h3 class="text-lg font-semibold mt-6 mb-2">Forma de Pagamento</h3>
    <p><?= htmlspecialchars(ucfirst($pedido['forma_pagamento'])) ?></p>

    <h3 class="text-lg font-semibold mt-6 mb-2">Observações</h3>
    <p><?= nl2br(htmlspecialchars($pedido['observacoes'] ?: 'Nenhuma observação.')) ?></p>
  </section>

  <div class="mt-8 text-center">
    <a href="index.php" class="inline-block bg-purple-700 text-white px-6 py-3 rounded hover:bg-purple-800 transition">
      Voltar para a loja
    </a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

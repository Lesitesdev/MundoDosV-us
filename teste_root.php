<?php
include 'admin/includes/conexao_admin.php'; // arquivo que cria a variável $pdo

$stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome");
$nossosProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
  <h2>Nossos Produtos</h2>

  <?php if (empty($nossosProdutos)): ?>
    <p>Nenhum produto encontrado.</p>
  <?php else: ?>
    <?php foreach ($nossosProdutos as $produto): ?>
      <div>
        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
        <p>Imagem: <?= htmlspecialchars($produto['imagem']) ?></p>
      </div>
    <?php endforeach ?>
  <?php endif ?>
</section>

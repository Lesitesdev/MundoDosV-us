<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexao.php'; // Conexão com PDO em $pdo

$nomeCliente = null;

if (isset($_SESSION['cliente_id'])) {
    $stmt = $pdo->prepare("SELECT nome FROM clientes WHERE id = ?");
    $stmt->execute([$_SESSION['cliente_id']]);
    $cliente = $stmt->fetch();

    if ($cliente) {
        $nomeCliente = $cliente['nome'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Mundo dos Véus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

  <style>
    html { scroll-behavior: smooth; }
    .search-box input {
      padding: 6px 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
  </style>
</head>

<body class="bg-white text-gray-800">

  <header class="bg-white shadow-md px-6 py-4">
    <div class="max-w-6xl mx-auto flex items-center">
      <!-- Logo -->
      <a href="index.php" class="text-2xl font-bold text-purple-700">Mundo dos Véus</a>

      <!-- Navegação -->
      <nav class="ml-8 hidden md:flex space-x-6">
        <a href="index.php" class="text-gray-700 hover:text-purple-700">Início</a>
        <a href="produtos.php" class="text-gray-700 hover:text-purple-700">Produtos</a>
        <a href="sobre.php" class="text-gray-700 hover:text-purple-700">Sobre</a>
        <a href="sobre.php#contato" class="text-gray-700 hover:text-purple-700">Contato</a>

        <?php if (isset($_SESSION['cliente_id'])): ?>
          <a href="cliente.php" class="text-gray-700 hover:text-purple-700">Meu Cadastro</a>
        <?php endif; ?>
      </nav>

      <div class="flex-1"></div>

      <!-- Carrinho -->
      <a href="carrinho.php" class="text-2xl text-gray-700 hover:text-purple-700 mr-6">🛒</a>

      <!-- Usuário logado -->
      <?php if (isset($_SESSION['cliente_id'])): ?>
        <div class="flex items-center space-x-2">
          <span class="text-gray-700">Olá, <?= htmlspecialchars($nomeCliente) ?>!</span>
          <a href="logout.php" class="text-gray-700 hover:text-purple-700">🚪 Logout</a>
        </div>
      <?php else: ?>
        <a href="login.php" class="text-gray-700 hover:text-purple-700">👤 Login</a>
      <?php endif; ?>
    </div>
  </header>

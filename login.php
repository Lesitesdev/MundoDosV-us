<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha e-mail e senha.";
    } else {
        $stmt = $pdo->prepare("SELECT id, senha FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['cliente_id'] = $usuario['id'];
            header('Location: cliente.php');
            exit;
        } else {
            $erro = "Email ou senha inválidos.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="flex items-center justify-center py-20 px-4">
  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-center text-pink-700 mb-6">Entrar na sua conta</h2>

    <?php if (!empty($erro)): ?>
      <div class="text-red-600 text-center text-sm mb-4"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <div class="mb-4">
        <label class="block mb-1 text-sm font-medium text-gray-700">E-mail</label>
        <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-200 focus:outline-none" placeholder="voce@exemplo.com" required />
      </div>
      <div class="mb-6">
        <label class="block mb-1 text-sm font-medium text-gray-700">Senha</label>
        <input type="password" name="senha" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-200 focus:outline-none" placeholder="********" required autocomplete="current-password" />
      </div>
      <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 rounded-lg transition">
        Entrar
      </button>
    </form>

    <p class="mt-4 text-sm text-center text-gray-600">
      Ainda não tem conta?
      <a href="telacadastro.php" class="text-pink-600 hover:underline font-medium">Cadastre-se aqui</a>
    </p>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

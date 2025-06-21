<?php
include __DIR__ . '/conexao.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $cpf = trim($_POST['cpf']);
    $cep = trim($_POST['cep']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);

    // Validações básicas
    if (strlen($senha) < 6) {
        die("Senha deve ter ao menos 6 caracteres.");
    }

    // Hash da senha para segurança
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se email já existe
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("Este e-mail já está cadastrado.");
    }

    // Insere no banco
    $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, senha, cpf, cep, rua, numero, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sucesso = $stmt->execute([$nome, $email, $senhaHash, $cpf, $cep, $rua, $numero, $cidade, $estado]);

    if ($sucesso) {
        $_SESSION['cliente_id'] = $pdo->lastInsertId();
$_SESSION['cliente_nome'] = $nome;

        header("Location: cliente.php");
        exit;
    } else {
        echo "Erro ao cadastrar. Tente novamente.";
    }
} else {
    header("Location: cadastro.php"); // redireciona para o formulário se acessado sem POST
    exit;
}
?>

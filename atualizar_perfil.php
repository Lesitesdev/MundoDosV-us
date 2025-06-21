<?php
session_start();
include 'includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        exit('Usuário não autenticado.');
    }

    $id = $_SESSION['usuario_id'];
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $rg = $_POST['rg'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $cep = $_POST['cep'] ?? '';
    $rua = $_POST['rua'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    } else {
        // Busca a senha atual para manter
        $stmt = $pdo->prepare("SELECT senha FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        $senhaHash = $usuario['senha'] ?? '';
    }

    $sql = "UPDATE clientes SET
      nome = :nome,
      email = :email,
      senha = :senha,
      cpf = :cpf,
      rg = :rg,
      telefone = :telefone,
      data_nascimento = :data_nascimento,
      cep = :cep,
      rua = :rua,
      numero = :numero,
      complemento = :complemento,
      bairro = :bairro,
      cidade = :cidade,
      estado = :estado,
      atualizado_em = NOW()
    WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaHash,
        ':cpf' => $cpf,
        ':rg' => $rg,
        ':telefone' => $telefone,
        ':data_nascimento' => $data_nascimento,
        ':cep' => $cep,
        ':rua' => $rua,
        ':numero' => $numero,
        ':complemento' => $complemento,
        ':bairro' => $bairro,
        ':cidade' => $cidade,
        ':estado' => $estado,
        ':id' => $id,
    ]);

    echo "Perfil atualizado com sucesso!";
}
?>


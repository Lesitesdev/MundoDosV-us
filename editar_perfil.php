<?php
session_start();
include 'includes/conexao.php'; // sua conexão PDO

// Verifica se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$msg = '';

// Atualiza os dados se formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pegue os dados do POST, fazendo validações básicas
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $rg = trim($_POST['rg'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $rua = trim($_POST['rua'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');

    // Aqui você pode adicionar validações específicas (email válido, CPF válido etc)

    // Atualiza o banco
    $sql = "UPDATE clientes SET
        nome = ?, email = ?, cpf = ?, rg = ?, telefone = ?, data_nascimento = ?, cep = ?, rua = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?
        WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nome, $email, $cpf, $rg, $telefone, $data_nascimento, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado,
        $usuario_id
    ]);

    $msg = "Dados atualizados com sucesso!";
}

// Busca os dados do usuário para preencher o formulário
$stmt = $pdo->prepare("SELECT nome, email, cpf, rg, telefone, data_nascimento, cep, rua, numero, complemento, bairro, cidade, estado FROM clientes WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Editar Perfil</title>
  <style>
    label { display: block; margin-top: 10px; }
    input, select { width: 300px; padding: 6px; margin-top: 2px; }
    button { margin-top: 15px; padding: 10px 20px; background: #e91e63; color: white; border: none; cursor: pointer; }
    .msg { margin-top: 10px; color: green; }
  </style>
</head>
<body>

<h1>Editar Perfil</h1>

<?php if ($msg): ?>
    <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<form method="POST" action="editar_perfil.php">
    <label>Nome:
      <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required />
    </label>

    <label>E-mail:
      <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required />
    </label>

    <label>CPF:
      <input type="text" name="cpf" value="<?= htmlspecialchars($usuario['cpf']) ?>" />
    </label>

    <label>RG:
      <input type="text" name="rg" value="<?= htmlspecialchars($usuario['rg']) ?>" />
    </label>

    <label>Telefone:
      <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" />
    </label>

    <label>Data de Nascimento:
      <input type="date" name="data_nascimento" value="<?= htmlspecialchars($usuario['data_nascimento']) ?>" />
    </label>

    <label>CEP:
      <input type="text" name="cep" value="<?= htmlspecialchars($usuario['cep']) ?>" />
    </label>

    <label>Rua:
      <input type="text" name="rua" value="<?= htmlspecialchars($usuario['rua']) ?>" />
    </label>

    <label>Número:
      <input type="text" name="numero" value="<?= htmlspecialchars($usuario['numero']) ?>" />
    </label>

    <label>Complemento:
      <input type="text" name="complemento" value="<?= htmlspecialchars($usuario['complemento']) ?>" />
    </label>

    <label>Bairro:
      <input type="text" name="bairro" value="<?= htmlspecialchars($usuario['bairro']) ?>" />
    </label>

    <label>Cidade:
      <input type="text" name="cidade" value="<?= htmlspecialchars($usuario['cidade']) ?>" />
    </label>

    <label>Estado:
      <select name="estado">
        <?php
        $estados = ["AC","AL","AP","AM","BA","CE","DF","ES","GO","MA","MT","MS","MG","PA","PB","PR","PE","PI","RJ","RN","RS","RO","RR","SC","SP","SE","TO"];
        foreach ($estados as $uf) {
            $sel = ($usuario['estado'] === $uf) ? "selected" : "";
            echo "<option value='$uf' $sel>$uf</option>";
        }
        ?>
      </select>
    </label>

    <button type="submit">Salvar alterações</button>
</form>

</body>
</html>

<?php
session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

$cliente_nome = $_SESSION['cliente_nome'] ?? 'Visitante';
echo "Bem-vindo, " . htmlspecialchars($cliente_nome);

require 'conexao.php';
include 'includes/header.php';

$cliente_id = $_SESSION['cliente_id'];

$msg_sucesso = '';
$msg_erro = '';


// Processar POST (atualizar perfil, endereços, cancelar pedido)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Cancelar pedido
    if (isset($_POST['cancelar_pedido_id'])) {
        $pedidoIdCancelar = intval($_POST['cancelar_pedido_id']);
        // Verifica se pedido pertence ao cliente
        $verifica = $pdo->prepare("SELECT id FROM pedidos WHERE id = ? AND cliente_id = ?");
        $verifica->execute([$pedidoIdCancelar, $cliente_id]);
        if ($verifica->fetch()) {
            $cancelar = $pdo->prepare("UPDATE pedidos SET status = 'cancelado' WHERE id = ?");
            if ($cancelar->execute([$pedidoIdCancelar])) {
                $msg_sucesso = "Pedido cancelado com sucesso.";
            } else {
                $msg_erro = "Erro ao cancelar pedido.";
            }
        } else {
            $msg_erro = "Pedido não encontrado ou não pertence a você.";
        }
    }

    // Editar perfil
    if (isset($_POST['editar_perfil'])) {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $celular  = trim($_POST['celular'] ?? '');

        if (
            empty($nome) ||
            empty($email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            (empty($telefone) && empty($celular))
        ) {
            $msg_erro = "Nome, e-mail válidos e ao menos um telefone ou celular são obrigatórios.";
        } else {
            $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, celular = ? WHERE id = ?");
            if ($stmt->execute([$nome, $email, $telefone, $celular, $cliente_id])) {
                $msg_sucesso = "Dados pessoais atualizados com sucesso.";
            } else {
                $msg_erro = "Erro ao atualizar dados pessoais.";
            }
        }
    }

    // Editar endereço
    if (isset($_POST['editar_endereco'])) {
        $endereco_id = intval($_POST['endereco_id'] ?? 0);
        $rua         = trim($_POST['rua'] ?? '');
        $numero      = trim($_POST['numero'] ?? '');
        $complemento = trim($_POST['complemento'] ?? '');
        $bairro      = trim($_POST['bairro'] ?? '');
        $cidade      = trim($_POST['cidade'] ?? '');
        $estado      = trim($_POST['estado'] ?? '');
        $cep         = trim($_POST['cep'] ?? '');

        if ($endereco_id <= 0 || empty($rua) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado) || empty($cep)) {
            $msg_erro = "Todos os campos obrigatórios do endereço devem ser preenchidos.";
        } else {
            $stmt = $pdo->prepare("
                UPDATE enderecos SET rua = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ?, cep = ?
                WHERE id = ? AND cliente_id = ?
            ");
            if ($stmt->execute([$rua, $numero, $complemento, $bairro, $cidade, $estado, $cep, $endereco_id, $cliente_id])) {
                $msg_sucesso = "Endereço atualizado com sucesso.";
            } else {
                $msg_erro = "Erro ao atualizar endereço.";
            }
        }
    }

    // Adicionar endereço novo
    if (isset($_POST['adicionar_endereco'])) {
        $rua         = trim($_POST['rua_novo'] ?? '');
        $numero      = trim($_POST['numero_novo'] ?? '');
        $complemento = trim($_POST['complemento_novo'] ?? '');
        $bairro      = trim($_POST['bairro_novo'] ?? '');
        $cidade      = trim($_POST['cidade_novo'] ?? '');
        $estado      = trim($_POST['estado_novo'] ?? '');
        $cep         = trim($_POST['cep_novo'] ?? '');

        if (empty($rua) || empty($numero) || empty($bairro) || empty($cidade) || empty($estado) || empty($cep)) {
            $msg_erro = "Todos os campos obrigatórios devem ser preenchidos para o novo endereço.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO enderecos (cliente_id, rua, numero, complemento, bairro, cidade, estado, cep)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            if ($stmt->execute([$cliente_id, $rua, $numero, $complemento, $bairro, $cidade, $estado, $cep])) {
                $msg_sucesso = "Novo endereço adicionado com sucesso.";
            } else {
                $msg_erro = "Erro ao adicionar novo endereço.";
            }
        }
    }
}

// Buscar dados pessoais atualizados
$stmt = $pdo->prepare("SELECT nome, email, telefone, celular, ultimo_acesso FROM clientes WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar histórico de pedidos
$stmt = $pdo->prepare("SELECT id, data_pedido, status FROM pedidos WHERE cliente_id = ? ORDER BY data_pedido DESC");
$stmt->execute([$cliente_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar produtos favoritos
$stmt = $pdo->prepare("
    SELECT p.id, p.nome,p.imagem_principal
    FROM favoritos f
    JOIN produtos p ON f.produto_id = p.id
    WHERE f.cliente_id = ?
");
$stmt->execute([$cliente_id]);
$favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar endereços
$stmt = $pdo->prepare("SELECT id, rua, numero, complemento, bairro, cidade, estado, cep FROM enderecos WHERE cliente_id = ?");
$stmt->execute([$cliente_id]);
$enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variáveis para controle da edição
$editar_dados = isset($_GET['editar_dados']) && $_GET['editar_dados'] == 1;
$editar_endereco_id = isset($_GET['editar_endereco']) ? intval($_GET['editar_endereco']) : 0;

?>

<!-- HTML do conteúdo da página abaixo -->

<main class="py-10 px-4 bg-gray-100 min-h-screen">
  <div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-pink-700 mb-6">Minha Conta</h1>

    <?php if ($msg_sucesso): ?>
      <p class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        <?= htmlspecialchars($msg_sucesso) ?>
      </p>
    <?php endif; ?>

    <?php if ($msg_erro): ?>
      <p class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?= htmlspecialchars($msg_erro) ?>
      </p>
    <?php endif; ?>

    <!-- Informações Pessoais -->
    <section class="bg-white p-6 rounded-xl shadow mb-6">
      <h2 class="text-xl font-semibold mb-4">Informações Pessoais</h2>

      <?php if (!$editar_dados): ?>
        <p><strong>Nome:</strong> <?= htmlspecialchars($cliente['nome']) ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($cliente['email']) ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($cliente['telefone']) ?></p>
        <p><strong>Celular:</strong> <?= htmlspecialchars($cliente['celular']) ?></p>
        <p><strong>Último acesso:</strong> <?= date('d/m/Y', strtotime($cliente['ultimo_acesso'])) ?></p>
        <a href="?editar_dados=1" class="inline-block mt-4 bg-pink-600 text-white py-2 px-4 rounded hover:bg-pink-700">Editar dados</a>
      <?php else: ?>
        <form method="POST" action="">
          <input type="hidden" name="editar_perfil" value="1" />

          <label class="block mb-2 font-medium" for="nome">Nome:</label>
          <input id="nome" name="nome" type="text" value="<?= htmlspecialchars($cliente['nome']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="email">E-mail:</label>
          <input id="email" name="email" type="email" value="<?= htmlspecialchars($cliente['email']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="telefone">Telefone:</label>
          <input id="telefone" name="telefone" type="text" value="<?= htmlspecialchars($cliente['telefone']) ?>" class="border p-2 w-full mb-4 rounded" />

          <label class="block mb-2 font-medium" for="celular">Celular:</label>
          <input id="celular" name="celular" type="text" value="<?= htmlspecialchars($cliente['celular']) ?>" class="border p-2 w-full mb-4 rounded" />

          <button type="submit" class="bg-pink-600 text-white py-2 px-4 rounded hover:bg-pink-700">Salvar</button>
          <a href="cliente.php" class="inline-block ml-4 py-2 px-4 border border-gray-300 rounded hover:bg-gray-200">Cancelar</a>
        </form>
      <?php endif; ?>
    </section>

    <!-- Endereços -->
    <section class="bg-white p-6 rounded-xl shadow mb-6">
      <h2 class="text-xl font-semibold mb-4">Endereços</h2>

      <?php if ($editar_endereco_id): 
        // Buscar dados do endereço específico para edição
        $enderecoEditar = null;
        foreach ($enderecos as $end) {
            if ($end['id'] == $editar_endereco_id) {
                $enderecoEditar = $end;
                break;
            }
        }
        if ($enderecoEditar):
      ?>
        <form method="POST" action="">
          <input type="hidden" name="editar_endereco" value="1" />
          <input type="hidden" name="endereco_id" value="<?= $editar_endereco_id ?>" />

          <label class="block mb-2 font-medium" for="rua">Rua:</label>
          <input id="rua" name="rua" type="text" value="<?= htmlspecialchars($enderecoEditar['rua']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="numero">Número:</label>
          <input id="numero" name="numero" type="text" value="<?= htmlspecialchars($enderecoEditar['numero']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="complemento">Complemento:</label>
          <input id="complemento" name="complemento" type="text" value="<?= htmlspecialchars($enderecoEditar['complemento']) ?>" class="border p-2 w-full mb-4 rounded" />

          <label class="block mb-2 font-medium" for="bairro">Bairro:</label>
          <input id="bairro" name="bairro" type="text" value="<?= htmlspecialchars($enderecoEditar['bairro']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="cidade">Cidade:</label>
          <input id="cidade" name="cidade" type="text" value="<?= htmlspecialchars($enderecoEditar['cidade']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="estado">Estado:</label>
          <input id="estado" name="estado" type="text" value="<?= htmlspecialchars($enderecoEditar['estado']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <label class="block mb-2 font-medium" for="cep">CEP:</label>
          <input id="cep" name="cep" type="text" value="<?= htmlspecialchars($enderecoEditar['cep']) ?>" class="border p-2 w-full mb-4 rounded" required />

          <button type="submit" class="bg-pink-600 text-white py-2 px-4 rounded hover:bg-pink-700">Salvar endereço</button>
          <a href="cliente.php" class="inline-block ml-4 py-2 px-4 border border-gray-300 rounded hover:bg-gray-200">Cancelar</a>
        </form>
      <?php else: ?>
        <p>Endereço não encontrado.</p>
        <a href="cliente.php" class="inline-block mt-4 bg-pink-600 text-white py-2 px-4 rounded hover:bg-pink-700">Voltar</a>
      <?php endif; else: ?>
        <table class="w-full border-collapse mb-4">
          <thead>
            <tr class="bg-pink-600 text-white">
              <th class="border px-4 py-2 text-left">Rua</th>
              <th class="border px-4 py-2 text-left">Número</th>
              <th class="border px-4 py-2 text-left">Complemento</th>
              <th class="border px-4 py-2 text-left">Bairro</th>
              <th class="border px-4 py-2 text-left">Cidade</th>
              <th class="border px-4 py-2 text-left">Estado</th>
              <th class="border px-4 py-2 text-left">CEP</th>
              <th class="border px-4 py-2">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($enderecos): ?>
              <?php foreach ($enderecos as $end): ?>
                <tr class="border-b">
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['rua']) ?></td>
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['numero']) ?></td>
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['complemento']) ?></td>
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['bairro']) ?></td>
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['cidade']) ?></td>
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['estado']) ?></td>
                  <td class="border px-4 py-2"><?= htmlspecialchars($end['cep']) ?></td>
                  <td class="border px-4 py-2 text-center">
                    <a href="?editar_endereco=<?= $end['id'] ?>" class="text-pink-600 hover:underline">Editar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="text-center py-4">Nenhum endereço cadastrado.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <details class="mb-4">
          <summary class="cursor-pointer font-semibold text-pink-600 mb-2">Adicionar novo endereço</summary>
          <form method="POST" action="" class="mt-2">
            <input type="hidden" name="adicionar_endereco" value="1" />

            <label class="block mb-2 font-medium" for="rua_novo">Rua:</label>
            <input id="rua_novo" name="rua_novo" type="text" class="border p-2 w-full mb-4 rounded" required />

            <label class="block mb-2 font-medium" for="numero_novo">Número:</label>
            <input id="numero_novo" name="numero_novo" type="text" class="border p-2 w-full mb-4 rounded" required />

            <label class="block mb-2 font-medium" for="complemento_novo">Complemento:</label>
            <input id="complemento_novo" name="complemento_novo" type="text" class="border p-2 w-full mb-4 rounded" />

            <label class="block mb-2 font-medium" for="bairro_novo">Bairro:</label>
            <input id="bairro_novo" name="bairro_novo" type="text" class="border p-2 w-full mb-4 rounded" required />

            <label class="block mb-2 font-medium" for="cidade_novo">Cidade:</label>
            <input id="cidade_novo" name="cidade_novo" type="text" class="border p-2 w-full mb-4 rounded" required />

            <label class="block mb-2 font-medium" for="estado_novo">Estado:</label>
            <input id="estado_novo" name="estado_novo" type="text" class="border p-2 w-full mb-4 rounded" required />

            <label class="block mb-2 font-medium" for="cep_novo">CEP:</label>
            <input id="cep_novo" name="cep_novo" type="text" class="border p-2 w-full mb-4 rounded" required />

            <button type="submit" class="bg-pink-600 text-white py-2 px-4 rounded hover:bg-pink-700">Adicionar endereço</button>
          </form>
        </details>
      <?php endif; ?>
    </section>

    <!-- Histórico de Pedidos -->
    <section class="bg-white p-6 rounded-xl shadow mb-6">
      <h2 class="text-xl font-semibold mb-4">Histórico de Pedidos</h2>

      <?php if ($pedidos): ?>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-pink-600 text-white">
              <th class="border px-4 py-2">Pedido</th>
              <th class="border px-4 py-2">Data</th>
              <th class="border px-4 py-2">Status</th>
              <th class="border px-4 py-2">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pedidos as $pedido): ?>
              <tr class="border-b">
                <td class="border px-4 py-2">#<?= $pedido['id'] ?></td>
                <td class="border px-4 py-2"><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
                <td class="border px-4 py-2 capitalize"><?= htmlspecialchars($pedido['status']) ?></td>
                <td class="border px-4 py-2 text-center">
                  <?php if ($pedido['status'] !== 'cancelado' && $pedido['status'] !== 'entregue'): ?>
                    <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?');" style="display:inline;">
                      <input type="hidden" name="cancelar_pedido_id" value="<?= $pedido['id'] ?>" />
                      <button type="submit" class="text-red-600 hover:underline bg-transparent border-0 cursor-pointer">Cancelar</button>
                    </form>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>Nenhum pedido realizado ainda.</p>
      <?php endif; ?>
    </section>

    <!-- Produtos Favoritos -->
    <section class="bg-white p-6 rounded-xl shadow mb-6">
      <h2 class="text-xl font-semibold mb-4">Produtos Favoritos</h2>

      <?php if ($favoritos): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <?php foreach ($favoritos as $fav): ?>
            <div class="border rounded p-2">
              <img src="<?= htmlspecialchars($fav['imagem']) ?>" alt="<?= htmlspecialchars($fav['nome']) ?>" class="w-full h-40 object-cover mb-2 rounded" />
              <p class="text-center font-medium"><?= htmlspecialchars($fav['nome']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>Você não tem produtos favoritos.</p>
      <?php endif; ?>
    </section>

    <a href="logout.php" class="inline-block bg-red-600 text-white py-2 px-6 rounded hover:bg-red-700">Sair</a>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

<?php
// Permite requisições do frontend e envia cookies (ajuste a origem conforme seu domínio)
header('Access-Control-Allow-Origin: http://localhost'); // coloque o domínio correto do frontend
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

session_start();

include 'conexao.php';  // Defina $dsn, $user, $pass, $options neste arquivo

// Permitir somente POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Verifica se cliente está logado pela sessão
$cliente_id = $_SESSION['cliente_id'] ?? null;
if (!$cliente_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não logado']);
    exit;
}

// Pega o JSON enviado pelo frontend
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido ou ausente']);
    exit;
}

// Extrai dados
$nome = $data['nome'] ?? '';
$email = $data['email'] ?? '';
$telefone = $data['telefone'] ?? '';
$cpf = $data['cpf'] ?? '';
$cep = $data['endereco']['cep'] ?? '';
$logradouro = $data['endereco']['logradouro'] ?? '';
$numero = $data['endereco']['numero'] ?? '';
$bairro = $data['endereco']['bairro'] ?? '';
$cidade = $data['endereco']['cidade'] ?? '';
$estado = $data['endereco']['estado'] ?? '';
$forma_pagamento = $data['formaPagamento'] ?? '';
$observacoes = $data['observacoes'] ?? '';
$itens = isset($data['itens']) ? json_encode($data['itens'], JSON_UNESCAPED_UNICODE) : '';
$total = $data['total'] ?? 0;

// Validação simples
if (!$nome || !$email || empty($itens) || !$total) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmt = $pdo->prepare("
        INSERT INTO pedidos (
            cliente_id, nome, email, telefone, cpf, cep, logradouro, numero,
            bairro, cidade, estado, forma_pagamento, observacoes, itens, total
        ) VALUES (
            :cliente_id, :nome, :email, :telefone, :cpf, :cep, :logradouro, :numero,
            :bairro, :cidade, :estado, :forma_pagamento, :observacoes, :itens, :total
        )
    ");

    $stmt->execute([
        ':cliente_id' => $cliente_id,
        ':nome' => $nome,
        ':email' => $email,
        ':telefone' => $telefone,
        ':cpf' => $cpf,
        ':cep' => $cep,
        ':logradouro' => $logradouro,
        ':numero' => $numero,
        ':bairro' => $bairro,
        ':cidade' => $cidade,
        ':estado' => $estado,
        ':forma_pagamento' => $forma_pagamento,
        ':observacoes' => $observacoes,
        ':itens' => $itens,
        ':total' => $total,
    ]);

    $pedido_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'pedido_id' => $pedido_id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar pedido: ' . $e->getMessage()]);
}

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

include __DIR__ . '/conexao.php';
session_start();

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Cliente não está logado'
    ]);
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// Ler o corpo JSON enviado pelo JS
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if ($data === null) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'JSON inválido ou não recebido'
    ]);
    exit;
}

// Extrair dados
$cliente = $data['cliente'] ?? [];
$resumo = $data['resumo'] ?? [];
$itens = $data['itens'] ?? [];
$status = $data['status'] ?? 'pendente';
$dataPedido = $data['dataPedido'] ?? date('Y-m-d H:i:s');

$nome = $cliente['nome'] ?? '';
$email = $cliente['email'] ?? '';
$telefone = $cliente['telefone'] ?? '';
$logradouro = $cliente['endereco']['logradouro'] ?? '';
$numero = $cliente['endereco']['numero'] ?? '';
$bairro = $cliente['endereco']['bairro'] ?? '';
$cidade = $cliente['endereco']['cidade'] ?? '';
$estado = $cliente['endereco']['estado'] ?? '';
$cep = $cliente['endereco']['cep'] ?? '';
$forma_pagamento = $cliente['formaPagamento'] ?? '';
$observacoes = $cliente['observacoes'] ?? '';
$total = (float) ($resumo['totalFinal'] ?? 0.00);
$itensJSON = json_encode($itens);

$sql = "INSERT INTO pedidos (
    cliente_id, nome, email, telefone, logradouro, numero, bairro, cidade, estado, cep,
    forma_pagamento, observacoes, total, itens, status, data_pedido
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $cliente_id, $nome, $email, $telefone, $logradouro, $numero, $bairro, $cidade, $estado, $cep,
        $forma_pagamento, $observacoes, $total, $itensJSON, $status, $dataPedido
    ]);

    $pedido_id = $pdo->lastInsertId();

    echo json_encode([
        'sucesso' => true,
        'pedido_id' => $pedido_id,
        'mensagem' => 'Compra finalizada com sucesso!'
    ]);

    
} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao salvar pedido: ' . $e->getMessage()
    ]);
}
exit;
?>

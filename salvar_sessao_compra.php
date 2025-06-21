<?php
session_start();

header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

// Valide os dados para segurança
$subtotal = floatval($dados['subtotal'] ?? 0);
$frete = floatval($dados['frete'] ?? 0);
$desconto = floatval($dados['desconto'] ?? 0);
$totalFinal = floatval($dados['totalFinal'] ?? 0);

$_SESSION['subtotal'] = $subtotal;
$_SESSION['frete'] = $frete;
$_SESSION['desconto'] = $desconto;
$_SESSION['totalFinal'] = $totalFinal;

echo json_encode(['sucesso' => true]);

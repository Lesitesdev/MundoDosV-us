<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['cliente_id'])) {
  echo json_encode(['erro' => 'Cliente não logado']);
  exit;
}

$id_cliente = $_SESSION['cliente_id'];

$stmt = $pdo->prepare("SELECT c.nome, c.email, c.telefone, e.rua, e.numero, e.bairro, e.cidade, e.estado, e.cep FROM clientes c LEFT JOIN enderecos e ON c.id = e.cliente_id WHERE c.id = ? ORDER BY e.id DESC LIMIT 1");
$stmt->execute([$id_cliente]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
  echo json_encode(['erro' => 'Dados não encontrados']);
  exit;
}

echo json_encode($dados);

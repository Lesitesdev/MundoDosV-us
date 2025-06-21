<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido']);
    exit;
}

if (empty($_POST['codigo'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Código do cupom não informado']);
    exit;
}

$codigo = trim(strtoupper($_POST['codigo'])); // transforma em maiúsculas para comparar melhor

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mundodosveus', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $sql = "SELECT * FROM cupons WHERE UPPER(nome) = :codigo AND quantidade_usada < quantidade_total LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['codigo' => $codigo]);
    $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cupom) {
        echo json_encode([
            'sucesso' => true,
            'desconto_percent' => floatval($cupom['desconto_percent']),
            'mensagem' => "Cupom válido! Desconto de {$cupom['desconto_percent']}% aplicado."
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Cupom inválido ou esgotado.']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
}

<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=mundodosveus', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $codigo = 'PROMO10';

    // prepare a query e salva em $stmt
    $sql = "SELECT * FROM cupons WHERE nome = :codigo AND quantidade_usada < quantidade_total LIMIT 1";
    $stmt = $pdo->prepare($sql);  // <--- aqui cria o statement

    // só depois execute
    $stmt->execute(['codigo' => $codigo]);

    $cupom = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cupom) {
        echo "Cupom válido! Desconto: {$cupom['desconto_percent']}%";
        // resto do código...
    } else {
        echo "Cupom inválido ou não disponível.";
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

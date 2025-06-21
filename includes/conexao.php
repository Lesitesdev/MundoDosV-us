<?php
$host = 'localhost';
$db = 'mundodosveus'; // substitua pelo nome exato do seu banco
$user = 'root';        // usuário padrão do XAMPP
$pass = '';            // senha vazia, a não ser que tenha alterado
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // 💡 mais seguro
];


try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Falha na conexão: ' . $e->getMessage());
}
?>

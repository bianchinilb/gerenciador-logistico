<?php
// padrao do xampp
$host  = 'localhost';
$banco = 'gerenciador_logistico';
$usuario = 'root';
$senha = '';

define('BASE_URL', '/gerenciador-logistico/');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao conectar no banco: ' . $e->getMessage() . '<br>Verifique se o MySQL do XAMPP esta ligado e se o banco.sql foi importado.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/funcoes.php';

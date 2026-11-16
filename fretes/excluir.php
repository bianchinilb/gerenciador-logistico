<?php
require_once '../config/conexao.php';

$id = $_GET['id'] ?? '';

if ($id) {
    $stmt = $pdo->prepare('DELETE FROM fretes WHERE id = ?');
    $stmt->execute([$id]);
    setMensagem('Frete excluído.');
}

redirecionar('listar.php');

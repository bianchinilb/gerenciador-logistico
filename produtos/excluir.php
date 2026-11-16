<?php
require_once '../config/conexao.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$id]);
        setMensagem('Produto excluído.');
    } catch (PDOException $e) {
        setMensagem('Não é possível excluir: este produto está vinculado a um frete.', 'danger');
    }
}

redirecionar('listar.php');

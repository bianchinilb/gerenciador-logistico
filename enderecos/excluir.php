<?php
require_once '../config/conexao.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM enderecos WHERE id = ?');
        $stmt->execute([$id]);
        setMensagem('Endereço excluído.');
    } catch (PDOException $e) {
        setMensagem('Não é possível excluir: este endereço é origem ou destino de algum frete.', 'danger');
    }
}

redirecionar('listar.php');

<?php
require_once '../config/conexao.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM transportadores WHERE id = ?');
        $stmt->execute([$id]);
        setMensagem('Transportador excluído.');
    } catch (PDOException $e) {
        setMensagem('Não é possível excluir: este transportador possui fretes cadastrados.', 'danger');
    }
}

redirecionar('listar.php');

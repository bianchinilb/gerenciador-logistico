<?php
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirecionar('listar.php');
}

$id = $_POST['id'] ?? '';
$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$peso = numeroBanco($_POST['peso_kg'] ?? 0);
$largura = numeroBanco($_POST['largura_cm'] ?? 0);
$altura = numeroBanco($_POST['altura_cm'] ?? 0);
$comprimento = numeroBanco($_POST['comprimento_cm'] ?? 0);
$valor = numeroBanco($_POST['valor'] ?? 0);

if ($nome == '') {
    setMensagem('Informe o nome do produto.', 'danger');
    redirecionar('form.php' . ($id ? '?id=' . $id : ''));
}

$dados = [$nome, $descricao, $peso, $largura, $altura, $comprimento, $valor];

if ($id) {
    $dados[] = $id;
    $stmt = $pdo->prepare('UPDATE produtos SET nome = ?, descricao = ?, peso_kg = ?, largura_cm = ?, altura_cm = ?, comprimento_cm = ?, valor = ? WHERE id = ?');
    $stmt->execute($dados);
    setMensagem('Produto atualizado com sucesso!');
} else {
    $stmt = $pdo->prepare('INSERT INTO produtos (nome, descricao, peso_kg, largura_cm, altura_cm, comprimento_cm, valor) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute($dados);
    setMensagem('Produto cadastrado com sucesso!');
}

redirecionar('listar.php');

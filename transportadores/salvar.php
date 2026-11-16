<?php
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirecionar('listar.php');
}

$id = $_POST['id'] ?? '';
$nome = trim($_POST['nome'] ?? '');
$cpfCnpj = trim($_POST['cpf_cnpj'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email = trim($_POST['email'] ?? '');
$veiculo = trim($_POST['veiculo'] ?? '');
$placa = strtoupper(str_replace('-', '', trim($_POST['placa'] ?? '')));
$capacidade = numeroBanco($_POST['capacidade_kg'] ?? 0);

if ($nome == '' || $cpfCnpj == '' || $veiculo == '' || $placa == '') {
    setMensagem('Preencha todos os campos obrigatórios.', 'danger');
    redirecionar('form.php' . ($id ? '?id=' . $id : ''));
}

if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setMensagem('E-mail inválido.', 'danger');
    redirecionar('form.php' . ($id ? '?id=' . $id : ''));
}

$dados = [$nome, $cpfCnpj, $telefone, $email, $veiculo, $placa, $capacidade];

if ($id) {
    $dados[] = $id;
    $stmt = $pdo->prepare('UPDATE transportadores SET nome = ?, cpf_cnpj = ?, telefone = ?, email = ?, veiculo = ?, placa = ?, capacidade_kg = ? WHERE id = ?');
    $stmt->execute($dados);
    setMensagem('Transportador atualizado com sucesso!');
} else {
    $stmt = $pdo->prepare('INSERT INTO transportadores (nome, cpf_cnpj, telefone, email, veiculo, placa, capacidade_kg) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute($dados);
    setMensagem('Transportador cadastrado com sucesso!');
}

redirecionar('listar.php');

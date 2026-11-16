<?php
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirecionar('listar.php');
}

$id = $_POST['id'] ?? '';
$campos = ['apelido', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];

$dados = [];
foreach ($campos as $campo) {
    $dados[$campo] = trim($_POST[$campo] ?? '');
}
$dados['uf'] = strtoupper($dados['uf']);

foreach ($campos as $campo) {
    if ($campo != 'complemento' && $dados[$campo] == '') {
        setMensagem('Preencha todos os campos obrigatórios.', 'danger');
        redirecionar('form.php' . ($id ? '?id=' . $id : ''));
    }
}

if ($id) {
    $dados['id'] = $id;
    $sql = 'UPDATE enderecos SET apelido = :apelido, cep = :cep, logradouro = :logradouro, numero = :numero,
            complemento = :complemento, bairro = :bairro, cidade = :cidade, uf = :uf WHERE id = :id';
    $msg = 'Endereço atualizado com sucesso!';
} else {
    $sql = 'INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf)
            VALUES (:apelido, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :uf)';
    $msg = 'Endereço cadastrado com sucesso!';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($dados);
setMensagem($msg);

redirecionar('listar.php');

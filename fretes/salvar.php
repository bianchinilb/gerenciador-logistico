<?php
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirecionar('listar.php');
}

$id = $_POST['id'] ?? '';
$transportadorId = $_POST['transportador_id'] ?? '';
$origemId = $_POST['origem_id'] ?? '';
$destinoId = $_POST['destino_id'] ?? '';
$dataFrete = $_POST['data_frete'] ?? '';
$valorFrete = numeroBanco($_POST['valor_frete'] ?? 0);
$status = $_POST['status'] ?? 'Agendado';
$observacao = trim($_POST['observacao'] ?? '');

$produtos = $_POST['produtos'] ?? [];
$quantidades = $_POST['quantidades'] ?? [];

$voltar = 'form.php' . ($id ? '?id=' . $id : '');

if (!$transportadorId || !$origemId || !$destinoId || !$dataFrete) {
    setMensagem('Preencha todos os campos obrigatórios do frete.', 'danger');
    redirecionar($voltar);
}

if ($origemId == $destinoId) {
    setMensagem('A origem e o destino não podem ser o mesmo endereço.', 'danger');
    redirecionar($voltar);
}

// produto repetido em duas linhas soma a quantidade
$itens = [];
foreach ($produtos as $i => $produtoId) {
    $qtd = (int) ($quantidades[$i] ?? 0);
    if ($produtoId == '' || $qtd < 1) {
        continue;
    }
    if (isset($itens[$produtoId])) {
        $itens[$produtoId] += $qtd;
    } else {
        $itens[$produtoId] = $qtd;
    }
}

if (count($itens) == 0) {
    setMensagem('Adicione pelo menos um produto ao frete.', 'danger');
    redirecionar($voltar);
}

try {
    $pdo->beginTransaction();

    if ($id) {
        $stmt = $pdo->prepare('UPDATE fretes SET transportador_id = ?, origem_id = ?, destino_id = ?, data_frete = ?, valor_frete = ?, status = ?, observacao = ? WHERE id = ?');
        $stmt->execute([$transportadorId, $origemId, $destinoId, $dataFrete, $valorFrete, $status, $observacao, $id]);

        $stmt = $pdo->prepare('DELETE FROM frete_produtos WHERE frete_id = ?');
        $stmt->execute([$id]);
        $freteId = $id;
    } else {
        $stmt = $pdo->prepare('INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, valor_frete, status, observacao) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$transportadorId, $origemId, $destinoId, $dataFrete, $valorFrete, $status, $observacao]);
        $freteId = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare('INSERT INTO frete_produtos (frete_id, produto_id, quantidade) VALUES (?, ?, ?)');
    foreach ($itens as $produtoId => $qtd) {
        $stmt->execute([$freteId, $produtoId, $qtd]);
    }

    $pdo->commit();
    setMensagem($id ? 'Frete atualizado com sucesso!' : 'Frete gerado com sucesso!');
    redirecionar('ver.php?id=' . $freteId);
} catch (PDOException $e) {
    $pdo->rollBack();
    setMensagem('Erro ao salvar o frete: ' . $e->getMessage(), 'danger');
    redirecionar($voltar);
}

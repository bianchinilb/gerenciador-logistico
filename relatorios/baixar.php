<?php
require_once '../config/conexao.php';

$data = $_GET['data'] ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    setMensagem('Data inválida.', 'danger');
    redirecionar('index.php');
}

$stmt = $pdo->prepare(
    'SELECT f.id, f.data_frete, f.status, f.valor_frete, f.observacao,
            t.nome AS transportador, t.cpf_cnpj, t.veiculo, t.placa,
            o.apelido AS origem, o.logradouro AS origem_rua, o.numero AS origem_num, o.cidade AS origem_cidade, o.uf AS origem_uf,
            d.apelido AS destino, d.logradouro AS destino_rua, d.numero AS destino_num, d.cidade AS destino_cidade, d.uf AS destino_uf,
            p.nome AS produto, fp.quantidade, p.peso_kg
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
       JOIN frete_produtos fp ON fp.frete_id = f.id
       JOIN produtos p ON p.id = fp.produto_id
      WHERE f.data_frete = ?
      ORDER BY f.id, p.nome'
);
$stmt->execute([$data]);
$linhas = $stmt->fetchAll();

$nomeArquivo = 'relatorio_fretes_' . $data . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');

$saida = fopen('php://output', 'w');

// BOM pro Excel nao quebrar acento
fwrite($saida, "\xEF\xBB\xBF");

fputcsv($saida, ['Relatório de fretes - ' . dataBr($data)], ';');
fputcsv($saida, [], ';');
fputcsv($saida, [
    'Frete', 'Data', 'Status', 'Transportador', 'CPF/CNPJ', 'Veículo', 'Placa',
    'Origem', 'Endereço origem', 'Destino', 'Endereço destino',
    'Produto', 'Quantidade', 'Peso total (kg)', 'Valor do frete (R$)', 'Observação'
], ';');

$fretesContados = [];
$totalValor = 0;
$totalPeso = 0;

foreach ($linhas as $l) {
    $peso = $l['peso_kg'] * $l['quantidade'];
    $totalPeso += $peso;

    // soma o valor so uma vez por frete
    if (!in_array($l['id'], $fretesContados)) {
        $fretesContados[] = $l['id'];
        $totalValor += $l['valor_frete'];
    }

    fputcsv($saida, [
        $l['id'],
        dataBr($l['data_frete']),
        $l['status'],
        $l['transportador'],
        $l['cpf_cnpj'],
        $l['veiculo'],
        $l['placa'],
        $l['origem'],
        $l['origem_rua'] . ', ' . $l['origem_num'] . ' - ' . $l['origem_cidade'] . '/' . $l['origem_uf'],
        $l['destino'],
        $l['destino_rua'] . ', ' . $l['destino_num'] . ' - ' . $l['destino_cidade'] . '/' . $l['destino_uf'],
        $l['produto'],
        $l['quantidade'],
        number_format($peso, 2, ',', ''),
        number_format($l['valor_frete'], 2, ',', ''),
        $l['observacao'],
    ], ';');
}

fputcsv($saida, [], ';');
fputcsv($saida, ['Total de fretes', count($fretesContados)], ';');
fputcsv($saida, ['Peso total (kg)', number_format($totalPeso, 2, ',', '')], ';');
fputcsv($saida, ['Faturamento (R$)', number_format($totalValor, 2, ',', '')], ';');

fclose($saida);
exit;

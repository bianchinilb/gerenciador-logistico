<?php
require_once '../config/conexao.php';

$titulo = 'Relatórios';
$menuAtivo = 'relatorios';

$data = $_GET['data'] ?? date('Y-m-d');

$stmt = $pdo->prepare(
    'SELECT f.id, f.valor_frete, f.status, t.nome AS transportador, t.placa,
            o.cidade AS origem_cidade, o.uf AS origem_uf,
            d.cidade AS destino_cidade, d.uf AS destino_uf,
            (SELECT SUM(fp.quantidade) FROM frete_produtos fp WHERE fp.frete_id = f.id) AS total_itens,
            (SELECT SUM(fp.quantidade * p.peso_kg) FROM frete_produtos fp JOIN produtos p ON p.id = fp.produto_id WHERE fp.frete_id = f.id) AS peso_total
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
      WHERE f.data_frete = ?
      ORDER BY f.id'
);
$stmt->execute([$data]);
$fretes = $stmt->fetchAll();

$somaValor = 0;
$somaPeso = 0;
foreach ($fretes as $f) {
    $somaValor += $f['valor_frete'];
    $somaPeso += $f['peso_total'];
}

include '../includes/header.php';
?>

<h2 class="titulo-pagina mb-3">Relatório de fretes por dia</h2>

<div class="card p-3 mb-3">
    <form method="get" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="data" class="form-label">Data dos fretes</label>
            <input type="date" id="data" name="data" class="form-control" value="<?= e($data) ?>" required>
        </div>
        <div class="col-md-8">
            <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i> Consultar</button>
            <?php if (count($fretes) > 0): ?>
                <a href="baixar.php?data=<?= e($data) ?>" class="btn btn-laranja"><i class="bi bi-download"></i> Baixar relatório (CSV)</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card p-3">
    <h5>Fretes de <?= dataBr($data) ?></h5>

    <?php if (count($fretes) == 0): ?>
        <p class="text-muted mb-0">Nenhum frete nessa data.</p>
    <?php else: ?>
        <p class="text-muted">
            <?= count($fretes) ?> frete(s) &middot; Peso total: <?= number_format($somaPeso, 2, ',', '.') ?> kg &middot; Faturamento: <?= dinheiro($somaValor) ?>
        </p>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transportador</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Itens</th>
                        <th>Peso</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fretes as $f): ?>
                        <tr>
                            <td><a href="../fretes/ver.php?id=<?= $f['id'] ?>"><?= $f['id'] ?></a></td>
                            <td><?= e($f['transportador']) ?> (<?= e($f['placa']) ?>)</td>
                            <td><?= e($f['origem_cidade']) ?>/<?= e($f['origem_uf']) ?></td>
                            <td><?= e($f['destino_cidade']) ?>/<?= e($f['destino_uf']) ?></td>
                            <td><?= (int) $f['total_itens'] ?></td>
                            <td><?= number_format($f['peso_total'], 2, ',', '.') ?> kg</td>
                            <td><?= dinheiro($f['valor_frete']) ?></td>
                            <td><?= e($f['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

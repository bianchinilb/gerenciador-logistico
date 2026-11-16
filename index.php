<?php
require_once 'config/conexao.php';

$titulo = 'Início';
$menuAtivo = 'inicio';

$totalProdutos = $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$totalEnderecos = $pdo->query('SELECT COUNT(*) FROM enderecos')->fetchColumn();
$totalTransportadores = $pdo->query('SELECT COUNT(*) FROM transportadores')->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM fretes WHERE data_frete = ?');
$stmt->execute([date('Y-m-d')]);
$fretesHoje = $stmt->fetchColumn();

$ultimosFretes = $pdo->query(
    'SELECT f.id, f.data_frete, f.status, f.valor_frete, t.nome AS transportador,
            o.cidade AS cidade_origem, d.cidade AS cidade_destino
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
       JOIN enderecos o ON o.id = f.origem_id
       JOIN enderecos d ON d.id = f.destino_id
      ORDER BY f.id DESC
      LIMIT 5'
)->fetchAll();

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="titulo-pagina mb-0">Painel</h2>
    <a href="fretes/form.php" class="btn btn-laranja"><i class="bi bi-plus-lg"></i> Novo frete</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-resumo p-3">
            <i class="bi bi-box-seam icone"></i>
            <h3><?= $totalProdutos ?></h3>
            <span class="text-muted">Produtos</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-resumo p-3">
            <i class="bi bi-geo-alt icone"></i>
            <h3><?= $totalEnderecos ?></h3>
            <span class="text-muted">Endereços</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-resumo p-3">
            <i class="bi bi-person-badge icone"></i>
            <h3><?= $totalTransportadores ?></h3>
            <span class="text-muted">Transportadores</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-resumo p-3">
            <i class="bi bi-calendar-check icone"></i>
            <h3><?= $fretesHoje ?></h3>
            <span class="text-muted">Fretes hoje</span>
        </div>
    </div>
</div>

<div class="card p-3">
    <h5 class="mb-3">Últimos fretes</h5>
    <?php if (count($ultimosFretes) == 0): ?>
        <p class="text-muted mb-0">Nenhum frete cadastrado ainda. <a href="fretes/form.php">Cadastre o primeiro</a>.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Transportador</th>
                        <th>Trajeto</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimosFretes as $f): ?>
                        <tr>
                            <td><a href="fretes/ver.php?id=<?= $f['id'] ?>"><?= $f['id'] ?></a></td>
                            <td><?= dataBr($f['data_frete']) ?></td>
                            <td><?= e($f['transportador']) ?></td>
                            <td><?= e($f['cidade_origem']) ?> <i class="bi bi-arrow-right"></i> <?= e($f['cidade_destino']) ?></td>
                            <td><?= dinheiro($f['valor_frete']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($f['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

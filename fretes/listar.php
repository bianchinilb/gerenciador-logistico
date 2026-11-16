<?php
require_once '../config/conexao.php';

$titulo = 'Fretes';
$menuAtivo = 'fretes';

$filtroData = $_GET['data'] ?? '';
$filtroStatus = $_GET['status'] ?? '';

$sql = 'SELECT f.*, t.nome AS transportador, t.placa,
               o.apelido AS origem_apelido, o.cidade AS origem_cidade, o.uf AS origem_uf,
               d.apelido AS destino_apelido, d.cidade AS destino_cidade, d.uf AS destino_uf,
               (SELECT SUM(quantidade) FROM frete_produtos fp WHERE fp.frete_id = f.id) AS total_itens
          FROM fretes f
          JOIN transportadores t ON t.id = f.transportador_id
          JOIN enderecos o ON o.id = f.origem_id
          JOIN enderecos d ON d.id = f.destino_id
         WHERE 1 = 1';
$params = [];

if ($filtroData != '') {
    $sql .= ' AND f.data_frete = ?';
    $params[] = $filtroData;
}
if ($filtroStatus != '') {
    $sql .= ' AND f.status = ?';
    $params[] = $filtroStatus;
}
$sql .= ' ORDER BY f.data_frete DESC, f.id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$fretes = $stmt->fetchAll();

$listaStatus = ['Agendado', 'Em trânsito', 'Entregue', 'Cancelado'];

function corStatus($status)
{
    switch ($status) {
        case 'Em trânsito':
            return 'bg-primary';
        case 'Entregue':
            return 'bg-success';
        case 'Cancelado':
            return 'bg-danger';
        default:
            return 'bg-warning text-dark';
    }
}

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="titulo-pagina mb-0">Fretes</h2>
    <a href="form.php" class="btn btn-laranja"><i class="bi bi-plus-lg"></i> Novo frete</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="date" name="data" class="form-control" value="<?= e($filtroData) ?>">
    </div>
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">Todos os status</option>
            <?php foreach ($listaStatus as $s): ?>
                <option value="<?= $s ?>" <?= $filtroStatus == $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filtrar</button>
        <a href="listar.php" class="btn btn-link">Limpar</a>
    </div>
</form>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Transportador</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Itens</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fretes as $f): ?>
                    <tr>
                        <td><?= $f['id'] ?></td>
                        <td><?= dataBr($f['data_frete']) ?></td>
                        <td><?= e($f['transportador']) ?><br><small class="text-muted"><?= e($f['placa']) ?></small></td>
                        <td><?= e($f['origem_apelido']) ?><br><small class="text-muted"><?= e($f['origem_cidade']) ?>/<?= e($f['origem_uf']) ?></small></td>
                        <td><?= e($f['destino_apelido']) ?><br><small class="text-muted"><?= e($f['destino_cidade']) ?>/<?= e($f['destino_uf']) ?></small></td>
                        <td><?= (int) $f['total_itens'] ?></td>
                        <td><?= dinheiro($f['valor_frete']) ?></td>
                        <td><span class="badge <?= corStatus($f['status']) ?>"><?= e($f['status']) ?></span></td>
                        <td class="text-end text-nowrap">
                            <a href="ver.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Detalhes"><i class="bi bi-eye"></i></a>
                            <a href="form.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="excluir.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger btn-excluir" title="Excluir"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($fretes) == 0): ?>
                    <tr><td colspan="9" class="text-center text-muted">Nenhum frete encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

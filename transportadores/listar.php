<?php
require_once '../config/conexao.php';

$titulo = 'Transportadores';
$menuAtivo = 'transportadores';

$busca = trim($_GET['busca'] ?? '');
if ($busca != '') {
    $stmt = $pdo->prepare('SELECT * FROM transportadores WHERE nome LIKE ? OR placa LIKE ? ORDER BY nome');
    $stmt->execute(['%' . $busca . '%', '%' . $busca . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM transportadores ORDER BY nome');
}
$transportadores = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="titulo-pagina mb-0">Transportadores</h2>
    <a href="form.php" class="btn btn-laranja"><i class="bi bi-plus-lg"></i> Novo transportador</a>
</div>

<form method="get" class="mb-3">
    <div class="input-group">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou placa..." value="<?= e($busca) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    </div>
</form>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>CPF/CNPJ</th>
                    <th>Contato</th>
                    <th>Veículo</th>
                    <th>Capacidade</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transportadores as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= e($t['nome']) ?></td>
                        <td><?= e($t['cpf_cnpj']) ?></td>
                        <td>
                            <?= e($t['telefone']) ?><br>
                            <small class="text-muted"><?= e($t['email']) ?></small>
                        </td>
                        <td><?= e($t['veiculo']) ?> <span class="badge bg-dark"><?= e($t['placa']) ?></span></td>
                        <td><?= number_format($t['capacidade_kg'], 0, ',', '.') ?> kg</td>
                        <td class="text-end">
                            <a href="form.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="excluir.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger btn-excluir" title="Excluir"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($transportadores) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted">Nenhum transportador encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

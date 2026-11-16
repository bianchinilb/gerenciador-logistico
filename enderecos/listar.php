<?php
require_once '../config/conexao.php';

$titulo = 'Endereços';
$menuAtivo = 'enderecos';

$busca = trim($_GET['busca'] ?? '');
if ($busca != '') {
    $stmt = $pdo->prepare('SELECT * FROM enderecos WHERE apelido LIKE ? OR cidade LIKE ? ORDER BY apelido');
    $stmt->execute(['%' . $busca . '%', '%' . $busca . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM enderecos ORDER BY apelido');
}
$enderecos = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="titulo-pagina mb-0">Endereços</h2>
    <a href="form.php" class="btn btn-laranja"><i class="bi bi-plus-lg"></i> Novo endereço</a>
</div>

<p class="text-muted">Os endereços cadastrados aqui podem ser usados como <strong>origem</strong> ou <strong>destino</strong> dos fretes.</p>

<form method="get" class="mb-3">
    <div class="input-group">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por apelido ou cidade..." value="<?= e($busca) ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    </div>
</form>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Apelido</th>
                    <th>Endereço</th>
                    <th>Cidade/UF</th>
                    <th>CEP</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enderecos as $end): ?>
                    <tr>
                        <td><?= $end['id'] ?></td>
                        <td><?= e($end['apelido']) ?></td>
                        <td>
                            <?= e($end['logradouro']) ?>, <?= e($end['numero']) ?>
                            <?= $end['complemento'] ? ' - ' . e($end['complemento']) : '' ?><br>
                            <small class="text-muted"><?= e($end['bairro']) ?></small>
                        </td>
                        <td><?= e($end['cidade']) ?>/<?= e($end['uf']) ?></td>
                        <td><?= e($end['cep']) ?></td>
                        <td class="text-end">
                            <a href="form.php?id=<?= $end['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="excluir.php?id=<?= $end['id'] ?>" class="btn btn-sm btn-outline-danger btn-excluir" title="Excluir"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($enderecos) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted">Nenhum endereço encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

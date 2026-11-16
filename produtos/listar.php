<?php
require_once '../config/conexao.php';

$titulo = 'Produtos';
$menuAtivo = 'produtos';

$busca = trim($_GET['busca'] ?? '');
if ($busca != '') {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE nome LIKE ? ORDER BY nome');
    $stmt->execute(['%' . $busca . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM produtos ORDER BY nome');
}
$produtos = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="titulo-pagina mb-0">Produtos</h2>
    <a href="form.php" class="btn btn-laranja"><i class="bi bi-plus-lg"></i> Novo produto</a>
</div>

<form method="get" class="mb-3">
    <div class="input-group">
        <input type="text" name="busca" class="form-control" placeholder="Buscar pelo nome..." value="<?= e($busca) ?>">
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
                    <th>Peso (kg)</th>
                    <th>Dimensões (L x A x C)</th>
                    <th>Valor</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td>
                            <?= e($p['nome']) ?><br>
                            <small class="text-muted"><?= e($p['descricao']) ?></small>
                        </td>
                        <td><?= number_format($p['peso_kg'], 2, ',', '.') ?></td>
                        <td><?= (float) $p['largura_cm'] ?> x <?= (float) $p['altura_cm'] ?> x <?= (float) $p['comprimento_cm'] ?> cm</td>
                        <td><?= dinheiro($p['valor']) ?></td>
                        <td class="text-end">
                            <a href="form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="excluir.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger btn-excluir" title="Excluir"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($produtos) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted">Nenhum produto encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

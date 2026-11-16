<?php
require_once '../config/conexao.php';

$menuAtivo = 'fretes';

$id = $_GET['id'] ?? '';

$stmt = $pdo->prepare(
    'SELECT f.*, t.nome AS transportador, t.cpf_cnpj, t.telefone, t.veiculo, t.placa, t.capacidade_kg
       FROM fretes f
       JOIN transportadores t ON t.id = f.transportador_id
      WHERE f.id = ?'
);
$stmt->execute([$id]);
$frete = $stmt->fetch();

if (!$frete) {
    setMensagem('Frete não encontrado.', 'danger');
    redirecionar('listar.php');
}

$stmt = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
$stmt->execute([$frete['origem_id']]);
$origem = $stmt->fetch();
$stmt->execute([$frete['destino_id']]);
$destino = $stmt->fetch();

$stmt = $pdo->prepare(
    'SELECT p.nome, p.peso_kg, p.valor, fp.quantidade
       FROM frete_produtos fp
       JOIN produtos p ON p.id = fp.produto_id
      WHERE fp.frete_id = ?
      ORDER BY p.nome'
);
$stmt->execute([$id]);
$itens = $stmt->fetchAll();

$pesoTotal = 0;
$valorMercadoria = 0;
foreach ($itens as $item) {
    $pesoTotal += $item['peso_kg'] * $item['quantidade'];
    $valorMercadoria += $item['valor'] * $item['quantidade'];
}

$titulo = 'Frete #' . $frete['id'];

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="titulo-pagina mb-0">Frete #<?= $frete['id'] ?> <small class="text-muted fs-6"><?= dataBr($frete['data_frete']) ?> - <?= e($frete['status']) ?></small></h2>
    <div>
        <a href="form.php?id=<?= $frete['id'] ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
        <a href="listar.php" class="btn btn-outline-secondary">Voltar</a>
    </div>
</div>

<?php if ($frete['capacidade_kg'] > 0 && $pesoTotal > $frete['capacidade_kg']): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Atenção: o peso total (<?= number_format($pesoTotal, 2, ',', '.') ?> kg) passa da capacidade do veículo (<?= number_format($frete['capacidade_kg'], 0, ',', '.') ?> kg).
    </div>
<?php endif; ?>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <h6 class="text-muted"><i class="bi bi-person-badge"></i> Transportador</h6>
            <strong><?= e($frete['transportador']) ?></strong>
            <span><?= e($frete['cpf_cnpj']) ?></span>
            <span><?= e($frete['telefone']) ?></span>
            <span><?= e($frete['veiculo']) ?> - <?= e($frete['placa']) ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <h6 class="text-muted"><i class="bi bi-geo-alt"></i> Origem</h6>
            <strong><?= e($origem['apelido']) ?></strong>
            <span><?= e($origem['logradouro']) ?>, <?= e($origem['numero']) ?> <?= e($origem['complemento']) ?></span>
            <span><?= e($origem['bairro']) ?> - <?= e($origem['cidade']) ?>/<?= e($origem['uf']) ?></span>
            <span>CEP <?= e($origem['cep']) ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <h6 class="text-muted"><i class="bi bi-flag"></i> Destino</h6>
            <strong><?= e($destino['apelido']) ?></strong>
            <span><?= e($destino['logradouro']) ?>, <?= e($destino['numero']) ?> <?= e($destino['complemento']) ?></span>
            <span><?= e($destino['bairro']) ?> - <?= e($destino['cidade']) ?>/<?= e($destino['uf']) ?></span>
            <span>CEP <?= e($destino['cep']) ?></span>
        </div>
    </div>
</div>

<div class="card p-3">
    <h5 class="mb-3">Produtos</h5>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Peso unit.</th>
                    <th>Peso total</th>
                    <th>Valor unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= e($item['nome']) ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td><?= number_format($item['peso_kg'], 2, ',', '.') ?> kg</td>
                        <td><?= number_format($item['peso_kg'] * $item['quantidade'], 2, ',', '.') ?> kg</td>
                        <td><?= dinheiro($item['valor']) ?></td>
                        <td><?= dinheiro($item['valor'] * $item['quantidade']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="fw-bold">
                <tr>
                    <td colspan="3">Totais</td>
                    <td><?= number_format($pesoTotal, 2, ',', '.') ?> kg</td>
                    <td></td>
                    <td><?= dinheiro($valorMercadoria) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <p class="mb-0"><strong>Valor do frete:</strong> <?= dinheiro($frete['valor_frete']) ?></p>
    <?php if ($frete['observacao']): ?>
        <p class="mb-0 mt-2"><strong>Observação:</strong> <?= e($frete['observacao']) ?></p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

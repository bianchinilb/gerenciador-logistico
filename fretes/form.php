<?php
require_once '../config/conexao.php';

$menuAtivo = 'fretes';

$frete = [
    'id' => '',
    'transportador_id' => '',
    'origem_id' => '',
    'destino_id' => '',
    'data_frete' => date('Y-m-d'),
    'valor_frete' => '',
    'status' => 'Agendado',
    'observacao' => '',
];
$itens = [];

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM fretes WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $frete = $stmt->fetch();

    if (!$frete) {
        setMensagem('Frete não encontrado.', 'danger');
        redirecionar('listar.php');
    }

    $stmt = $pdo->prepare('SELECT produto_id, quantidade FROM frete_produtos WHERE frete_id = ?');
    $stmt->execute([$frete['id']]);
    $itens = $stmt->fetchAll();
}

$editando = !empty($frete['id']);
$titulo = $editando ? 'Editar frete #' . $frete['id'] : 'Novo frete';

$transportadores = $pdo->query('SELECT id, nome, veiculo, placa FROM transportadores ORDER BY nome')->fetchAll();
$enderecos = $pdo->query('SELECT id, apelido, cidade, uf FROM enderecos ORDER BY apelido')->fetchAll();
$produtos = $pdo->query('SELECT id, nome, peso_kg FROM produtos ORDER BY nome')->fetchAll();

$listaStatus = ['Agendado', 'Em trânsito', 'Entregue', 'Cancelado'];

function opcoesProduto($produtos, $selecionado = '')
{
    $html = '<option value="">Selecione o produto...</option>';
    foreach ($produtos as $p) {
        $sel = $p['id'] == $selecionado ? 'selected' : '';
        $html .= '<option value="' . $p['id'] . '" ' . $sel . '>' . e($p['nome']) . ' (' . (float) $p['peso_kg'] . ' kg)</option>';
    }
    return $html;
}

function linhaProduto($produtos, $produtoId = '', $quantidade = 1)
{
    return '
    <div class="linha-produto row g-2 align-items-end">
        <div class="col-md-8">
            <label class="form-label small mb-1">Produto</label>
            <select name="produtos[]" class="form-select" required>' . opcoesProduto($produtos, $produtoId) . '</select>
        </div>
        <div class="col-8 col-md-3">
            <label class="form-label small mb-1">Quantidade</label>
            <input type="number" name="quantidades[]" class="form-control" min="1" required value="' . (int) $quantidade . '">
        </div>
        <div class="col-4 col-md-1 text-end">
            <button type="button" class="btn btn-outline-danger btn-remover" title="Remover"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>';
}

$faltaCadastro = count($transportadores) == 0 || count($enderecos) < 2 || count($produtos) == 0;

include '../includes/header.php';
?>

<h2 class="titulo-pagina mb-3"><?= e($titulo) ?></h2>

<?php if ($faltaCadastro): ?>
    <div class="alert alert-warning">
        Para gerar um frete você precisa ter pelo menos <strong>1 transportador</strong>, <strong>2 endereços</strong> (origem e destino) e <strong>1 produto</strong> cadastrados.
        <div class="mt-2">
            <a href="../transportadores/form.php" class="btn btn-sm btn-outline-dark">Cadastrar transportador</a>
            <a href="../enderecos/form.php" class="btn btn-sm btn-outline-dark">Cadastrar endereço</a>
            <a href="../produtos/form.php" class="btn btn-sm btn-outline-dark">Cadastrar produto</a>
        </div>
    </div>
<?php else: ?>

<form action="salvar.php" method="post" id="formFrete">
    <input type="hidden" name="id" value="<?= e($frete['id']) ?>">

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card p-4 h-100">
                <h5 class="mb-3"><i class="bi bi-info-circle"></i> Dados do frete</h5>

                <div class="mb-3">
                    <label for="transportador_id" class="form-label obrigatorio">Transportador</label>
                    <select id="transportador_id" name="transportador_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($transportadores as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $frete['transportador_id'] == $t['id'] ? 'selected' : '' ?>>
                                <?= e($t['nome']) ?> - <?= e($t['veiculo']) ?> (<?= e($t['placa']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="origem_id" class="form-label obrigatorio">Origem</label>
                    <select id="origem_id" name="origem_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($enderecos as $end): ?>
                            <option value="<?= $end['id'] ?>" <?= $frete['origem_id'] == $end['id'] ? 'selected' : '' ?>><?= e(enderecoResumido($end)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="destino_id" class="form-label obrigatorio">Destino</label>
                    <select id="destino_id" name="destino_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($enderecos as $end): ?>
                            <option value="<?= $end['id'] ?>" <?= $frete['destino_id'] == $end['id'] ? 'selected' : '' ?>><?= e(enderecoResumido($end)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label for="data_frete" class="form-label obrigatorio">Data</label>
                        <input type="date" id="data_frete" name="data_frete" class="form-control" required value="<?= e($frete['data_frete']) ?>">
                    </div>
                    <div class="col-6">
                        <label for="valor_frete" class="form-label obrigatorio">Valor do frete (R$)</label>
                        <input type="number" step="0.01" min="0" id="valor_frete" name="valor_frete" class="form-control" required value="<?= e($frete['valor_frete']) ?>">
                    </div>
                    <div class="col-12">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <?php foreach ($listaStatus as $s): ?>
                                <option value="<?= $s ?>" <?= $frete['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="observacao" class="form-label">Observação</label>
                        <textarea id="observacao" name="observacao" class="form-control" rows="2" maxlength="255"><?= e($frete['observacao']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Produtos do frete <span class="badge bg-secondary" id="totalItens">0</span></h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarProduto">
                        <i class="bi bi-plus-lg"></i> Adicionar produto
                    </button>
                </div>

                <div id="listaProdutos">
                    <?php foreach ($itens as $item): ?>
                        <?= linhaProduto($produtos, $item['produto_id'], $item['quantidade']) ?>
                    <?php endforeach; ?>
                </div>

                <small class="text-muted">Um mesmo transportador pode levar vários produtos no mesmo frete.</small>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-laranja"><i class="bi bi-check-lg"></i> Salvar frete</button>
        <a href="listar.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>

<!-- template usado pelo javascript pra criar novas linhas de produto -->
<template id="modeloLinhaProduto">
    <?= linhaProduto($produtos) ?>
</template>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>

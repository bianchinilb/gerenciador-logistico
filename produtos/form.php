<?php
require_once '../config/conexao.php';

$menuAtivo = 'produtos';

$produto = [
    'id' => '',
    'nome' => '',
    'descricao' => '',
    'peso_kg' => '',
    'largura_cm' => '',
    'altura_cm' => '',
    'comprimento_cm' => '',
    'valor' => '',
];

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $produto = $stmt->fetch();

    if (!$produto) {
        setMensagem('Produto não encontrado.', 'danger');
        redirecionar('listar.php');
    }
}

$editando = !empty($produto['id']);
$titulo = $editando ? 'Editar produto' : 'Novo produto';

include '../includes/header.php';
?>

<h2 class="titulo-pagina mb-3"><?= $titulo ?></h2>

<div class="card p-4">
    <form action="salvar.php" method="post">
        <input type="hidden" name="id" value="<?= e($produto['id']) ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nome" class="form-label obrigatorio">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" maxlength="100" required value="<?= e($produto['nome']) ?>">
            </div>
            <div class="col-md-6">
                <label for="descricao" class="form-label">Descrição</label>
                <input type="text" id="descricao" name="descricao" class="form-control" maxlength="255" value="<?= e($produto['descricao']) ?>">
            </div>

            <div class="col-md-3">
                <label for="peso_kg" class="form-label obrigatorio">Peso (kg)</label>
                <input type="number" step="0.01" min="0" id="peso_kg" name="peso_kg" class="form-control" required value="<?= e($produto['peso_kg']) ?>">
            </div>
            <div class="col-md-3">
                <label for="largura_cm" class="form-label">Largura (cm)</label>
                <input type="number" step="0.01" min="0" id="largura_cm" name="largura_cm" class="form-control" value="<?= e($produto['largura_cm']) ?>">
            </div>
            <div class="col-md-3">
                <label for="altura_cm" class="form-label">Altura (cm)</label>
                <input type="number" step="0.01" min="0" id="altura_cm" name="altura_cm" class="form-control" value="<?= e($produto['altura_cm']) ?>">
            </div>
            <div class="col-md-3">
                <label for="comprimento_cm" class="form-label">Comprimento (cm)</label>
                <input type="number" step="0.01" min="0" id="comprimento_cm" name="comprimento_cm" class="form-control" value="<?= e($produto['comprimento_cm']) ?>">
            </div>

            <div class="col-md-3">
                <label for="valor" class="form-label obrigatorio">Valor unitário (R$)</label>
                <input type="number" step="0.01" min="0" id="valor" name="valor" class="form-control" required value="<?= e($produto['valor']) ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-laranja"><i class="bi bi-check-lg"></i> Salvar</button>
            <a href="listar.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

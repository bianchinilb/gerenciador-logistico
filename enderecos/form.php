<?php
require_once '../config/conexao.php';

$menuAtivo = 'enderecos';

$endereco = [
    'id' => '',
    'apelido' => '',
    'cep' => '',
    'logradouro' => '',
    'numero' => '',
    'complemento' => '',
    'bairro' => '',
    'cidade' => '',
    'uf' => '',
];

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $endereco = $stmt->fetch();

    if (!$endereco) {
        setMensagem('Endereço não encontrado.', 'danger');
        redirecionar('listar.php');
    }
}

$editando = !empty($endereco['id']);
$titulo = $editando ? 'Editar endereço' : 'Novo endereço';

$estados = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

include '../includes/header.php';
?>

<h2 class="titulo-pagina mb-3"><?= $titulo ?></h2>

<div class="card p-4">
    <form action="salvar.php" method="post">
        <input type="hidden" name="id" value="<?= e($endereco['id']) ?>">

        <div class="row g-3">
            <div class="col-md-5">
                <label for="apelido" class="form-label obrigatorio">Apelido</label>
                <input type="text" id="apelido" name="apelido" class="form-control" maxlength="60" required placeholder="Ex: Depósito Central, Cliente João..." value="<?= e($endereco['apelido']) ?>">
            </div>
            <div class="col-md-3">
                <label for="cep" class="form-label obrigatorio">CEP</label>
                <input type="text" id="cep" name="cep" class="form-control" maxlength="9" required placeholder="00000-000" value="<?= e($endereco['cep']) ?>">
                <small id="avisoCep" class="text-muted"></small>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <small class="text-muted">Digite o CEP que o resto é preenchido automaticamente (ViaCEP).</small>
            </div>

            <div class="col-md-8">
                <label for="logradouro" class="form-label obrigatorio">Logradouro</label>
                <input type="text" id="logradouro" name="logradouro" class="form-control" maxlength="120" required value="<?= e($endereco['logradouro']) ?>">
            </div>
            <div class="col-md-4">
                <label for="numero" class="form-label obrigatorio">Número</label>
                <input type="text" id="numero" name="numero" class="form-control" maxlength="10" required value="<?= e($endereco['numero']) ?>">
            </div>

            <div class="col-md-4">
                <label for="complemento" class="form-label">Complemento</label>
                <input type="text" id="complemento" name="complemento" class="form-control" maxlength="60" value="<?= e($endereco['complemento']) ?>">
            </div>
            <div class="col-md-4">
                <label for="bairro" class="form-label obrigatorio">Bairro</label>
                <input type="text" id="bairro" name="bairro" class="form-control" maxlength="80" required value="<?= e($endereco['bairro']) ?>">
            </div>
            <div class="col-md-3">
                <label for="cidade" class="form-label obrigatorio">Cidade</label>
                <input type="text" id="cidade" name="cidade" class="form-control" maxlength="80" required value="<?= e($endereco['cidade']) ?>">
            </div>
            <div class="col-md-1">
                <label for="uf" class="form-label obrigatorio">UF</label>
                <select id="uf" name="uf" class="form-select" required>
                    <option value=""></option>
                    <?php foreach ($estados as $uf): ?>
                        <option value="<?= $uf ?>" <?= $endereco['uf'] == $uf ? 'selected' : '' ?>><?= $uf ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-laranja"><i class="bi bi-check-lg"></i> Salvar</button>
            <a href="listar.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

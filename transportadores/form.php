<?php
require_once '../config/conexao.php';

$menuAtivo = 'transportadores';

$transportador = [
    'id' => '',
    'nome' => '',
    'cpf_cnpj' => '',
    'telefone' => '',
    'email' => '',
    'veiculo' => '',
    'placa' => '',
    'capacidade_kg' => '',
];

if (!empty($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM transportadores WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $transportador = $stmt->fetch();

    if (!$transportador) {
        setMensagem('Transportador não encontrado.', 'danger');
        redirecionar('listar.php');
    }
}

$editando = !empty($transportador['id']);
$titulo = $editando ? 'Editar transportador' : 'Novo transportador';

$tiposVeiculo = ['Utilitário', 'Van', 'Caminhão 3/4', 'Caminhão Toco', 'Caminhão Truck', 'Carreta'];

include '../includes/header.php';
?>

<h2 class="titulo-pagina mb-3"><?= $titulo ?></h2>

<div class="card p-4">
    <form action="salvar.php" method="post">
        <input type="hidden" name="id" value="<?= e($transportador['id']) ?>">

        <h6 class="text-muted">Dados do transportador</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="nome" class="form-label obrigatorio">Nome / Razão social</label>
                <input type="text" id="nome" name="nome" class="form-control" maxlength="100" required value="<?= e($transportador['nome']) ?>">
            </div>
            <div class="col-md-6">
                <label for="cpf_cnpj" class="form-label obrigatorio">CPF ou CNPJ</label>
                <input type="text" id="cpf_cnpj" name="cpf_cnpj" class="form-control" maxlength="18" required value="<?= e($transportador['cpf_cnpj']) ?>">
            </div>
            <div class="col-md-6">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" id="telefone" name="telefone" class="form-control" maxlength="15" placeholder="(44) 99999-9999" value="<?= e($transportador['telefone']) ?>">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" maxlength="100" value="<?= e($transportador['email']) ?>">
            </div>
        </div>

        <h6 class="text-muted">Veículo</h6>
        <div class="row g-3">
            <div class="col-md-5">
                <label for="veiculo" class="form-label obrigatorio">Tipo de veículo</label>
                <select id="veiculo" name="veiculo" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($tiposVeiculo as $tipo): ?>
                        <option value="<?= $tipo ?>" <?= $transportador['veiculo'] == $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="placa" class="form-label obrigatorio">Placa</label>
                <input type="text" id="placa" name="placa" class="form-control text-uppercase" maxlength="8" required placeholder="ABC1D23" value="<?= e($transportador['placa']) ?>">
            </div>
            <div class="col-md-4">
                <label for="capacidade_kg" class="form-label obrigatorio">Capacidade de carga (kg)</label>
                <input type="number" step="0.01" min="0" id="capacidade_kg" name="capacidade_kg" class="form-control" required value="<?= e($transportador['capacidade_kg']) ?>">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-laranja"><i class="bi bi-check-lg"></i> Salvar</button>
            <a href="listar.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

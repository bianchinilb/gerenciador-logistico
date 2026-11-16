<?php
$titulo = $titulo ?? 'Gerenciador Logístico';
$menuAtivo = $menuAtivo ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | LogiFrete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-escuro">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>index.php">
            <i class="bi bi-truck"></i> LogiFrete
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $menuAtivo == 'inicio' ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $menuAtivo == 'produtos' ? 'active' : '' ?>" href="<?= BASE_URL ?>produtos/listar.php">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $menuAtivo == 'enderecos' ? 'active' : '' ?>" href="<?= BASE_URL ?>enderecos/listar.php">Endereços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $menuAtivo == 'transportadores' ? 'active' : '' ?>" href="<?= BASE_URL ?>transportadores/listar.php">Transportadores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $menuAtivo == 'fretes' ? 'active' : '' ?>" href="<?= BASE_URL ?>fretes/listar.php">Fretes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $menuAtivo == 'relatorios' ? 'active' : '' ?>" href="<?= BASE_URL ?>relatorios/index.php">Relatórios</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <?php mostrarMensagem(); ?>

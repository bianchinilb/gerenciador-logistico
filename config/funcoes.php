<?php
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function dinheiro($valor)
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function dataBr($data)
{
    if (empty($data)) {
        return '';
    }
    return date('d/m/Y', strtotime($data));
}

// o usuario digita 1.234,56 ou 1234.56, aqui deixo no padrao do banco
function numeroBanco($valor)
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        return 0;
    }
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
    }
    return (float) $valor;
}

function setMensagem($texto, $tipo = 'success')
{
    $_SESSION['mensagem'] = ['texto' => $texto, 'tipo' => $tipo];
}

function mostrarMensagem()
{
    if (!empty($_SESSION['mensagem'])) {
        $msg = $_SESSION['mensagem'];
        unset($_SESSION['mensagem']);
        echo '<div class="alert alert-' . e($msg['tipo']) . ' alert-dismissible fade show" role="alert">'
            . e($msg['texto'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

function redirecionar($pagina)
{
    header('Location: ' . $pagina);
    exit;
}

function enderecoResumido($end)
{
    return $end['apelido'] . ' - ' . $end['cidade'] . '/' . $end['uf'];
}

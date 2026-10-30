<?php
chdir(__DIR__);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

require_once './includes/db_connect.php';
require_once './controllers/produto.controller.php';
require_once './controllers/cliente.controller.php';
require_once './controllers/venda.controller.php';

function responderErro($codigo, $mensagem) {
    http_response_code($codigo);
    echo json_encode(['sucesso' => false, 'mensagem' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}

$controller = "";

$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : null;

$acao = isset($_GET['acao']) ? $_GET['acao'] : null;

$dadosEnviados = file_get_contents('php://input');

$dadosFormatados = json_decode($dadosEnviados, true);

if (trim($dadosEnviados) !== '' && !is_array($dadosFormatados)) {
    responderErro(400, 'JSON inválido');
}

if ($dadosFormatados === null) {
    $dadosFormatados = [];
}

if ($modulo == "produto") {
    $controller = new ProdutoController($conn);
} else if ($modulo == "cliente") {
    $controller = new ClienteController($conn);
} else if ($modulo == "venda") {
    $controller = new VendaController($conn);
} else {
    responderErro(400, 'Módulo não encontrado');
}

try {
    if ($acao == "listar") {
        echo json_encode($controller->listarTodos($dadosFormatados), JSON_UNESCAPED_UNICODE);
    } else if ($acao == "editar") {
        echo json_encode($controller->atualizar($dadosFormatados), JSON_UNESCAPED_UNICODE);
    } else if ($acao == "salvar") {
        echo json_encode($controller->cadastrar($dadosFormatados), JSON_UNESCAPED_UNICODE);
    } else if ($acao == "deletar") {
        echo json_encode($controller->deletar($dadosFormatados), JSON_UNESCAPED_UNICODE);
    } else {
        responderErro(400, 'Ação não encontrada');
    }
} catch (mysqli_sql_exception $e) {
    if ((int) $e->getCode() === 1451) {
        responderErro(400, 'Este registro está vinculado a uma venda e não pode ser removido');
    }
    responderErro(500, 'Erro ao acessar o banco de dados');
} catch (Exception $e) {
    responderErro(400, $e->getMessage());
} catch (Throwable $e) {
    responderErro(500, 'Erro interno no servidor');
}

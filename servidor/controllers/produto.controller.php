<?php
require_once './controllers/generic.controller.php';
require_once './models/produto.php';
require_once './services/produto.service.php';

class ProdutoController extends GenericController {

    private $service;

    public function __construct($conn) {
        parent::__construct($conn);
        $this->service = new ProdutoService($conn);
    }

    public function listarTodos($parametros) {
        return $this->service->listarTodos($parametros);
    }

    public function cadastrar($parametros) {
        $nomeProduto = $parametros['nome'] ?? '';
        $precoProduto = $parametros['preco'] ?? null;
        $fabricanteProduto = $parametros['fabricante'] ?? null;
        $produto = new Produto(null, $nomeProduto, $precoProduto, $fabricanteProduto);

        $this->service->cadastrar($produto);
        return ['sucesso' => true];
    }

    public function atualizar($parametros) {
        $id = $parametros['id'] ?? null;
        $nomeProduto = $parametros['nome'] ?? '';
        $precoProduto = $parametros['preco'] ?? null;
        $fabricanteProduto = $parametros['fabricante'] ?? null;
        $produto = new Produto($id, $nomeProduto, $precoProduto, $fabricanteProduto);

        $this->service->atualizar($produto);
        return ['sucesso' => true];
    }

    public function deletar($parametros) {
        $this->service->deletar($parametros['id'] ?? null);
        return ['sucesso' => true];
    }
}

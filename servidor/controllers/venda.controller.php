<?php
require_once './controllers/generic.controller.php';
require_once './models/venda.php';
require_once './models/cliente.php';
require_once './models/produto.php';
require_once './services/venda.service.php';

class VendaController extends GenericController {

    private $service;

    public function __construct($conn) {
        parent::__construct($conn);
        $this->service = new VendaService($conn);
    }

    private function montarVenda($parametros, $id) {
        $cliente = new Cliente($parametros['cliente_id'] ?? null, null, null);
        $dataVenda = $parametros['data_venda'] ?? null;

        $produtos = [];
        $idsProdutos = $parametros['produtos'] ?? [];
        if (is_array($idsProdutos)) {
            foreach ($idsProdutos as $idProduto) {
                array_push($produtos, new Produto($idProduto, null, null, null));
            }
        }

        return new Venda($id, $cliente, $dataVenda, 0, $produtos);
    }

    public function listarTodos($parametros) {
        return $this->service->listarTodos($parametros);
    }

    public function cadastrar($parametros) {
        $venda = $this->montarVenda($parametros, null);

        $this->service->cadastrar($venda);
        return ['sucesso' => true];
    }

    public function atualizar($parametros) {
        $venda = $this->montarVenda($parametros, $parametros['id'] ?? null);

        $this->service->atualizar($venda);
        return ['sucesso' => true];
    }

    public function deletar($parametros) {
        $this->service->deletar($parametros['id'] ?? null);
        return ['sucesso' => true];
    }
}

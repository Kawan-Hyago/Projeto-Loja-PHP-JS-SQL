<?php
require_once './controllers/generic.controller.php';
require_once './models/cliente.php';
require_once './services/cliente.service.php';

class ClienteController extends GenericController {

    private $service;

    public function __construct($conn) {
        parent::__construct($conn);
        $this->service = new ClienteService($conn);
    }

    public function listarTodos($parametros) {
        return $this->service->listarTodos($parametros);
    }

    public function cadastrar($parametros) {
        $nomeCliente = $parametros['nome'] ?? '';
        $enderecoCliente = $parametros['endereco'] ?? null;
        $cliente = new Cliente(null, $nomeCliente, $enderecoCliente);

        $this->service->cadastrar($cliente);
        return ['sucesso' => true];
    }

    public function atualizar($parametros) {
        $id = $parametros['id'] ?? null;
        $nomeCliente = $parametros['nome'] ?? '';
        $enderecoCliente = $parametros['endereco'] ?? null;
        $cliente = new Cliente($id, $nomeCliente, $enderecoCliente);

        $this->service->atualizar($cliente);
        return ['sucesso' => true];
    }

    public function deletar($parametros) {
        $this->service->deletar($parametros['id'] ?? null);
        return ['sucesso' => true];
    }
}

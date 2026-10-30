<?php
require_once './services/generic.service.php';
require_once './dao/cliente.dao.php';

class ClienteService extends GenericService {

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function getDAOPrincipal($conn) {
        return new ClienteDAO($conn);
    }

    private function validar($cliente) {
        if (trim((string) $cliente->getNome()) === '') {
            throw new Exception('O nome do cliente é obrigatório');
        }
    }

    public function listarTodos($filtros) {
        return $this->dao->listarTodos($filtros);
    }

    public function cadastrar($cliente) {
        $this->validar($cliente);
        return $this->dao->cadastrar($cliente);
    }

    public function atualizar($cliente) {
        $this->validar($cliente);
        return $this->dao->atualizar($cliente);
    }

    public function deletar($id) {
        return $this->dao->deletar($id);
    }
}

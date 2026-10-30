<?php
require_once './services/generic.service.php';
require_once './dao/produto.dao.php';

class ProdutoService extends GenericService {

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function getDAOPrincipal($conn) {
        return new ProdutoDAO($conn);
    }

    private function validar($produto) {
        if (trim((string) $produto->getNome()) === '') {
            throw new Exception('O nome do produto é obrigatório');
        }
        if (!is_numeric($produto->getPreco()) || $produto->getPreco() <= 0) {
            throw new Exception('O preço do produto deve ser maior que zero');
        }
    }

    public function listarTodos($filtros) {
        return $this->dao->listarTodos($filtros);
    }

    public function cadastrar($produto) {
        $this->validar($produto);
        return $this->dao->cadastrar($produto);
    }

    public function atualizar($produto) {
        $this->validar($produto);
        return $this->dao->atualizar($produto);
    }

    public function deletar($id) {
        return $this->dao->deletar($id);
    }
}

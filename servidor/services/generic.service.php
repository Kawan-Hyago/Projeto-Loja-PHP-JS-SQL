<?php
abstract class GenericService {

    protected $dao;

    public function __construct($conn) {
        $this->dao = $this->getDAOPrincipal($conn);
    }

    public abstract function getDAOPrincipal($conn);

    public function listarTodos($filtros) {
        return $this->dao->listarTodos($filtros);
    }

    public abstract function cadastrar($obj);
    public abstract function atualizar($obj);
    public abstract function deletar($id);
}

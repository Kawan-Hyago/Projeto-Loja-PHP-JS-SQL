<?php
abstract class GenericController {

    protected $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public abstract function listarTodos($parametros);

    public abstract function cadastrar($parametros);

    public abstract function atualizar($parametros);

    public abstract function deletar($parametros);
}

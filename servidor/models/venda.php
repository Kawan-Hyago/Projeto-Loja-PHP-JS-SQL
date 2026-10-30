<?php
require_once './models/generic.entity.php';

class Venda extends GenericEntity implements JsonSerializable {
    private $cliente;
    private $dataVenda;
    private $totalVenda;
    private $produtos;

    public function __construct($id, $cliente, $dataVenda, $totalVenda, $produtos = []) {
        parent::__construct($id);
        $this->cliente = $cliente;
        $this->dataVenda = $dataVenda;
        $this->totalVenda = $totalVenda;
        $this->produtos = $produtos;
    }

    public function getCliente() {
        return $this->cliente;
    }

    public function getDataVenda() {
        return $this->dataVenda;
    }

    public function getTotalVenda() {
        return $this->totalVenda;
    }

    public function setTotalVenda($totalVenda) {
        $this->totalVenda = $totalVenda;
    }

    public function getProdutos() {
        return $this->produtos;
    }

    public function setProdutos($produtos) {
        $this->produtos = $produtos;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente->getId(),
            'cliente_nome' => $this->cliente->getNome(),
            'data_venda' => $this->dataVenda,
            'total_venda' => $this->totalVenda,
            'produtos' => $this->produtos
        ];
    }
}

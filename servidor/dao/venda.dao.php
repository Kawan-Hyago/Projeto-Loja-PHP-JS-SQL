<?php
require_once './models/venda.php';
require_once './models/cliente.php';

class VendaDAO {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    public function listarTodos($filtros = null) {
        $SQL = "SELECT v.id, v.cliente_id, v.data_venda, v.total_venda, c.nome AS cliente_nome, c.endereco AS cliente_endereco
                FROM vendas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                ORDER BY v.id";
        $result = mysqli_query($this->conexao, $SQL);
        $vendas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente((int) $row['cliente_id'], $row['cliente_nome'], $row['cliente_endereco']);
            $venda = new Venda((int) $row['id'], $cliente, $row['data_venda'], (float) $row['total_venda']);
            array_push($vendas, $venda);
        }
        return $vendas;
    }

    public function buscarPorId($id) {
        $SQL = "SELECT v.id, v.cliente_id, v.data_venda, v.total_venda, c.nome AS cliente_nome, c.endereco AS cliente_endereco
                FROM vendas v
                INNER JOIN clientes c ON c.id = v.cliente_id
                WHERE v.id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if ($row === null) {
            return null;
        }
        $cliente = new Cliente((int) $row['cliente_id'], $row['cliente_nome'], $row['cliente_endereco']);
        return new Venda((int) $row['id'], $cliente, $row['data_venda'], (float) $row['total_venda']);
    }

    public function cadastrar($venda) {
        $SQL = "INSERT INTO vendas (cliente_id, data_venda, total_venda) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        $clienteId = $venda->getCliente()->getId();
        $dataVenda = $venda->getDataVenda();
        $totalVenda = $venda->getTotalVenda();
        mysqli_stmt_bind_param($stmt, 'isd', $clienteId, $dataVenda, $totalVenda);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return mysqli_insert_id($this->conexao);
    }

    public function atualizar($venda) {
        $SQL = "UPDATE vendas SET cliente_id = ?, data_venda = ?, total_venda = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        $clienteId = $venda->getCliente()->getId();
        $dataVenda = $venda->getDataVenda();
        $totalVenda = $venda->getTotalVenda();
        $id = $venda->getId();
        mysqli_stmt_bind_param($stmt, 'isdi', $clienteId, $dataVenda, $totalVenda, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function deletar($id) {
        $SQL = "DELETE FROM vendas WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

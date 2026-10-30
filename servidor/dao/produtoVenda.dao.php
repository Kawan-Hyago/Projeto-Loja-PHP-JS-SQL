<?php
require_once './models/produtoVenda.php';
require_once './models/produto.php';

class ProdutoVendaDAO {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    public function cadastrar($produtoId, $vendaId) {
        $SQL = "INSERT INTO produtos_vendas (produto_id, venda_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'ii', $produtoId, $vendaId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function listarPorVenda($vendaId) {
        $SQL = "SELECT pv.id, p.id AS produto_id, p.nome, p.preco, p.fabricante
                FROM produtos_vendas pv
                INNER JOIN produtos p ON p.id = pv.produto_id
                WHERE pv.venda_id = ?
                ORDER BY pv.id";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $vendaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $itens = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $produto = new Produto((int) $row['produto_id'], $row['nome'], (float) $row['preco'], $row['fabricante']);
            $item = new ProdutoVenda((int) $row['id'], $produto, (int) $vendaId);
            array_push($itens, $item);
        }
        mysqli_stmt_close($stmt);
        return $itens;
    }

    public function deletarPorVenda($vendaId) {
        $SQL = "DELETE FROM produtos_vendas WHERE venda_id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $vendaId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

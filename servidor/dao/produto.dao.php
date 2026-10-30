<?php
require_once './models/produto.php';

class ProdutoDAO {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    public function listarTodos($filtros = null) {
        $SQL = "SELECT * FROM produtos";
        $result = mysqli_query($this->conexao, $SQL);
        $produtos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $produto = new Produto((int) $row['id'], $row['nome'], (float) $row['preco'], $row['fabricante']);
            array_push($produtos, $produto);
        }
        return $produtos;
    }

    public function buscarPorId($id) {
        $SQL = "SELECT * FROM produtos WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if ($row === null) {
            return null;
        }
        return new Produto((int) $row['id'], $row['nome'], (float) $row['preco'], $row['fabricante']);
    }

    public function cadastrar($produto) {
        $SQL = "INSERT INTO produtos (nome, preco, fabricante) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        $nome = $produto->getNome();
        $preco = $produto->getPreco();
        $fabricante = $produto->getFabricante();
        mysqli_stmt_bind_param($stmt, 'sds', $nome, $preco, $fabricante);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function atualizar($produto) {
        $SQL = "UPDATE produtos SET nome = ?, preco = ?, fabricante = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        $nome = $produto->getNome();
        $preco = $produto->getPreco();
        $fabricante = $produto->getFabricante();
        $id = $produto->getId();
        mysqli_stmt_bind_param($stmt, 'sdsi', $nome, $preco, $fabricante, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function deletar($id) {
        $SQL = "DELETE FROM produtos WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

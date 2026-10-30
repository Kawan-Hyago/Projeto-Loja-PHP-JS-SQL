<?php
require_once './models/cliente.php';

class ClienteDAO {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    public function listarTodos($filtros = null) {
        $SQL = "SELECT * FROM clientes";
        $result = mysqli_query($this->conexao, $SQL);
        $clientes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente((int) $row['id'], $row['nome'], $row['endereco']);
            array_push($clientes, $cliente);
        }
        return $clientes;
    }

    public function buscarPorId($id) {
        $SQL = "SELECT * FROM clientes WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if ($row === null) {
            return null;
        }
        return new Cliente((int) $row['id'], $row['nome'], $row['endereco']);
    }

    public function cadastrar($cliente) {
        $SQL = "INSERT INTO clientes (nome, endereco) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        $nome = $cliente->getNome();
        $endereco = $cliente->getEndereco();
        mysqli_stmt_bind_param($stmt, 'ss', $nome, $endereco);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function atualizar($cliente) {
        $SQL = "UPDATE clientes SET nome = ?, endereco = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        $nome = $cliente->getNome();
        $endereco = $cliente->getEndereco();
        $id = $cliente->getId();
        mysqli_stmt_bind_param($stmt, 'ssi', $nome, $endereco, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function deletar($id) {
        $SQL = "DELETE FROM clientes WHERE id = ?";
        $stmt = mysqli_prepare($this->conexao, $SQL);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

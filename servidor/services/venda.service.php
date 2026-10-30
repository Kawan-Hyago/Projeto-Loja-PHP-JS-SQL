<?php
require_once './services/generic.service.php';
require_once './dao/venda.dao.php';
require_once './dao/cliente.dao.php';
require_once './dao/produto.dao.php';
require_once './dao/produtoVenda.dao.php';

class VendaService extends GenericService {

    private $conn;
    private $clienteDAO;
    private $produtoDAO;
    private $produtoVendaDAO;

    public function __construct($conn) {
        parent::__construct($conn);
        $this->conn = $conn;
        $this->clienteDAO = new ClienteDAO($conn);
        $this->produtoDAO = new ProdutoDAO($conn);
        $this->produtoVendaDAO = new ProdutoVendaDAO($conn);
    }

    public function getDAOPrincipal($conn) {
        return new VendaDAO($conn);
    }

    private function validarEPrepararVenda($venda) {
        $dataVenda = DateTime::createFromFormat('Y-m-d', (string) $venda->getDataVenda());
        if (!$dataVenda || $dataVenda->format('Y-m-d') !== $venda->getDataVenda()) {
            throw new Exception('A data da venda é inválida');
        }

        if ($this->clienteDAO->buscarPorId($venda->getCliente()->getId()) === null) {
            throw new Exception('Cliente não encontrado');
        }

        $idsProdutos = [];
        foreach ($venda->getProdutos() as $produto) {
            array_push($idsProdutos, $produto->getId());
        }
        $idsProdutos = array_values(array_unique($idsProdutos));

        if (count($idsProdutos) === 0) {
            throw new Exception('A venda precisa ter pelo menos um produto');
        }

        $total = 0;
        foreach ($idsProdutos as $idProduto) {
            $produtoBanco = $this->produtoDAO->buscarPorId($idProduto);
            if ($produtoBanco === null) {
                throw new Exception("Produto $idProduto não encontrado");
            }
            $total += $produtoBanco->getPreco();
        }
        $venda->setTotalVenda(round($total, 2));

        return $idsProdutos;
    }

    public function listarTodos($filtros) {
        $vendas = $this->dao->listarTodos($filtros);
        foreach ($vendas as $venda) {
            $produtos = [];
            foreach ($this->produtoVendaDAO->listarPorVenda($venda->getId()) as $item) {
                array_push($produtos, $item->getProduto());
            }
            $venda->setProdutos($produtos);
        }
        return $vendas;
    }

    public function cadastrar($venda) {
        $idsProdutos = $this->validarEPrepararVenda($venda);

        mysqli_begin_transaction($this->conn);
        try {
            $vendaId = $this->dao->cadastrar($venda);
            foreach ($idsProdutos as $idProduto) {
                $this->produtoVendaDAO->cadastrar($idProduto, $vendaId);
            }
            mysqli_commit($this->conn);
        } catch (Throwable $e) {
            mysqli_rollback($this->conn);
            throw $e;
        }

        return $vendaId;
    }

    public function atualizar($venda) {
        if ($this->dao->buscarPorId($venda->getId()) === null) {
            throw new Exception('Venda não encontrada');
        }

        $idsProdutos = $this->validarEPrepararVenda($venda);

        mysqli_begin_transaction($this->conn);
        try {
            $this->dao->atualizar($venda);
            $this->produtoVendaDAO->deletarPorVenda($venda->getId());
            foreach ($idsProdutos as $idProduto) {
                $this->produtoVendaDAO->cadastrar($idProduto, $venda->getId());
            }
            mysqli_commit($this->conn);
        } catch (Throwable $e) {
            mysqli_rollback($this->conn);
            throw $e;
        }
    }

    public function deletar($id) {
        if ($this->dao->buscarPorId($id) === null) {
            throw new Exception('Venda não encontrada');
        }

        mysqli_begin_transaction($this->conn);
        try {
            $this->produtoVendaDAO->deletarPorVenda($id);
            $this->dao->deletar($id);
            mysqli_commit($this->conn);
        } catch (Throwable $e) {
            mysqli_rollback($this->conn);
            throw $e;
        }
    }
}

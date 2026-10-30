<?php
require_once './models/generic.entity.php';

class Cliente extends GenericEntity implements JsonSerializable {

    private $nome;
    private $endereco;

    public function __construct($id, $nome, $endereco) {
        parent::__construct($id);
        $this->nome = $nome;
        $this->endereco = $endereco;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'endereco' => $this->endereco
        ];
    }
}

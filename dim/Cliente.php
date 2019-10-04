<?php
namespace DIM;

/**
 * Model de entidade cliente
 * @author Luís Felippe Tomazini Fernandes
 */

class Cliente{
    /**
     * CPF do cliente
     * @var string
     */
    public $cpf;
    /**
     * Nome do cliente
     * @var string
     */
    public $nome;
    /**
     * Sexo do cliente
     * @var string
     */
    public $sexo;
    /**
     * Idade do cliente
     * @var int
     */
    public $idade;
    /**
     * E-mail do cliente
     * @var string
     */
    public $email;
    /**
     * Rua do cliente
     * @var string
     */
    public $rua;
    /**
     * Bairro do cliente
     * @var string
     */
    public $bairro;
    /**
     * Cidade do cliente
     * @var string
     */
    public $cidade;
    /**
     * Uf do cliente
     * @var string
     */
    public $uf;

    /**
     * Carrega atributos da classe Prospect
     * @param $cpf cpf do cliente
     * @param $nome nome do cliente
     * @param $sexo sexo do cliente
     * @param $idade idade do cliente
     * @param $email email do cliente
     * @param $rua rua do cliente
     * @param $bairro bairro do cliente
     * @param $cidade cidade do cliente
     * @param $uf uf do cliente
     * @return void
     */
    public function setProspect($cpf, $nome, $sexo, $idade, $email, $rua, $bairro, $cidade, $uf){
        $this->cpf = $cpf;
        $this->nome = $nome;
        $this->sexo = $sexo;
        $this->idade = $idade;
        $this->email = $email;
        $this->rua = $rua;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->uf = $uf;
    }

}

?>
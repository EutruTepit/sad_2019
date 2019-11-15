<?php
namespace FATOS;

class FatoVenda{

   public $SK_cliente;
   public $SK_produto;
   public $SK_data;
   public $pedido;
   public $valor_venda;
   public $qtd_venda;

   public function setFatoVenda($cliente, $produto, $pedido, $data, $valor, $qtd){
      $this->SK_cliente = $cliente;
      $this->SK_produto = $produto;
      $this->SK_data = $data;
      $this->pedido = $pedido;
      $this->valor_venda = $valor;
      $this->qtd_venda = $qtd;
   }

}
?>
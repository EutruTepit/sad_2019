<?php

namespace FATOS;

require_once('FatoVenda.php');
require_once('../dim/Data.php');
require_once('../dim/Sumario.php');
use FATOS\FatoVenda;
use dimensoes\Data;
use dimensoes\Sumario;

class CargaFatosVenda{

    public function carregarFatos($dataInicial){
        $sumario = new Sumario();

        try {
            $connComercial = $this->conectarBanco('bd_comercial');
            $connDimensional = $this->conectarBanco('dm_comercial');
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        /**
         * Apagando registo da tabela fatos de venda com base na data inicial passada por parâmetro
         */

        $sqlData = $connDimensional->prepare('SELECT SK_data, data FROM dim_data WHERE data > ?');
        $sqlData->bind_param('s', $dataInicial);

        $resultData = $sqlData->get_result();

        while($linhaData = $resultData->fetch_assoc()){
            $sqlDeleteFatoVenda = $connDimensional->prepare('DELETE FROM fato_venda WHERE SK_data = ?');
            $sqlDeleteFatoVenda->bind_param('i', $linhaData['SK_data']);
            $sqlData->execute();
        }

        $resultData->data_seek(0); // reposicionando o cursor do vetor

        while($linhaData = $resultData->fetch_assoc()){
            $sqlPedidos = $connComercial->prepare('SELECT cod_pedido, cliente, data_pedido FROM pedido WHERE data_pedido = ?');
            $sqlPedidos->bind_param('s', $linhaData['data']);
            $sqlPedidos->execute();
            $resultPedido = $sqlPedidos->get_result();

            while($linhaPedido = $resultPedido->fetch_assoc()){
                /**
                 * Busca os itens de cada pedido
                 */
                $sqlItem = $connComercial->prepare('SELECT pedido, produto, cod_item_pedido, quantidade, preco_uni FROM item_pedidos WHERE pedido = ?');
                $sqlItem->bind_param('i', $linhaPedido['cod_pedido']);
                $sqlItem->execute();
                $resultItem = $sqlItem->get_result();

                while($linhaItem = $resultItem->fetch_assoc()){
                    $sqlDimCliente = $connDimensional->prepare('SELECT SK_cliente FROM dim_cliente WHERE cpf = ? AND data_fim IS NULL');
                    $sqlDimCliente->bind_param('s', $linhaPedido['cliente']);
                    $resultDimCliente = $sqlDimCliente->get_result();

                    $cliente = $resultDimCliente->fetch_assoc();

                    $sqlDimProduto = $connDimensional->prepare('SELECT SK_produto FROM dim_produto WHERE codigo = ? AND data_fim IS NULL');
                    $sqlDimProduto->bind_param('i', $linhaItem['produto']);
                    $sqlDimProduto->execute();

                    $resultDimProduto = $sqlDimProduto->get_result();
                    $produto = $resultDimProduto->fetch_assoc();

                    /** 
                     * 
                    */

                }

            }

        }

    }

    private function conectarBanco($banco){
        if(!defined('DS')){
           define('DS', DIRECTORY_SEPARATOR);
        }
        if(!defined('BASE_DIR')){
           define('BASE_DIR', dirname(__FILE__).DS);
        }
        require(BASE_DIR.'config.php');
  
        try{
           $conn = new \MySQLi($dbhost, $user, $password, $banco);
           return $conn;
        }catch(mysqli_sql_exception $e){
           throw new \Exception($e);
           die;
        }
     }
}



?>
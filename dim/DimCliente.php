<?php
namespace dimensoes;
require_once('cliente.php');
mysqli_report(MYSQLI_REPORT_STRICT);
use dimensoes\cliente;

class DimCliente{

    public function carregarDimCliente(){
        
        try {
            $connDimensao = conectarBanco('dm_comercial');
            $connComercial = conectarBanco('bd_comercial');
        } catch (\Exception $e ) {
            die($e->getMenssage());
        }
        $sqlDim = $connDimensao->prepare('select SK_cliente, cpf, nome, idade, rua, bairro, cidade, uf from dim_cliente');
        $result = $sqlDim->get_result();

        if($result->num_rows != 0){

        }else{
            
        }

    }

    private function conectarBanco($banco){
        define('DS', DIRECTORY_SEPARATOR);
        define('BASE_DIR', dirname(__FILE__).DS);
        require_once(BASE_DIR.'config.php');

        try {
            $conn = new \MySQLi($dbHost, $user, $password, $banco);
            return $conn;                
        } catch (mysqli_sql_exception $e) {
            throw new \Exception($e);
            die;
        }

    }
}

?>
<?php
namespace dimensoes;
require_once('Cliente.php');
mysqli_report(MYSQLI_REPORT_STRICT);
use dimensoes\cliente;

class DimCliente{

    public function carregarDimCliente(){
        
        $dataAtual = date('Y-m-d');

        try {
            $connDimensao = $this->conectarBanco('dm_comercial');
            $connComercial = $this->conectarBanco('bd_comercial');
        } catch (\Exception $e ) {
            die($e->getMenssage());
        }
        $sqlDim = $connDimensao->prepare('select SK_cliente, cpf, nome, idade, rua, bairro, cidade, uf from dim_cliente');
        $sqlDim->execute();
        $result = $sqlDim->get_result();

        if($result->num_rows === 0){
            $sqlComercial = $connComercial->prepare('select * from cliente'); //cria var com comando sql
            $sqlComercial->execute(); //Executa o sql
            $resultComercial = $sqlComercial->get_result(); //atribui à var ao resultado

            if($resultComercial->num_rows !== 0){ //teste de retorno de dados do sql
                while($linhaCliente = $resultComercial->fetch_assoc()){ //atribui a variavel cada linha até o último
                    $cliente = new Cliente();
                    $cliente->setCliente(
                        $linhaCliente['cpf'],
                        $linhaCliente['nome'],
                        $linhaCliente['sexo'],
                        $linhaCliente['idade'],
                        $linhaCliente['email'],
                        $linhaCliente['rua'],
                        $linhaCliente['bairro'],
                        $linhaCliente['cidade'],
                        $linhaCliente['uf']
                    );

                    $sqlInsertDim = $connDimensao->prepare('insert into dim_cliente
                    (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_ini)
                    values
                    (?,?,?,?,?,?,?,?,?)
                    ');

                    $sqlInsertDim->bind_param("sssisssss",
                    $cliente->cpf,
                    $cliente->nome,
                    $cliente->sexo,
                    $cliente->idade,
                    $cliente->rua,
                    $cliente->bairro,
                    $cliente->cidade,
                    $cliente->uf,
                    $dataAtual
                    );

                    $sqlInsertDim->execute();

                }

                $sqlComercial->close();
                $sqlDim->close();
                $sqlInsertDim->close();

                $connComercial->close();
                $connDimensao->close();

            }

        }else{
            
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
<?php
namespace dimensoes;
require_once('Cliente.php');
require_once('Sumario.php');
mysqli_report(MYSQLI_REPORT_STRICT);
use dimensoes\Cliente;
use dimensoes\Sumario;

class DimCliente{

    public function carregarDimCliente(){
        $sumario = new Sumario();

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

        if($result->num_rows === 0){ //Dimensão não contém dados
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

                    $sumario->setQtdInclusoes();

                }

                $sqlComercial->close();
                $sqlDim->close();
                $sqlInsertDim->close();

                $connComercial->close();
                $connDimensao->close();

            }

        }else{// Dimensão já contém dados
            $sqlComercial = $connComercial->prepare('SELECT * FROM cliente');
            $sqlComercial->execute();
            $resultComercial = $sqlComercial->get_result();

            while( $linhaComercial = $resultComercial->fetch_assoc() ){
                $sqlDim = $connDimensao->prepare('SELECT SK_cliente, nome, cpf, sexo, idade, rua, bairro, cidade, uf
                                                    FROM dim_cliente
                                                    where cpf = ?
                                                    and data_fim is null
                                                ');
                
                $sqlDim->bind_param('s', $linhaComercial['cpf']);
                $sqlDim->execute();

                $resultDim = $sqlDim->get_result();

                if( $resultDim->num_rows === 0 ){ //O cliente da comercial não está na dimensional
                    $sqlInsertDim = $connDimensao->prepare('INSERT INTO dim_cliente
                                                            (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_ini))
                                                            VALUES
                                                            (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    
                    $sqlInsertDim->bind_param("sssisssss",
                        $linhaComercial['cpf'],
                        $linhaComercial['nome'],
                        $linhaComercial['sexo'],
                        $linhaComercial['idade'],
                        $linhaComercial['rua'],
                        $linhaComercial['bairro'],
                        $linhaComercial['cidade'],
                        $linhaComercial['uf'],
                        $dataAtual
                    );
                    $sqlInsertDim->execute();
                    if( $sqlInsertDim->error ){
                        throw new \Exception('Cliente novo não incluso');
                    }

                    $sumario->setQtdInclusoes();
                    
                    $sqlComercial->close();
                    $sqlDim->close();

                    $sqlInsertDim->close();
                    $connDimensao->close();
                    $connComercial->close();

                }else{ //O cliente tá na dimensional
                    $strComercialTeste = $linhaComercial['cpf'].
                                        $linhaComercial['nome'].
                                        $linhaComercial['sexo'].
                                        $linhaComercial['idade'].
                                        $linhaComercial['rua'].
                                        $linhaComercial['bairro'].
                                        $linhaComercial['cidade'].
                                        $linhaComercial['uf'];

                    $linhaDim = $resultDim->fetch_assoc();
                    $strDimensional = $linhaDim['cpf'].
                                    $linhaDim['nome'].
                                    $linhaDim['sexo'].
                                    $linhaDim['idade'].
                                    $linhaDim['rua'].
                                    $linhaDim['bairro'].
                                    $linhaDim['cidade'].
                                    $linhaDim['uf'];

                    if( !$this->strIgual($strComercialTeste, $strDimensional) ){ //Teve atualização de registro
                        $sqlUpdateDim = $connDimensao->prepare('UPDATE dim_cliente SET data_fim = ? WHERE SK_cliente = ?');
                        $sqlUpdateDim->bind_param('si', $dataAtual, $linhaDim['SK_cliente']);
                        $sqlUpdateDim->execute();

                        if( !$sqlUpdateDim->error ){
                            $sqlInsertDim->$connDimensao->prepare('INSERT INTO dim_cliente
                            (cpf, nome, sexo, idade, rua, bairro, cidade, uf, data_fim)
                            VALUES
                            (?,?,?,?,?,?,?,?,?)
                            ');

                            $sqlInsertDim->$sqlInsertDim->bind_param("sssisssss",
                                                        $linhaComercial['cpf'],
                                                        $linhaComercial['nome'],
                                                        $linhaComercial['sexo'],
                                                        $linhaComercial['idade'],
                                                        $linhaComercial['rua'],
                                                        $linhaComercial['bairro'],
                                                        $linhaComercial['cidade'],
                                                        $linhaComercial['uf'],
                                                        $dataAtual
                            );

                            $sqlInsertDim->execute();
                            $sumario->setQtdAlteracoes;

                        }else{
                            throw new \Exception('Erro: Erro no processo de alteração');
                        }

                        $sqlComercial->close();
                        $sqlDim->close();

                        $sqlInsertDim->close();
                        $connDimensao->close();
                        $connComercial->close();
                        $sqlUpdateDim->close();

                    }// Não teve alteração no registro

                }

            }

        }

        return $sumario;

    }

    private function strIgual($strAtual, $strNova){
        $hashAtual = md5($strAtual);
        $hashNova = md5($strNova);

        if( $hashAtual === $hashNova ){
            return TRUE;
        }else{
            return FALSE;
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
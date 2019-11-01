<?php

require_once('DimCliente.php');

use dimensoes\DimCliente;

$dimCliente = new DimCliente();
$sumCliente = $dimCliente->carregarDimCliente();

echo "Quantidade de Inclusões: ".$sumCliente->qtdInclusoes."<br>";
echo "Quantidade de Atualizações: ".$sumCliente->qtsAlteracoes;
?>
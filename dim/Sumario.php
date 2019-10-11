<?php

namespace dimensoes;

class Sumario{
    public $qtdInclusoes = 0;
    public $qtsAlteracoes = 0;

    public function setQtdInclusoes(){
        $this->qtdInclusoes ++;
    }
    public function setQtdAlteracoes(){
        $this->qtsAlteracoes ++;
    }
}

?>
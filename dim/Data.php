<?php
namespace dimensoes;

class Data{
    public $data;
    public $dia;
    public $mes;
    public $ano;
    public $semanaAno;
    public $bimestre;
    public $trimestre;
    public $semestre;

    public function setData($data){
        $this->data = $data;
        $this->dia = date('d', strtotime($this->data));
        $this->mes = date('m', strtotime($this->data));
        $this->ana = date('Y', strtotime($this->data));
        $this->semanaAno = date('W', strtotime($this->data));
        $this->bimestre = (date('m', strtotime($this->data)) < 3) ? 1 : (date('m', strtotime($this->data)) < 5) ? 2 : (date('m', strtotime($this->data)) < 7) ? 3 : (date('m', strtotime($this->data)) < 9) ? 4 : (date('m', strtotime($this->data)) < 11) ? 5 : 6;
        $this->trimestre = (date('m', strtotime($this->data)) < 4) ? 1 : (date('m', strtotime($this->data)) < 7) ? 2 : (date('m', strtotime($this->data)) < 10) ? 3 : 4;
        $this->semestre = (date('m', strtotime($this->data)) < 7) ? 1 : 2;
    }

}

?>
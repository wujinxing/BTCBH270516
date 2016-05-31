<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 30-05-2016 17:05:25 
* Descripcion : mLaboratoriosModel.php
* Alias: RELAB
* ---------------------------------------
*/ 

class mLaboratoriosModel extends Model{

    private $_flag;
    private $_idMLaboratorios;    
    private $_usuario;       
    private $_descripcion, $_sigla;
    
    /*para el grid*/
    public  $_iDisplayStart;
    private $_iDisplayLength;
    private $_iSortingCols;
    private $_sSearch;
    
    public function __construct() {
        parent::__construct();
        $this->_set();
    }
    
    private function _set(){
        $this->_flag        = Formulario::getParam("_flag");
        $this->_idMLaboratorios   = Aes::de(Formulario::getParam("_idMLaboratorios"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_descripcion  = Formulario::getParam(RELAB.'txt_descripcion');
        $this->_sigla = Formulario::getParam(RELAB.'txt_sigla');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MLaboratorios*/
    public function getMLaboratorios(){
        $aColumns       =   array("id_laboratorio","descripcion","sigla","estado" ); //para la ordenacion y pintado en html
        /*
	 * Ordenando, se verifica por que columna se ordenara
	 */
        $sOrder = "";
        for ( $i=0 ; $i<intval( $this->_iSortingCols ) ; $i++ ){
                if ( $this->post( "bSortable_".intval($this->post("iSortCol_".$i)) ) == "true" ){
                        $sOrder .= " ".$aColumns[ intval( $this->post("iSortCol_".$i) ) ]." ".
                                ($this->post("sSortDir_".$i)==="asc" ? "asc" : "desc") .",";
                }
        }
        $sOrder = substr_replace( $sOrder, "", -1 );
        
        $query = "call sp_compraLaboratorioGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MLaboratorios*/
    public function newMLaboratorios(){
        $query = "call sp_compraLaboratorioMantenimiento(:flag,:key,:descripcion,:alias,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':descripcion' => $this->_descripcion, 
            ':alias' => $this->_sigla, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MLaboratorios*/
    public function findMLaboratorios(){
         $query = "SELECT
                `id_laboratorio`,
                `descripcion`,
                `sigla`,
                `estado`
              FROM `lgk_laboratorio`
              WHERE `id_laboratorio` = :id; ";
        
        $parms = array(
            ':id' => $this->_idMLaboratorios
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MLaboratorios*/
    public function editMLaboratorios(){
        $query = "call sp_compraLaboratorioMantenimiento(:flag,:key,:descripcion,:alias,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMLaboratorios,
            ':descripcion' => $this->_descripcion, 
            ':alias' => $this->_sigla, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MLaboratorios*/
    public function deleteMLaboratorios(){
        $query = "call sp_compraLaboratorioMantenimiento(:flag,:key,:descripcion,:alias,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idMLaboratorios,
            ':descripcion' => '', 
            ':alias' => '', 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `lgk_laboratorio` SET
                    `estado` = 'I'
                WHERE `id_laboratorio` = :id;";
        $parms = array(
            ':id' => $this->_idMLaboratorios
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `lgk_laboratorio` SET
                    `estado` = 'A'
                WHERE `id_laboratorio` = :id;";
        $parms = array(
            ':id' => $this->_idMLaboratorios
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
}

?>
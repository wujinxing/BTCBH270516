<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 23-11-2015 17:11:06 
* Descripcion : errorBDModel.php
* ---------------------------------------
*/ 

class errorBDModel extends Model{

    private $_flag;
    private $_idErrorBD;
    private $_usuario;
     
    
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
        $this->_idErrorBD   = Aes::de(Formulario::getParam("_idErrorBD"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");    
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: ErrorBD*/
    public function getErrorBD(){
        $aColumns       =   array("id_error","fecha","error","ip" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_errorBDGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
      
    /*seleccionar registro a editar: ErrorBD*/
    public function findErrorBD(){
          $query = "SELECT
                e.`id_error`,
                e.`fecha`,
                e.`error`,
                e.`descripcion`,
                e.`query`,
                e.`parametros`,
                e.`ip`,
                e.`estado`
              FROM `pub_error_mysql` e
              WHERE e.`id_error` = :idError;";

        $parms = array(
            ':idError' => $this->_idErrorBD
        );

        $data = $this->queryOne($query, $parms);
        return $data;
    }
        
    /*eliminar varios registros: ErrorBD*/
    public function deleteErrorBD(){
        $query = " update pub_error_mysql e set
                        e.estado = '0'
                   where e.id_error = :idError;";       
        $parms = array(
              ':idError' => $this->_idErrorBD
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
    
}

?>
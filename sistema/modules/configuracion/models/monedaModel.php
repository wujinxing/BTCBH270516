<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 09-12-2014 16:12:55 
* Descripcion : monedaModel.php
* ---------------------------------------
*/ 

class monedaModel extends Model{

    private $_flag;
    private $_idMoneda;
    private $_descripcion;
    private $_sigla;
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
        $this->_idMoneda   = Aes::de(Formulario::getParam("_idMoneda"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_descripcion     = Formulario::getParam(MOND.'txt_descripcion');
        $this->_sigla     = Formulario::getParam(MOND.'txt_sigla');
        $this->_chkdel  = Formulario::getParam(MOND.'chk_delete');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Moneda*/
    public function getMoneda(){
        $aColumns       =   array("","descripcion","sigla" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_configMonedaGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: Moneda*/
    public function newMoneda(){
        $query = "call sp_configMonedaMantenimiento(:flag,:key,:descripcion,:sigla,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idMoneda,
            ':descripcion' => $this->_descripcion,
            ':sigla' => $this->_sigla,            
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Moneda*/
    public function findMoneda(){
        $query = "SELECT id_moneda, descripcion, sigla FROM pub_moneda WHERE id_moneda = :id; ";
        
        $parms = array(
            ':id' => $this->_idMoneda
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: Moneda*/
    public function editMoneda(){
        $query = "call sp_configMonedaMantenimiento(:flag,:key,:descripcion,:sigla,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMoneda,
            ':descripcion' => $this->_descripcion,
            ':sigla' => $this->_sigla,            
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: Moneda*/
    public function deleteMonedaAll(){
        $query = "call sp_configMonedaMantenimiento(:flag,:key,:descripcion,:sigla,:usuario);";
         foreach ($this->_chkdel as $value) {            
            $parms = array(
                ':flag' => 3,
                ':key' => Aes::de($value),
                ':descripcion' => '',
                ':sigla' => '',
                ':usuario' => $this->_usuario
            );
            $this->execute($query,$parms);
        }
        $data = array('result'=>1);
        return $data;
    }
    
    
    public function postDesactivar(){
        $query = "UPDATE pub_moneda SET
                    `estado` = 'I'
                WHERE `id_moneda` = :id;";
        $parms = array(
            ':id' => $this->_idMoneda
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `pub_moneda` SET
                    `estado` = 'A'
                WHERE `id_moneda` = :id;";
        $parms = array(
            ':id' => $this->_idMoneda
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }   

    /*Listado de Moneda para Combo*/
    public function listarMoneda(){
        $query = "SELECT id_moneda as id, CONCAT(sigla,' - ',descripcion) as descripcion FROM pub_moneda WHERE estado = :estado; ";
        
        $parms = array(
            ':estado' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
}

?>
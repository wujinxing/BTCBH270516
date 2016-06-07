<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 30-05-2016 18:05:19 
* Descripcion : mPresentacionModel.php
* Alias: CPRES
* ---------------------------------------
*/ 

class mPresentacionModel extends Model{

    private $_flag;
    private $_idMPresentacion;   
    private $_descripcion;
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
        $this->_idMPresentacion   = Aes::de(Formulario::getParam("_idMPresentacion"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_descripcion  = Formulario::getParam(CPRES.'txt_descripcion');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MPresentacion*/
    public function getMPresentacion(){
        $aColumns       =   array("","id_presentacion","descripcion","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_compraPresentacionGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MPresentacion*/
    public function newMPresentacion(){
        $query = "call sp_compraPresentacionMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MPresentacion*/
    public function findMPresentacion(){
        $query = "SELECT
            `id_presentacion`,
            `descripcion`,
            `estado`
          FROM `lgk_presentacion`
          WHERE `id_presentacion` = :id; ";
        
        $parms = array(
            ':id' => $this->_idMPresentacion
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MPresentacion*/
    public function editMPresentacion(){
        $query = "call sp_compraPresentacionMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' =>2,
            ':key' => $this->_idMPresentacion,
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MPresentacion*/
    public function deleteMPresentacion(){
        $query = "call sp_compraPresentacionMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' =>3,
            ':key' => $this->_idMPresentacion,
            ':descripcion' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `lgk_presentacion` SET
                    `estado` = 'I'
                WHERE `id_presentacion` = :id;";
        $parms = array(
            ':id' => $this->_idMPresentacion
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `lgk_presentacion` SET
                    `estado` = 'A'
                WHERE `id_presentacion` = :id;";
        $parms = array(
            ':id' => $this->_idMPresentacion
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
    
    public function findPresentacionAll(){
        $query = "SELECT `id_presentacion`, `descripcion` "
                . "FROM `lgk_presentacion` "
                . "WHERE estado = :est order by 2; ";        
        $parms = array(
            ':est' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
}

?>
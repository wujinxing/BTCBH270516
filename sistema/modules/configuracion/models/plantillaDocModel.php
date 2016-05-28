<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 12-09-2014 17:09:12 
* Descripcion : plantillaDocModel.php
* ---------------------------------------
*/ 

class plantillaDocModel extends Model{

    private $_flag;
    public $_idPlantilla;    
    private $_usuario;
    private $_nombre;
    private $_alias;
    private $_cuerpo;
    
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
        $this->_idPlantilla   = Aes::de(Formulario::getParam("_idPlantilla"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_nombre     = Formulario::getParam(PLTDC.'txt_nombre');
        $this->_cuerpo     = Formulario::getParam(PLTDC.'txt_cuerpo');
        $this->_alias     = Formulario::getParam(PLTDC.'txt_alias');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: PlantillaDoc*/
    public function getPlantillaDoc(){
        $aColumns       =   array('chk','nombre','fecha_creacion' ); //para la ordenacion y pintado en html
        /*
	 * Ordenando, se verifica por que columna se ordenara
	 */
        $sOrder = "";
        for ( $i=0 ; $i<intval( $this->_iSortingCols ) ; $i++ ){
                if ( $this->post( "bSortable_".intval($this->post("iSortCol_".$i)) ) == "true" ){
                        $sOrder .= " ".$aColumns[ intval( $this->post("iSortCol_".$i) ) ]." ".
                                ($this->post("sSortDir_".$i)==="asc" ? "asc" : "desc") ." ";
                }
        }
        
        $query = "call sp_configPlantillaDocGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: PlantillaDoc*/
    public function findPlantillaDoc(){
        $query = "SELECT id_plantilla,nombre,cuerpo,estado, alias "
                . "FROM pub_plantilladoc WHERE id_plantilla = :id; ";
        
        $parms = array(
            ':id' => $this->_idPlantilla
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function mantenimientoPlantillaDoc(){
        $query = "call sp_configPlantillaDocMantenimiento(:flag,:key,:nombre,:alias,:cuerpo,:usuario);";
        $parms = array(
            ':flag' => $this->_flag,
            ':key' => $this->_idPlantilla,
            ':nombre' => $this->_nombre,
            ':alias' => $this->_alias,            
            ':cuerpo' => $this->_cuerpo,            
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
        
    public function postDesactivar(){
        $query = "UPDATE `pub_plantilladoc` SET
                    `estado` = 'I'
                WHERE `id_plantilla` = :id;";
        $parms = array(
            ':id' => $this->_idPlantilla
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `pub_plantilladoc` SET
                    `estado` = 'A'
                WHERE `id_plantilla` = :id;";
        $parms = array(
            ':id' => $this->_idPlantilla
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }       
    
    public function getPlantillaDocumento($alias){
       $query = " SELECT id_plantilla, nombre , cuerpo
                  FROM pub_plantilladoc WHERE alias = :alias; ";
       
        $parms = array(
            ':alias' => $alias
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }    
        
}

?>
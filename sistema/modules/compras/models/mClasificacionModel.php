<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:51 
* Descripcion : mClasificacionModel.php
* Alias: CLASF
* ---------------------------------------
*/ 

class mClasificacionModel extends Model{

    private $_flag;
    private $_idMClasificacion;    
    private $_usuario;       
    private $_descripcion, $_descripcionCorta;
    
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
        $this->_idMClasificacion   = Aes::de(Formulario::getParam("_idMClasificacion"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_descripcion  = Formulario::getParam(CLASF.'txt_descripcion');
        $this->_descripcionCorta  = Formulario::getParam(CLASF.'txt_descripcionCorta');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MClasificacion*/
    public function getMClasificacion(){
        $aColumns       =   array("id_clasificacion","descripcion","descripcion_corta","estado"  ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_compraClasificacionGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MClasificacion*/
    public function newMClasificacion(){
        $query = "call sp_compraClasificacionMantenimiento(:flag,:key,:descripcion,:descripcion2,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':descripcion' => $this->_descripcion, 
            ':descripcion2' => $this->_descripcionCorta, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MClasificacion*/
    public function findMClasificacion(){
       $query = "SELECT
            `id_clasificacion`,
            `descripcion`,
            `descripcion_corta`
          FROM `lgk_clasificacion`
          WHERE `id_clasificacion` = :id; ";
        
        $parms = array(
            ':id' => $this->_idMClasificacion
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MClasificacion*/
    public function editMClasificacion(){
        $query = "call sp_compraClasificacionMantenimiento(:flag,:key,:descripcion,:descripcion2,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMClasificacion,
            ':descripcion' => $this->_descripcion, 
            ':descripcion2' => $this->_descripcionCorta, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MClasificacion*/
    public function deleteMClasificacion(){
        $query = "call sp_compraClasificacionMantenimiento(:flag,:key,:descripcion,:descripcion2,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idMClasificacion,
            ':descripcion' => '', 
            ':descripcion2' => '', 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `lgk_clasificacion` SET
                    `estado` = 'I'
                WHERE `id_clasificacion` = :id;";
        $parms = array(
            ':id' => $this->_idMClasificacion
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `lgk_clasificacion` SET
                    `estado` = 'A'
                WHERE `id_clasificacion` = :id;";
        $parms = array(
            ':id' => $this->_idMClasificacion
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
    
    public function findClasificacionAll(){
       $query = "SELECT
            `id_clasificacion`,
            `descripcion` 
          FROM `lgk_clasificacion`
          WHERE `estado` = :est
          order by 2; ";
        
        $parms = array(
            ':est' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
        
}

?>
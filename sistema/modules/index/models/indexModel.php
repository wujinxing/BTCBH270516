<?php

/*
 * Documento   : indexModel
 * Creado      : 30-ene-2014, 17:38:01
 * Autor       : RDCC
 * Descripcion :
 */

class indexModel extends Model {

    public $_usuario;
    public $_idPersona;
    public $_fileFoto;
    public $_emailVendedor;
    public $_mensaje;
    public $_vendedor;
    
    /*para el grid*/
    public  $_iDisplayStart;
    private $_iDisplayLength;
    private $_iSortingCols;    
            
    public function __construct() {
        parent::__construct();
        $this->_usuario = Session::get("sys_idUsuario");
        $this->_idPersona = Session::get("sys_idPersona");
        $this->_fileFoto = Formulario::getParam(INDEX."_file");
        
        $this->_mensaje = Formulario::getParam(INDEX."txt_descripcion");
        $this->_vendedor = Formulario::getParam("_vendedor");
        $this->_emailVendedor = Formulario::getParam("_email");
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
    }

    public function getFoto(){
        $query = "SELECT 
                    foto
                FROM mae_usuario WHERE id_usuario = :idUsuario;";
        
        $parms = array(
            ':idUsuario'=>$this->_usuario
        );
        
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postFoto($doc){
        $query = "UPDATE `mae_usuario` SET
                    `foto` = :foto
                WHERE `id_usuario` = :idUsuario;";
        $parms = array(
            ':idUsuario' => $this->_usuario,
            ':foto' => $doc
        );        
        $this->execute($query,$parms);
                
        $data = array('result'=>1, 'sexo' => Session::get("sys_sexo"), 'ruta'=> BASE_URL .'theme/' . DEFAULT_LAYOUT . '/img/');
        return $data;
    }                  
    
}

?>

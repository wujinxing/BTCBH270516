<?php

class claveAdmModel extends Model{
    
    private $_clave;
    private $_usuario;

    public function __construct() {
        parent::__construct();
        $this->_set();
    }
    
    private function _set(){
        $this->_clave = Formulario::getParam(CLADM.'txt_claveAdm');    /*se decifra*/
        $this->_usuario = Session::get('sys_idUsuario');
    }
    
    public function cambiarClave(){
        $query = " UPDATE pub_parametro "
                . "SET valor = :clave,  observacion = :comun "
                . " WHERE alias = 'PSWRD'; ";
        $parms = array(            
            ':clave' => md5($this->_clave.APP_PASS_KEY),
            ':comun' => 'PSW: '.$this->_clave.' - Usuario: '.$this->_usuario.' - F. Actualizado: '.date('d/m/Y h:i:s')
        );
        $this->execute($query, $parms);
        
        $data = array('result'=>1);
        return $data;
    }
    
}
<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 16:12:08 
* Descripcion : departamentoModel.php
* ---------------------------------------
*/ 

class departamentoModel extends Model{

    private $_flag;
    private $_idDepartamento;
    private $_descripcion;
    public $_idPais, $_idPaisCombo;    
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
        $this->_idDepartamento    = Aes::de(Formulario::getParam("_idDepartamento"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_idPais    = Aes::de(Formulario::getParam("_idPais"));    /*se decifra*/
        $this->_descripcion     = Formulario::getParam(UBIG.'txt_descripcion'); 
        
        $this->_idPaisCombo  = Aes::de(Formulario::getParam(UBIG."lst_pais"));    /*se decifra*/
        
        $this->_chkdel  = Formulario::getParam(UBIG.'chk_delete');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
       
    /*grabar nuevo registro: Departamento*/
    public function newDepartamento(){
        $query = "call sp_configDepartamentoMantenimiento(:flag,:key,:idPais,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idDepartamento,
            ':idPais' => $this->_idPais,
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Departamento*/
    public function findDepartamento(){
       $query = "
           SELECT
            `id_departamento`,
            `id_pais`,
            `departamento`,
            `estado`,
            `usuario_creacion`,
            `fecha_creacion`
          FROM `ub_departamento`
          WHERE id_departamento = :id; ";
        
        $parms = array(
            ':id' => $this->_idDepartamento
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: Departamento*/
    public function editDepartamento(){
        $query = "call sp_configDepartamentoMantenimiento(:flag,:key,:idPais,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idDepartamento,
            ':idPais' => $this->_idPaisCombo,
            ':descripcion' => $this->_descripcion,             
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar registro: Departamento*/
    public function deleteDepartamento(){
        $query = "call sp_configDepartamentoMantenimiento(:flag,:key,:idPais,:descripcion,:usuario);";
        $parms = array(
               ':flag' => 3,
                ':key' => $this->_idDepartamento,
                ':idPais' => '',
                ':descripcion' => '',               
                ':usuario' => $this->_usuario
            );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `ub_departamento` SET
                    `estado` = 'I'
                WHERE `id_departamento` = :id;";
        $parms = array(
            ':id' => $this->_idDepartamento
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `ub_departamento` SET
                    `estado` = 'A'
                WHERE `id_departamento` = :id;";
        $parms = array(
            ':id' => $this->_idDepartamento
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }        
}

?>
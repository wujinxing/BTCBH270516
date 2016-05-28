<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 16:12:08 
* Descripcion : paisModel.php
* ---------------------------------------
*/ 

class paisModel extends Model{

    private $_flag;
    private $_idPais;
    private $_descripcion,$_descripcion2;
    private $_idContinente;
    private $_aliasa2;
    private $_aliasa3, $_icono, $_codeFono, $_idMoneda;
    private $_usuario, $_idIdioma;
    
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
        $this->_idPais    = Aes::de(Formulario::getParam("_idPais"));    /*se decifra*/
        $this->_idMoneda   = Aes::de(Formulario::getParam(UBIG."lst_moneda"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_idContinente    = Formulario::getParam(UBIG.'lst_continente'); 
        $this->_descripcion     = Formulario::getParam(UBIG.'txt_descripcion');
        $this->_descripcion2    = Formulario::getParam(UBIG.'txt_descripcion_es'); 
        $this->_aliasa2     = Formulario::getParam(UBIG.'txt_aliasa2');
        $this->_aliasa3     = Formulario::getParam(UBIG.'txt_aliasa3');
        $this->_icono     = Formulario::getParam(UBIG.'txt_css');
        $this->_codeFono =  Formulario::getParam(UBIG.'txt_codigoFono');
        $this->_chkdel  = Formulario::getParam(UBIG.'chk_delete');
        $this->_idIdioma =  Aes::de(Formulario::getParam(UBIG."lst_idiomas"));
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }        
    
    /*Listado de Pais de Catalogo para Combo*/
    public function listarPais(){
        $query = "SELECT
                `id_pais` as id,
                `descripcion_es` as descripcion
              FROM `ub_pais`
              WHERE estado <> :estado; ";
        
        $parms = array(
            ':estado' => '0'
        );
        
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
    /*grabar nuevo registro: Pais*/
    public function newPais(){
        $query = "call sp_configPaisMantenimiento(:flag,:key,:idContinente,:descripcion,:descripcion2,:aliasa2,:aliasa3,:iconocss,:codeFono, :mone,:idioma, :usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idPais,
            ':idContinente' => $this->_idContinente,
            ':descripcion' => $this->_descripcion, 
            ':descripcion2' => $this->_descripcion2, 
            ':aliasa2' => $this->_aliasa2, 
            ':aliasa3' => $this->_aliasa3, 
            ':iconocss' => $this->_icono, 
            ':codeFono' => $this->_codeFono,
            ':mone' => $this->_idMoneda,
            ':idioma' => $this->_idIdioma,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Pais*/
    public function findPais(){
       $query = "
           SELECT
            `id_pais`,
            `id_continente`,
            `descripcion`,
            `alias_isoa2`,
            `alias_isoa3`,
            descripcion_es,
            `css`,
            `estado`,
            `usuario_creacion`,
            `fecha_creacion`,
            codigo_telefonico,
            id_moneda,
            id_idioma
          FROM `ub_pais`
          WHERE id_pais = :id; ";
        
        $parms = array(
            ':id' => $this->_idPais
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: Pais*/
    public function editPais(){
        $query = "call sp_configPaisMantenimiento(:flag,:key,:idContinente,:descripcion,:descripcion2,:aliasa2,:aliasa3,:iconocss,:codeFono,:mone,:idioma,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idPais,
            ':idContinente' => $this->_idContinente,
            ':descripcion' => $this->_descripcion, 
            ':descripcion2' => $this->_descripcion2, 
            ':aliasa2' => $this->_aliasa2, 
            ':aliasa3' => $this->_aliasa3, 
            ':iconocss' => $this->_icono, 
            ':codeFono' => $this->_codeFono,
            ':mone' => $this->_idMoneda,
            ':idioma' => $this->_idIdioma,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar registro: Pais*/
    public function deletePais(){
        $query = "call sp_configPaisMantenimiento(:flag,:key,:idContinente,:descripcion,:descripcion2,:aliasa2,:aliasa3,:iconocss,:codeFono,:mone,:idioma,:usuario);";       
        $parms = array(
               ':flag' => 3,
                ':key' => $this->_idPais,
                ':idContinente' => '',
                ':descripcion' => '', 
                ':descripcion2' => '', 
                ':aliasa2' => '', 
                ':aliasa3' => '', 
                ':iconocss' => '',
                ':codeFono' => '',
                ':mone' => '',
                ':idioma' => '',
                ':usuario' => $this->_usuario
            );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `ub_pais` SET
                    `estado` = 'I'
                WHERE `id_pais` = :id;";
        $parms = array(
            ':id' => $this->_idPais
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `ub_pais` SET
                    `estado` = 'A'
                WHERE `id_pais` = :id;";
        $parms = array(
            ':id' => $this->_idPais
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }    
}

?>
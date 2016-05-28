<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 00:12:57 
* Descripcion : ubigeoModel.php
* ---------------------------------------
*/ 

class ubigeoModel extends Model{

    private $_flag;
    private $_idUbigeo;
    private $_usuario, $_idPersona;
        
    private $_idDepartamento, $_idDepartamentoCombo;
    private $_idPais;
    private $_codigoPostal;
        
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
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_idPais = Aes::de(Formulario::getParam('_idPais'));  /*se decifra*/
        $this->_idDepartamento = Aes::de(Formulario::getParam('_idDepartamento'));        
        $this->_idUbigeo   = Aes::de(Formulario::getParam("_idUbigeo")); 
        $this->_idPersona     = Session::get("sys_idPersona");
        
        $this->_idDepartamentoCombo =  Aes::de(Formulario::getParam(UBIG."lst_departamento"));    /*se decifra*/
        $this->_codigoPostal = Formulario::getParam(UBIG."txt_codigoPostal");        
        $this->_descripcion     = Formulario::getParam(UBIG.'txt_descripcion'); 
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
             
    }
    
    /*data para el grid: pais*/
    public function getGridPais(){
        $aColumns       =   array("","","descripcion","id_continente" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_configPaisGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*data para el grid: Departamento*/
    public function getGridDepartamento(){
        $aColumns       =   array("","id_departamento","departamento" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_confiDepartamentoGrid(:idPais,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ':idPais' => $this->_idPais,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }        
    
    /*data para el grid: Ubigeo*/
    public function getGridUbigeo(){
        $aColumns       =   array("","id_ubigeo","distrito","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_confiUbigeoGrid(:idDep,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ':idDep' => $this->_idDepartamento,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
   /*Listado de Departamento => Combo*/
    public function listarDepartamento(){
        $query = "SELECT
                `id_departamento` as id,
                `departamento` as descripcion
              FROM `ub_departamento`
              WHERE estado <> :estado and id_pais = left(LPAD(:idd,5,0),3)  ; ";
        
        $parms = array(
            ':estado' => '0',
            ':idd' => $this->_idDepartamento
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
     /*grabar nuevo registro: Ubigeo*/
    public function newUbigeo(){
        $query = "call sp_configUbigeoMantenimiento(:flag,:key,:idDep,:descripcion,:code,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idUbigeo,
            ':idDep' => $this->_idDepartamento,
            ':descripcion' => $this->_descripcion, 
            ':code' => $this->_codigoPostal,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Ubigeo*/
    public function findUbigeo(){
       $query = "
         select
            `id_ubigeo`,
            `distrito`,
            `id_departamento`,
            `codigo_postal`,
            `estado`,
            `usuario_creacion`,
            `fecha_creacion`
          from `ub_ubigeo`
          WHERE id_ubigeo = :id; ";
        
        $parms = array(
            ':id' => $this->_idUbigeo
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: Ubigeo*/
    public function editUbigeo(){
        $query = "call sp_configUbigeoMantenimiento(:flag,:key,:idDep,:descripcion,:code,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idUbigeo,
            ':idDep' => $this->_idDepartamentoCombo,
            ':descripcion' => $this->_descripcion,   
            ':code' => $this->_codigoPostal,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar registro: Ubigeo*/
    public function deleteUbigeo(){
        $query = "call sp_configUbigeoMantenimiento(:flag,:key,:idDep,:descripcion,:code,:usuario);";
        $parms = array(
               ':flag' => 3,
                ':key' => $this->_idUbigeo,
                ':idDep' => '',
                ':descripcion' => '',  
                ':code' => '',
                ':usuario' => $this->_usuario
            );
        $data = $this->queryOne($query,$parms);
        return $data;
    }    
    
    public function postDesactivar(){
        $query = "UPDATE `ub_ubigeo` SET
                    `estado` = 'I'
                WHERE `id_ubigeo` = :id;";
        $parms = array(
            ':id' => $this->_idUbigeo
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `ub_ubigeo` SET
                    `estado` = 'A'
                WHERE `id_ubigeo` = :id;";
        $parms = array(
            ':id' => $this->_idUbigeo
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }        
           
    /*Para combos en todo el Sistema: */
    public function getPais(){        
        $query = 'call sp_configUbigeoConsulta(:flag, :idPer, :idP, :idD, :idU);';                                     
        $parms = array(
            ':flag' => 1, 
            ':idPer' => $this->_idPersona, 
            ':idP' => '',
            ':idD' => '',
            ':idU' => ''
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
    public function getDepartamentos($pais=''){
        $query = 'call sp_configUbigeoConsulta(:flag, :idPer, :idP, :idD, :idU);';                                   
        $parms = array(             
            ':flag' => 2, 
            ':idPer' => $this->_idPersona, 
            ':idP' => str_pad(($pais == '')?$this->_idPais:$pais, 3, "0", STR_PAD_LEFT) ,
            ':idD' => '',
            ':idU' => ''
        );
        $data = $this->queryAll($query,$parms);  

        return $data;
    }             

    public function getUbigeo($pro=''){
        $query = 'call sp_configUbigeoConsulta(:flag, :idPer, :idP, :idD, :idU);';                                   
        $parms = array(             
            ':flag' => 3, 
            ':idPer' => $this->_idPersona, 
            ':idP' => '',
            ':idD' => str_pad(($pro == '')?$this->_idDepartamento:$pro, 5, "0", STR_PAD_LEFT) , 
            ':idU' => ''
        );
        
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getCiudad(){
        /*Combo para reportes y consultas */
        $query = 'call sp_configUbigeoConsulta(:flag, :idPer, :idP, :idD, :idU);';                                   
        $parms = array(             
            ':flag' => 4, 
            ':idPer' => $this->_idPersona, 
            ':idP' => '',
            ':idD' => '',
            ':idU' => ''
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
        
    /* Fin Combos del Sistema */
        
}

?>
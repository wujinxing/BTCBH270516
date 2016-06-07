<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 02-06-2016 19:06:04 
* Descripcion : mCatalogoGeneralModel.php
* Alias: CATGR
* ---------------------------------------
*/ 

class mCatalogoGeneralModel extends Model{

    private $_flag;
    private $_idMCatalogoGeneral;    
    private $_usuario;   
    private $_nombre, $_concentracion;
    private $_fraccion, $_codigoBarra, $_recetaMedica;
    private $_idPresentacion, $_idLaboratorio;
    private $_idClasificacion, $_idFamilia, $_idGenerico;
    
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
        $this->_idMCatalogoGeneral   = Aes::de(Formulario::getParam("_idMCatalogoGeneral"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_nombre = Formulario::getParam(CATGR.'txt_nombre');
        $this->_concentracion = Formulario::getParam(CATGR.'txt_concentracion');
        $this->_fraccion = Formulario::getParam(CATGR.'txt_fraccion'); 
        $this->_codigoBarra = Formulario::getParam(CATGR.'txt_codigoBarras'); 
        $this->_recetaMedica = (Formulario::getParam(CATGR."chk_receta")==''?'N':'S');
        $this->_idPresentacion = Formulario::getParam(CATGR."lst_presentacion");
        $this->_idLaboratorio = Formulario::getParam(CATGR."lst_laboratorio");
        $this->_idClasificacion= Formulario::getParam(CATGR."lst_clasificacion");
        $this->_idFamilia= Formulario::getParam(CATGR."lst_familia");
        $this->_idGenerico= Formulario::getParam(CATGR."lst_generico");
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MCatalogoGeneral*/
    public function getMCatalogoGeneral(){
        $aColumns       =   array("id_catalogo","nombre","concentracion","presentacion","laboratorio","fraccion","receta_medica" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_compraCatalogoGralGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MCatalogoGeneral*/
    public function newMCatalogoGeneral(){
        $query = "call sp_compraCatalogoGralMantenimiento(:flag,:key,:nom,:conc,:frac,:cbarra,:receta,:idPre,:idLab,:idCla,:idFam,:idGen,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':nom' => $this->_nombre,
            ':conc' => $this->_concentracion, 
            ':frac' => $this->_fraccion, 
            ':cbarra' => $this->_codigoBarra, 
            ':receta' => $this->_recetaMedica, 
            ':idPre' => $this->_idPresentacion, 
            ':idLab' => $this->_idLaboratorio, 
            ':idCla' => $this->_idClasificacion, 
            ':idFam' => $this->_idFamilia, 
            ':idGen' => $this->_idGenerico, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MCatalogoGeneral*/
    public function findMCatalogoGeneral(){
        $query = "SELECT
            `id_catalogo`,`id_laboratorio`,`nombre`,`concentracion`,`fraccion`,`id_presentacion`,`id_clasificacion`,
            `id_familia`,`id_generico`,`codigo_barras`,`receta_medica`
          FROM `lgk_catalogogral` WHERE `id_catalogo` =  :id; ";
        
        $parms = array(
            ':id' => $this->_idMCatalogoGeneral
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MCatalogoGeneral*/
    public function editMCatalogoGeneral(){
        $query = "call sp_compraCatalogoGralMantenimiento(:flag,:key,:nom,:conc,:frac,:cbarra,:receta,:idPre,:idLab,:idCla,:idFam,:idGen,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMCatalogoGeneral,
            ':nom' => $this->_nombre,
            ':conc' => $this->_concentracion, 
            ':frac' => $this->_fraccion, 
            ':cbarra' => $this->_codigoBarra, 
            ':receta' => $this->_recetaMedica, 
            ':idPre' => $this->_idPresentacion, 
            ':idLab' => $this->_idLaboratorio, 
            ':idCla' => $this->_idClasificacion, 
            ':idFam' => $this->_idFamilia, 
            ':idGen' => $this->_idGenerico, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MCatalogoGeneral*/
    public function deleteMCatalogoGeneral(){
       $query = "call sp_compraCatalogoGralMantenimiento(:flag,:key,:nom,:conc,:frac,:cbarra,:receta,:idPre,:idLab,:idCla,:idFam,:idGen,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idMCatalogoGeneral,
            ':nom' => '',
            ':conc' => '',
            ':frac' => '',
            ':cbarra' => '',
            ':receta' => '',
            ':idPre' => '',
            ':idLab' => '',
            ':idCla' => '',
            ':idFam' => '',
            ':idGen' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
}

?>
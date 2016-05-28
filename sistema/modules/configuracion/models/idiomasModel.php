<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 23-12-2014 14:12:17 
* Descripcion : idiomasModel.php
* ---------------------------------------
*/ 

class idiomasModel extends Model{

    private $_flag;
    private $_idIdiomas;
    private $_nombre, $_nombreV;
    private $_abreviatura, $_lang, $_icono;
    private $_usuario;
    private $_chkdel;  
    public $_sigla;
    
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
        $this->_idIdiomas   = Aes::de(Formulario::getParam("_idIdiomas"));    /*se decifra*/
        $this->_sigla = Aes::de(Formulario::getParam("_sigla"));         
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_nombre     = Formulario::getParam(IDIOM.'txt_nombre'); 
        $this->_nombreV     = Formulario::getParam(IDIOM.'txt_nombreV'); 
        $this->_abreviatura  = Formulario::getParam(IDIOM.'txt_abreviatura');
        $this->_lang = Formulario::getParam(IDIOM.'txt_lang');
        $this->_icono = Formulario::getParam(IDIOM.'txt_icono');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Idiomas*/
    public function getIdiomas(){
        $aColumns       =   array("","nombre","sigla","","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_configIdiomaGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: Idiomas*/
    public function newIdiomas(){
        $query = "call sp_configIdiomaMantenimiento(:flag,:key,:nombre,:nombreV,:alias,:lang,:icon,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idIdiomas,
            ':nombre' => $this->_nombre, 
            ':nombreV' =>  $this->_nombreV, 
            ':alias' => $this->_abreviatura, 
            ':lang' => $this->_lang,
            ':icon' => $this->_icono,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Idiomas*/
    public function findIdiomas(){
       $query = "SELECT
            `id_idioma`,
            `nombre`, nombre_visible,
            `sigla`,
            `estado`,
            `usuario_creacion`,
            `fecha_creacion`, 
            directorio_lang, icono
          FROM `pub_idioma`
          WHERE id_idioma = :id; ";
        
        $parms = array(
            ':id' => $this->_idIdiomas
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: Idiomas*/
    public function editIdiomas(){
       $query = "call sp_configIdiomaMantenimiento(:flag,:key,:nombre,:nombreV,:alias,:lang,:icon,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idIdiomas,
            ':nombre' => $this->_nombre, 
            ':nombreV' =>  $this->_nombreV, 
            ':alias' => $this->_abreviatura, 
            ':lang' => $this->_lang,
            ':icon' => $this->_icono,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: Idiomas*/
    public function deleteIdiomasAll(){
        $query = "call sp_configIdiomaMantenimiento(:flag,:key,:nombre,:nombreV,:alias,:lang,:icon,:usuario);";
         foreach ($this->_chkdel as $value) {            
            $parms = array(
               ':flag' => 3,
                ':key' => Aes::de($value),
                ':nombre' => '', 
                ':nombreV' =>  '',
                ':alias' => '', 
                ':lang' => '',
                ':icon' => '',
                ':usuario' => $this->_usuario
            );
            $this->execute($query,$parms);
        }
        $data = array('result'=>1);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `pub_idioma` SET
                    `estado` = 'I'
                WHERE `id_idioma` = :id;";
        $parms = array(
            ':id' => $this->_idIdiomas
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `pub_idioma` SET
                    `estado` = 'A'
                WHERE `id_idioma` = :id;";
        $parms = array(
            ':id' => $this->_idIdiomas
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
    
      /*Listado para Combo*/
    public function listarIdiomas(){
        $query = "select
            `id_idioma` as id,
            `nombre` as descripcion
          from `pub_idioma` 
          WHERE estado = :estado 
          order by 2 ; ";
        
        $parms = array(
            ':estado' => 'A'
        );
        
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
}

?>
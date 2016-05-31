<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:34 
* Descripcion : mGenericosModel.php
* Alias: GENER
* ---------------------------------------
*/ 

class mGenericosModel extends Model{

    private $_flag;
    private $_idMGenericos;    
    private $_usuario;     
    private $_descripcion;
    
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
        $this->_idMGenericos   = Aes::de(Formulario::getParam("_idMGenericos"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_descripcion  = Formulario::getParam(GENER.'txt_descripcion');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MGenericos*/
    public function getMGenericos(){
        $aColumns       =   array("id_generico","descripcion","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_compraGenericoGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MGenericos*/
    public function newMGenericos(){
        $query = "call sp_compraGenericoMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MGenericos*/
    public function findMGenericos(){
       $query = "SELECT
            `id_generico`,
            `descripcion`  
          FROM  `lgk_generico`
          WHERE id_generico = :id; ";
        
        $parms = array(
            ':id' => $this->_idMGenericos
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MGenericos*/
    public function editMGenericos(){
        $query = "call sp_compraGenericoMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMGenericos,
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MGenericos*/
    public function deleteMGenericos(){
        $query = "call sp_compraGenericoMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idMGenericos,
            ':descripcion' => '', 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `lgk_generico` SET
                    `estado` = 'I'
                WHERE `id_generico` = :id;";
        $parms = array(
            ':id' => $this->_idMGenericos
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `lgk_generico` SET
                    `estado` = 'A'
                WHERE `id_generico` = :id;";
        $parms = array(
            ':id' => $this->_idMGenericos
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
    
}

?>
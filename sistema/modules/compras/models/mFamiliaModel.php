<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:31 
* Descripcion : mFamiliaModel.php
* Alias: FAM
* ---------------------------------------
*/ 

class mFamiliaModel extends Model{

    private $_flag;
    private $_idMFamilia;    
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
        $this->_idMFamilia   = Aes::de(Formulario::getParam("_idMFamilia"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_descripcion  = Formulario::getParam(FAM.'txt_descripcion');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MFamilia*/
    public function getMFamilia(){
        $aColumns       =   array("id_familia","descripcion","estado"  ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_compraFamiliaGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MFamilia*/
    public function newMFamilia(){
        $query = "call sp_compraFamiliaMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MFamilia*/
    public function findMFamilia(){
        $query = "SELECT
            `id_familia`,
            `descripcion`  
          FROM  `lgk_familia`
          WHERE id_familia = :id; ";
        
        $parms = array(
            ':id' => $this->_idMFamilia
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MFamilia*/
    public function editMFamilia(){
        $query = "call sp_compraFamiliaMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMFamilia,
            ':descripcion' => $this->_descripcion, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MFamilia*/
    public function deleteMFamilia(){
        $query = "call sp_compraFamiliaMantenimiento(:flag,:key,:descripcion,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idMFamilia,
            ':descripcion' => '', 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `lgk_familia` SET
                    `estado` = 'I'
                WHERE `id_familia` = :id;";
        $parms = array(
            ':id' => $this->_idMFamilia
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `lgk_familia` SET
                    `estado` = 'A'
                WHERE `id_familia` = :id;";
        $parms = array(
            ':id' => $this->_idMFamilia
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
    
}

?>
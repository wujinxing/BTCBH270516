<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-04-2016 16:04:52 
* Descripcion : metodoPagoModel.php
* Alias: MEPAG
* ---------------------------------------
*/ 

class metodoPagoModel extends Model{

    private $_flag;
    private $_idMetodoPago;  
    private $_descripcion, $_icono, $_sumaCaja;
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
        $this->_idMetodoPago   = Aes::de(Formulario::getParam("_idMetodoPago"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_descripcion     = Formulario::getParam(MEPAG.'txt_nombre'); 
        $this->_icono = Formulario::getParam(MEPAG.'txt_icono');
        $this->_sumaCaja = (Formulario::getParam(MEPAG."chk_caja") == NULL)? 'N':'S';
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: MetodoPago*/
    public function getMetodoPago(){
        $aColumns       =   array("id_metodopago","descripcion" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaMetodoPagoGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: MetodoPago*/
    public function newMetodoPago(){
        $query = "call sp_ventaMetodoPagoMantenimiento(:flag,:key,:des,:icon,:caja,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idMetodoPago,
            ':des' => $this->_descripcion, 
            ':icon' =>  $this->_icono, 
            ':caja' => $this->_sumaCaja, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: MetodoPago*/
    public function findMetodoPago(){
        $query = "SELECT
            `id_metodopago`,
            `descripcion`,
            `icono`,
            `estado`,  
            `sumar_caja`
          FROM `pub_metodopago`
          WHERE `id_metodopago` = :id; ";
        
        $parms = array(
            ':id' => $this->_idMetodoPago
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: MetodoPago*/
    public function editMetodoPago(){
        $query = "call sp_ventaMetodoPagoMantenimiento(:flag,:key,:des,:icon,:caja,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idMetodoPago,
            ':des' => $this->_descripcion, 
            ':icon' =>  $this->_icono, 
            ':caja' => $this->_sumaCaja, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: MetodoPago*/
    public function deleteMetodoPago(){
      $query = "call sp_ventaMetodoPagoMantenimiento(:flag,:key,:des,:icon,:caja,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idMetodoPago,
            ':des' => '', 
            ':icon' => '', 
            ':caja' => '', 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postDesactivar(){
        $query = "UPDATE `pub_metodopago` SET
                    `estado` = 'I'
                WHERE `id_metodopago` = :id;";
        $parms = array(
            ':id' => $this->_idMetodoPago
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `pub_metodopago` SET
                    `estado` = 'A'
                WHERE `id_metodopago` = :id;";
        $parms = array(
            ':id' => $this->_idMetodoPago
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    } 
    
    public function findMetodoPagoAll(){
        $query = "SELECT
            `id_metodopago` as id,
            `descripcion`
          FROM pub_metodopago
          WHERE `estado` = :est; ";
        
        $parms = array(
            ':est' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
}

?>
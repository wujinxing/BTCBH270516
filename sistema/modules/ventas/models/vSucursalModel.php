<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-04-2016 18:04:07 
* Descripcion : vSucursalModel.php
* ---------------------------------------
*/ 

class vSucursalModel extends Model{

    private $_flag;
    private $_idVSucursal;    
    private $_usuario;
    private $_nombre, $_sigla;    
    
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
        $this->_idVSucursal   = Aes::de(Formulario::getParam("_idVSucursal"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_nombre     = Formulario::getParam(SUCUR.'txt_nombre');
        $this->_sigla     = Formulario::getParam(SUCUR.'txt_sigla');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: VSucursal*/
    public function getVSucursal(){
        $aColumns       =   array("","nombre","sigla" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaSucursalGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: VSucursal*/
    public function newVSucursal(){
        $query = "call sp_ventaSucursalMantenimiento(:flag,:key,:nombre, :sigla, :usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idVSucursal,
            ':nombre' => $this->_nombre,
            ':sigla' => $this->_sigla,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: VSucursal*/
    public function findVSucursal(){
       $query = "SELECT
            `id_sucursal`,
            `nombre`, sigla,
            `estado`,
            `usuario_creacion`,
            `fecha_creacion`
          FROM `ven_sucursal`
          WHERE `id_sucursal` = :idd; ";
        
        $parms = array(
            ':idd' => $this->_idVSucursal
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: VSucursal*/
    public function editVSucursal(){
       $query = "call sp_ventaSucursalMantenimiento(:flag,:key,:nombre, :sigla, :usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idVSucursal,
            ':nombre' => $this->_nombre,
            ':sigla' => $this->_sigla,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: VSucursal*/
    public function deleteVSucursal(){
         $query = "call sp_ventaSucursalMantenimiento(:flag,:key,:nombre, :sigla, :usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idVSucursal,
            ':nombre' => '',
            ':sigla' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function getSucursalAll($permiso='N'){
       
        if($permiso == 'S'):
            $query = "SELECT
                    s.`id_sucursal` as id,
                    s.`nombre` as descripcion
                  FROM `ven_sucursal` s                        
                  WHERE `estado` = :est AND 
                  EXISTS (SELECT * FROM `ven_movimientos_caja` c WHERE c.`id_sucursal` = s.`id_sucursal` AND c.estado ='A' )   ; ";
        else:
            $query = "SELECT
                `id_sucursal` as id,
                `nombre` as descripcion
              FROM `ven_sucursal`
              WHERE `estado` = :est; ";
        endif;
        
        $parms = array(
            ':est' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getSucursalSiglaAll(){
        $query = "SELECT
                `id_sucursal` as id,
                `sigla`
              FROM `ven_sucursal`
              WHERE `estado` = :est; ";      
        
        $parms = array(
            ':est' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
    
    public function getSucursalSigla($idd){
        $query = "SELECT
                `id_sucursal` as id,
                `sigla`
              FROM `ven_sucursal`
              WHERE `id_sucursal` = :idd; ";      
        
        $parms = array(
            ':idd' => $idd
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }      

    public function postDesactivar(){
        $query = "UPDATE `ven_sucursal` SET
                    `estado` = 'I'
                WHERE `id_sucursal` = :idd;";
        $parms = array(
            ':idd' => $this->_idVSucursal
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `ven_sucursal` SET
                    `estado` = 'A'
                WHERE `id_sucursal` = :idd;";
        $parms = array(
            ':idd' => $this->_idVSucursal
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }       
}

?>
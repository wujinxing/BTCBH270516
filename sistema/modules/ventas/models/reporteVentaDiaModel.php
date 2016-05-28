<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 19-11-2014 22:11:59 
* Descripcion : reporteVentaDiaModel.php
* ---------------------------------------
*/ 

class reporteVentaDiaModel extends Model{

    private $_flag;
    public $_idCaja;
    public  $_fecha;
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
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_idCaja   = Aes::de(Formulario::getParam("_idCaja"));    /*se decifra*/        
        
         if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursal = '';
        }else{
            $this->_idSucursal = Session::get('sys_idSucursal');
        }                
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    public function getDataCajaAll(){
        if( $this->_idSucursal !== ''){
            $sucursal = 'AND c.`id_sucursal` = '.$this->_idSucursal;
        }        
        $query = "SELECT TRIM(c.id_caja) AS id_caja,
                        CONCAT(DATE_FORMAT(c.`fecha_caja`,'%d/%m/%Y'),' - ', 
                        (SELECT mo.`descripcion` FROM `pub_moneda` mo WHERE mo.id_moneda = c.id_moneda), ' - ',                        
                        c.id_caja ) AS descripcion,
                        (SELECT ss.`sigla` FROM `ven_sucursal` ss WHERE ss.`id_sucursal` = c.id_sucursal) AS sucursal
             FROM ven_movimientos_caja c
             WHERE c.`fecha_caja` > DATE(DATE_ADD(fnNow(), INTERVAL -4 DAY))  ".$sucursal." 
       ORDER BY 1 DESC; ";        
        $parms = array(            
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }   
    
    
    
}

?>
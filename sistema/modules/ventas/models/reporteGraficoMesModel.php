<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-11-2014 17:11:15 
* Descripcion : reporteGraficoMesModel.php
* ---------------------------------------
*/ 

class reporteGraficoMesModel extends Model{

    private $_flag;
    private $_idReporteGraficoMes;
    public $_periodo, $_mes;
    private $_idSucursalGrid;
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
        $this->_idReporteGraficoMes   = Aes::de(Formulario::getParam("_idReporteGraficoMes"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_periodo        = Formulario::getParam("_periodo");
        $this->_mes        = Formulario::getParam("_mes");
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursalGrid = Formulario::getParam("_idSucursalGrid");
        }else{
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
        }      
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    public function getGrafico(){
        $query = "
             SELECT fecha_caja,
             DATE_FORMAT(`fecha_caja`,'%d/%m/%Y')AS fecha,
             MONTH( m.`fecha_caja`) as mes,
            (SELECT v.`sigla` FROM `ven_sucursal` v WHERE v.`id_sucursal` = m.`id_sucursal` ) AS sucursal,
            (SELECT v.`nombre` FROM `ven_sucursal` v WHERE v.`id_sucursal` = m.`id_sucursal` ) AS sucursal_descripcion,
            (SELECT mm.sigla FROM pub_moneda mm WHERE mm.id_moneda = m.id_moneda) AS moneda,
            m.total_saldo AS monto        
         FROM `ven_movimientos_caja` m      
         WHERE YEAR( m.`fecha_caja`) = :periodo AND m.`estado` = 'C' AND m.`id_sucursal` = :idSucursal and
               MONTH( m.`fecha_caja`) = :mes 
         order by 1 asc; ";
        $parms = array(
            ':periodo' =>$this->_periodo,
            ':idSucursal'=> $this->_idSucursalGrid,
            ':mes' => $this->_mes
        );    
        $data = $this->queryAll($query,$parms);    
        return $data;
    }
    
    public function getAnioAll(){
        $query = 'SELECT YEAR(m.`fecha_caja`) AS anio
            FROM `ven_movimientos_caja` m      
            WHERE m.`estado` = "C"
            GROUP BY 1';
        $parms = array();
        $data = $this->queryAll($query,$parms);                
        return $data;
    }    
}

?>
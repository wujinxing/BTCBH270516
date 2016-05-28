<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 04-05-2016 17:05:37 
* Descripcion : reporteGraficoIngresosMesModel.php
* Alias: VRPT6
* ---------------------------------------
*/ 

class reporteGraficoIngresosMesModel extends Model{

    private $_flag;
    private $_idReporteGraficoIngresosMes;    
    private $_usuario;     
    public $_periodo;
    private $_idSucursalGrid;
    
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
        $this->_idReporteGraficoIngresosMes   = Aes::de(Formulario::getParam("_idReporteGraficoIngresosMes"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_periodo        = Formulario::getParam("_periodo");
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
             SELECT MONTH(p.`fecha`) AS mes,
             (SELECT v.`sigla` FROM `ven_sucursal` v WHERE v.`id_sucursal` = d.`id_sucursal` ) AS sucursal,
             (SELECT v.`nombre` FROM `ven_sucursal` v WHERE v.`id_sucursal` = d.`id_sucursal` ) AS sucursal_descripcion,
             (select m.sigla from pub_moneda m where m.id_moneda = p.id_moneda) as moneda,
                SUM(p.`monto_pagado`) AS monto
            FROM `ven_pago` p      
               INNER JOIN ven_documento d ON d.`id_docventa` = p.`id_docventa`
            WHERE p.`id_metodopago` IN (1,2,3,4,5) AND p.saldo_pasado = 'N' AND YEAR( p.`fecha`) = :periodo AND d.estado in ('E','P') and 
                  p.`estado` = 'P' AND d.`id_sucursal` = :idSucursal
            GROUP BY 1,2,3,4
            ";
        $parms = array(
            ':periodo' =>$this->_periodo,
            ':idSucursal'=> $this->_idSucursalGrid          
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
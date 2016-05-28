<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-05-2016 17:05:16 
* Descripcion : reporteResumenClienteModel.php
* Alias: VRPT5
* ---------------------------------------
*/ 

class reporteResumenClienteModel extends Model{

    private $_flag;
    public $_idPersona;  
    public $_fecha1, $_fecha2, $_idSucursalGrid;
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
        $this->_idPersona   = Aes::de(Formulario::getParam("_idPersona"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_fecha1 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha1"));
        $this->_fecha2 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha2"));
        
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
    
    /*data para el grid: ReporteResumenCliente*/
    public function getReporteResumenCliente(){
        $aColumns       =   array("","id_persona","empresa ASC, cliente","ciudad","ventas" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaRptVentasClienteGrid(:idSucursal,:fecha1,:fecha2,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":idSucursal"=> $this->_idSucursalGrid,
            ":fecha1"=> $this->_fecha1,
            ":fecha2"=> $this->_fecha2,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
     public function getFindResumenUnitario(){

        $query = "
         SELECT  d.`id_docventa`,           
            d.`codigo_impresion`,
            d.fecha,
            d.`id_persona`,
            ub.`distrito` AS ciudad, 
            (SELECT pp.nombrecompleto FROM mae_persona pp WHERE pp.id_persona = d.id_persona) AS cliente,            
            (SELECT pp.empresacliente FROM mae_persona pp WHERE pp.id_persona = d.id_persona) AS empresa,
            (SELECT `sigla` FROM pub_moneda mo WHERE mo.id_moneda = d.`id_moneda`) AS moneda,
            d.`monto_importe`,  d.`monto_subtotal`, d.`monto_impuesto`,                                 
            d.`observacion`,d.estado,d.`monto_asignado`,d.`monto_saldo`,d.porcentaje_igv,
            d.porcentaje_igv,d.incl_igv, d.flag_impuesto, d.monto_total_final,
            (SELECT v.nombre FROM ven_sucursal v WHERE v.id_sucursal = d.id_sucursal) AS sucursal
	FROM `ven_documento` d 
		INNER JOIN `mae_persona` p ON p.`id_persona` = d.`id_persona`
		INNER JOIN `ub_ubigeo` ub ON ub.`id_ubigeo` = p.`id_ubigeo`		
	WHERE d.`estado` IN ('E','P')  AND d.`fecha` BETWEEN :fecha1 AND :fecha2 
		AND d.`id_persona` = :idPersona	
        ORDER BY 3,1;";
        
        $parms = array(
            ':fecha1' =>  $this->_fecha1,
            ':fecha2' =>  $this->_fecha2,
            ':idPersona'=>  $this->_idPersona
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
    
    public function getFindPagosUnitario(){

        $query = "
            SELECT  p.`id_pago`,
                    p.`id_docventa`,  v.fecha as fecha_venta,
                    (SELECT m.`sigla` FROM `pub_moneda` m WHERE m.id_moneda = p.`id_moneda`) AS moneda,
                    p.`fecha`, p.estado,
                    p.`monto_pagado`, v.`monto_total`,
                    p.`id_metodopago`, 
                    (SELECT mp.`descripcion` FROM `pub_metodopago` mp WHERE mp.`id_metodopago` = p.`id_metodopago`) AS metodopago,
                    p.`numero_operacion`,
                    (select tp.`descripcion` from `pub_tipodoc` tp where tp.`id_tipodoc` = p.id_tipodoc) as tipo_doc,
                    p.serie, p.numero                   
	FROM `ven_documento` v 
		INNER JOIN `ven_pago` p on p.id_docventa = v.id_docventa
	WHERE v.`estado` IN ('E','P') AND p.estado <> '0' AND v.`fecha` BETWEEN :fecha1 AND :fecha2 AND	
	      v.`id_persona` = :idPersona	
        ORDER BY p.fecha, p.id_docventa; ";
        
        $parms = array(
            ':fecha1' =>  $this->_fecha1,
            ':fecha2' =>  $this->_fecha2,
            ':idPersona'=>  $this->_idPersona
        );
        $data = $this->queryAll($query,$parms);        
        return $data;
    }   
    
    
    public function getFindResumenGeneral(){
                                
        $query = "
         SELECT p.id_persona, p.`nombrecompleto` AS cliente, p.`direccion`, ub.`distrito` AS ciudad, 
            pr.`descripcion` AS producto, v.`fecha`, d.`cantidad`, 
            IF(pr.`tipo_producto`='R',d.`devolucion`,0) as devolucion, 
            d.`cantidad_saldo`, 
            d.`precio_dsto`, d.`importe`, d.id_docventa, v.`monto_saldo`,
            (SELECT mm.sigla FROM pub_moneda mm WHERE mm.id_moneda = v.id_moneda) AS moneda,
            (SELECT pp.nombrecompleto FROM mae_persona pp WHERE pp.id_persona = v.id_responsable) AS responsable,
            (SELECT tc.`descripcion` FROM `ven_tipocliente` tc WHERE tc.`id_tipocliente` = p.`id_tipocliente`) AS tipoCliente, 
            v.monto_asignado,
            (SELECT COUNT(dd.id_docventa) FROM `ven_documentod` dd 
		INNER JOIN ven_documento vv ON vv.id_docventa = dd.id_docventa
                WHERE vv.id_persona = p.id_persona AND vv.estado = 'E' AND vv.`fecha` BETWEEN :fecha1 AND :fecha2  
            ) AS tVenta
	FROM `ven_documentod` d 
		INNER JOIN `ven_documento` v ON v.`id_docventa` = d.`id_docventa`
		INNER JOIN `mae_persona` p ON p.`id_persona` = v.`id_persona`
		INNER JOIN `ub_ubigeo` ub ON ub.`id_ubigeo` = p.`id_ubigeo`
		INNER JOIN `ven_producto` pr ON pr.`id_producto` = d.`id_producto`
	WHERE v.`estado` = 'E' AND v.`fecha` BETWEEN :fecha11 AND :fecha22  
        ORDER BY 4,3,2,6;";
        
        $parms = array(  
            ':fecha1' =>  $this->_fecha1,
            ':fecha2' =>  $this->_fecha2,
            ':fecha11' =>  $this->_fecha1,
            ':fecha22' =>  $this->_fecha2
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
   
    
}

?>
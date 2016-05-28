<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-11-2014 22:11:42 
* Descripcion : vConsultaSaldoModel.php
* ---------------------------------------
*/ 

class vConsultaSaldoModel extends Model{

    private $_flag;
    public $_idVConsultaSaldo;
    private $_idSucursalGrid;
    private $_usuario;    
    public $_idPersona;
    
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
        $this->_idVConsultaSaldo   = Aes::de(Formulario::getParam("_idVConsultaSaldo"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_idPersona   = Aes::de(Formulario::getParam("_idPersona"));    /*se decifra*/        
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
    
    /*data para el grid: VConsultaSaldo*/
    public function getVConsultaSaldo(){
        $aColumns       =   array( 'id_persona','cliente','sucursal', 'monto_saldo');//para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaRptSaldoClienteGrid(:idSucursal,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":idSucursal"=> $this->_idSucursalGrid,          
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
        SELECT p.id_persona, p.`nombrecompleto` AS cliente, p.`empresacliente` AS empresa, ub.`distrito` AS ciudad, 
            v.`fecha`, d.`cantidad_real` AS cantidad, d.id_producto,
            (d.`precio_final` + (d.impuesto / d.`cantidad_real` )) as precio, 
            d.total_impuesto as importe, d.id_docventa, v.`monto_saldo`,
            (SELECT mm.sigla FROM pub_moneda mm WHERE mm.id_moneda = v.id_moneda) AS moneda,
            d.`descripcion`, pr.`descripcion` AS producto,
            (SELECT s.`sigla` FROM  `ven_sucursal` s WHERE s.`id_sucursal` = v.`id_sucursal` ) AS sucursal
        FROM `ven_documentod` d 
		INNER JOIN `ven_documento` v ON v.`id_docventa` = d.`id_docventa`
		INNER JOIN `mae_persona` p ON p.`id_persona` = v.`id_persona`
		INNER JOIN `ub_ubigeo` ub ON ub.`id_ubigeo` = p.`id_ubigeo`	
		INNER JOIN `ven_producto` pr ON pr.`id_producto` = d.`id_producto`
	WHERE v.`estado` = 'E' AND  v.`id_persona` = :idPersona AND v.id_sucursal = :idSuc AND v.`monto_saldo` > 0	
        ORDER BY 6, v.id_docventa;";
        
        $parms = array(          
            ':idPersona'=>  $this->_idPersona,
            ':idSuc' => $this->_idSucursalGrid
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
    
    public function getFindResumenGeneral(){
                
        $idUbigeo = "";
        if ($this->_idCiudadGrid !== 'ALL'){
            $idUbigeo = ' AND p.id_ubigeo = "'.$this->_idCiudadGrid.'"';
        }
        
        $query = "
          SELECT p.id_persona, p.`nombrecompleto` AS cliente, p.`empresacliente` AS empresa, ub.`distrito` AS ciudad, 
            v.`fecha`, d.`cantidad_real` AS cantidad,
           (d.`precio_final` + (d.impuesto / d.`cantidad_real` )) as precio, 
            d.total_impuesto as importe, d.id_docventa, v.`monto_saldo`,
            (SELECT mm.sigla FROM pub_moneda mm WHERE mm.id_moneda = v.id_moneda) AS moneda,
            d.`descripcion`, pr.`descripcion` AS producto, d.id_producto,
            (SELECT concat(s.`nombre`,' / ', s.sigla) FROM  `ven_sucursal` s WHERE s.`id_sucursal` = v.`id_sucursal` ) AS sucursal,
            (SELECT COUNT(dd.id_docventa) FROM `ven_documentod` dd 
		INNER JOIN ven_documento vv ON vv.id_docventa = dd.id_docventa
                WHERE vv.id_persona = p.id_persona AND vv.estado = 'E' AND vv.`monto_saldo` > 0
            ) AS tVenta
        FROM `ven_documentod` d 
		INNER JOIN `ven_documento` v ON v.`id_docventa` = d.`id_docventa`
		INNER JOIN `mae_persona` p ON p.`id_persona` = v.`id_persona`
		INNER JOIN `ub_ubigeo` ub ON ub.`id_ubigeo` = p.`id_ubigeo`	
		INNER JOIN `ven_producto` pr ON pr.`id_producto` = d.`id_producto`
	WHERE v.`estado` = 'E' AND v.`monto_saldo` > 0 AND v.id_sucursal = :idSuc 	      
        ORDER BY 4,2,5;";
        
        $parms = array(           
            ':idSuc' => $this->_idSucursalGrid
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
}

?>
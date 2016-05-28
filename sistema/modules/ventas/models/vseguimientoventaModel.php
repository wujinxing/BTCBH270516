<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-11-2014 17:11:41 
* Descripcion : vseguimientoventaModel.php
* ---------------------------------------
*/ 

class vseguimientoventaModel extends Model{

    private $_flag;
    private $_idVenta, $_idCaja, $_idSucursalGrid;
    private $_idPago;
    private $_montoAsignado;
    private $_fecha;
    private $_usuario;
    private $_tipoDoc, $_serie, $_numero;
    private $_idMetodo, $_numOpe;
    
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
        $this->_idVenta   = Aes::de(Formulario::getParam("_idVenta"));    /*se decifra*/
        $this->_idPago   = Aes::de(Formulario::getParam("_idPago"));    /*se decifra*/
        
        $this->_usuario     = Session::get("sys_idUsuario");        
        $this->_idCaja   = Aes::de(Formulario::getParam(VSEVE.'lst_caja')); 
        
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursalGrid = Formulario::getParam("_idSucursalGrid");
        }else{
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
        }            
        
        $this->_montoAsignado     = Functions::deleteComa(Formulario::getParam(VSEVE."txt_pago"));
        $this->_fecha     = Functions::cambiaf_a_mysql(Formulario::getParam(VSEVE."txt_fecha"));                
        $this->_serie     = Formulario::getParam(VSEVE."txt_serie");
        $this->_numero     = Formulario::getParam(VSEVE."txt_numero");
        $this->_tipoDoc     = Formulario::getParam(VSEVE."lst_tipoDoc");
        $this->_idMetodo     = Aes::de(Formulario::getParam(VSEVE."lst_metodoPago"));
        $this->_numOpe     = Formulario::getParam(VSEVE."txt_operacion");
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Vseguimientoventa*/
    public function getVseguimientoventa(){
        $aColumns       =   array('id_docventa','cliente','sucursal','fecha','monto_total', 'monto_saldo','estado' ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaSeguimientoVentaGrid(:idSucursal,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
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
       
    public function getPagoVenta(){
        $aColumns       =   array("","fecha_creacion","monto_pagado",'estado' ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaPagoGrid(:idVenta,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ':idVenta' =>  $this->_idVenta,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }              
    
    /*editar registro: Vseguimientoventa*/
    public function newPagoVenta(){        
        $query = "call sp_ventaPagoMantenimiento(:flag,:key,:idCaja, :pago, :td, :ser, :num,:idMetodo, :numOpe, :est,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idVenta,
            ':idCaja' => $this->_idCaja,
            ':pago' => $this->_montoAsignado,
            ':td' => $this->_tipoDoc,       
            ':ser' => $this->_serie,       
            ':num' => $this->_numero,     
            ':idMetodo' =>$this->_idMetodo,
            ':numOpe' =>$this->_numOpe,
            ':est' => 'P',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function findVenta(){
       $query = "SELECT
            v.`id_docventa`,
            v.`id_persona`,
            v.`fecha`,
            v.`monto_importe`,
            v.`monto_asignado`,
            v.`monto_saldo`,
            v.`incl_igv`,
            v.`porcentaje_igv`,
            v.`monto_subtotal`,
            v.`monto_impuesto`,
            v.`monto_total`,  
            v.`estado`,
            (SELECT mo.`sigla` FROM `pub_moneda` mo WHERE mo.id_moneda = v.id_moneda) AS moneda,
            v.id_sucursal
          FROM `ven_documento` v
          WHERE v.`id_docventa` =  :idd; ";
        
        $parms = array(
            ':idd' => $this->_idVenta
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function anularPago (){
        $query = "call sp_ventaAnulacion( :flag, :idd, :usuario);";
        $parms = array(
            ':flag' => 3,
            ':idd' => $this->_idPago,
            ':usuario'=> $this->_usuario
        );        
        $data = $this->queryOne($query,$parms);
        return $data;        
    }    
    
    public function findVentaPago(){
       $query = "SELECT
                `id_pago`,  
                `id_moneda`,
                `id_tipodoc`,`serie`,`numero`,
                `fecha`,
                `monto_pagado`,
                `estado`,
                `id_metodopago`,
                `numero_operacion`
              FROM `ven_pago`
              WHERE `id_pago` = :idd; ";
        
        $parms = array(
            ':idd' => $this->_idPago
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
   
}

?>
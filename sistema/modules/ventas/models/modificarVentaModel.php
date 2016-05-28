<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 10-05-2016 19:05:57 
* Descripcion : modificarVentaModel.php
* Alias: VMOVE
* ---------------------------------------
*/ 

class modificarVentaModel extends Model{

    private $_flag;
    private $_idVenta, $_idSucursalGrid;    
    private $_usuario;      
    
    public $_idSucursal;        
    private $_idCaja;
    private $_idProducto;
    private $_montoTotal, $_montoTotalFinal;
    private $_montoAsignado;
    private $_observacion;
    private $_codImpr;
    private $_idPersona;
    private $_tipoDoc, $_incigv;
    private $_serie, $_numero, $_liberarImpuesto;
    private $_idMetodo, $_numOpe;
    
    private $_cantidad1;
    private $_cantidad2;
    private $_precio;
    
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
        $this->_usuario     = Session::get("sys_idUsuario");
                
        $this->_idCaja   = Aes::de(Formulario::getParam(VGEVE.'lst_caja')); 
        $this->_montoTotal     = Functions::deleteComa(Formulario::getParam(VGEVE."txt_total"));
        $this->_montoTotalFinal     = Functions::deleteComa(Formulario::getParam(VGEVE."txt_totalF"));
        
        $this->_observacion     = Formulario::getParam(VGEVE."txt_obs");
        $this->_incigv         = Formulario::getParam(VGEVE.'lst_igv'); 
        $this->_liberarImpuesto  = (Formulario::getParam(GNOSE."chk_impuesto")==''?'0':'1'); 
        $this->_codImpr     = Formulario::getParam(VGEVE."txt_codImpr");
        $this->_idPersona     = Aes::de(Formulario::getParam(VGEVE."txt_idpersona"));
        $this->_tipoDoc     = Formulario::getParam(VGEVE."lst_tipoDoc");
        
        $this->_idMetodo     = Aes::de(Formulario::getParam(VGEVE."lst_metodoPago"));
        $this->_numOpe     = Formulario::getParam(VGEVE."txt_operacion");
        
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursal = Formulario::getParam(VGEVE."lst_sucursal");
            $this->_idSucursalGrid = Formulario::getParam("_idSucursalGrid");
        }else{
            $this->_idSucursal = Session::get('sys_idSucursal');
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
        }            
                
        $this->_montoAsignado     = Functions::deleteComa(Formulario::getParam(VGEVE."txt_pago"));
        $this->_serie     = Formulario::getParam(VGEVE."txt_serie");
        $this->_numero     = Formulario::getParam(VGEVE."txt_numero");
        
        $this->_idProducto     = Formulario::getParam(VGEVE."hhddIdProducto"); #array
        $this->_cantidad1     = Formulario::getParam(VGEVE."txt_cantidad1");#array
        $this->_cantidad2     = Formulario::getParam(VGEVE."txt_cantidad2");#array        
        $this->_precio     = Formulario::getParam(VGEVE."txt_precio");#array
        $this->_descripcion     = Formulario::getParam(VGEVE."txt_descripcion");#array
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: ModificarVenta*/
    public function getModificarVenta(){
        $aColumns       =   array('id_docventa','cliente','sucursal','fecha','monto_total','monto_asignado','monto_saldo','estado' ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaModificarVentaGrid(:idSucursal,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
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
    
    public function modificarGenerarVenta(){
        $query = "CALL sp_ventaModificarVentaMantenimiento("
                . ":flag, :idVenta, :idVentaOld, :codigoImpr, :idCaja,"
                . ":idPersona, :montoTotal, :montoFinal, :tipoDoc, "
                . ":obs, :inclIgv, :imp, :serie,  "
                . ":num, :idProducto, :cant1, :cant2,  "
                . ":precio, :descripcion, :pago, :idMetodo, :numOpe, :usuario "                
            . "); ";
               
        $parms = array(
            ':flag'=> 1,
            ':idVenta'=> '',
            ':idVentaOld'=> $this->_idVenta,
            ':codigoImpr'=>$this->_codImpr,
            ':idCaja'=>$this->_idCaja,
            ':idPersona'=>$this->_idPersona,            
            ':montoTotal'=>$this->_montoTotal,
            ':montoFinal'=>$this->_montoTotalFinal,
            ':tipoDoc' => $this->_tipoDoc,
            ':obs' => $this->_observacion,
            ':inclIgv' => $this->_incigv,
            ':imp' => $this->_liberarImpuesto,
            ':serie' => $this->_serie,
            ':num' => $this->_numero,
            ':idProducto' => '',
            ':cant1' => '',                
            ':cant2' => '',
            ':precio' => '',
            ':descripcion' => '',
            ':pago'=>$this->_montoAsignado,
            ':idMetodo' =>$this->_idMetodo,
            ':numOpe' =>$this->_numOpe,
            ':usuario'=> $this->_usuario     
        );
        $data = $this->queryOne($query,$parms);
                
        $idVenta = $data['idVenta'];
        if($data['result'] !== 1)
            return $data;
        
        if($data['result'] == '1'){
            /*detalle de produccion*/
            foreach ($this->_idProducto as $key=>$idProducto) {
                $parmsx = array(
                    ':flag'=> 2,
                    ':idVenta'=>$idVenta,
                    ':idVentaOld'=>$this->_idVenta,
                    ':codigoImpr'=>'',
                    ':idCaja'=>'',
                    ':idPersona'=>'',
                    ':montoTotal'=>'',
                    ':montoFinal'=>'',
                    ':tipoDoc' => '',
                    ':obs' => '',
                    ':inclIgv' => $this->_incigv,
                    ':imp' => $this->_liberarImpuesto,
                    ':serie' => '',
                    ':num' => '',
                    ':idProducto' => AesCtr::de($idProducto),
                    ':cant1' => Functions::deleteComa($this->_cantidad1[$key]),                
                    ':cant2' => Functions::deleteComa($this->_cantidad2[$key]),
                    ':precio'=> Functions::deleteComa($this->_precio[$key]),
                    ':descripcion'=> $this->_descripcion[$key],
                    ':pago'=>'',
                    ':idMetodo' => '',
                    ':numOpe' => '',
                    ':usuario'=> $this->_usuario
                );
                $this->execute($query,$parmsx);
              
            }
        }
        $datax = array('result'=>1);
        return $datax;
    }   
    
    public function cancelarVenta(){
        $query = "CALL sp_ventaModificarVentaMantenimiento("
                . ":flag, :idVenta, :idVentaOld, :codigoImpr, :idCaja,"
                . ":idPersona, :montoTotal, :montoFinal, :tipoDoc, "
                . ":obs, :inclIgv, :imp, :serie,  "
                . ":num, :idProducto, :cant1, :cant2,  "
                . ":precio, :descripcion, :pago, :idMetodo, :numOpe, :usuario "                
            . "); ";
               
        $parms = array(
            ':flag'=> 3,
            ':idVenta'=> $this->_idVenta,
            ':idVentaOld'=> '',
            ':codigoImpr'=>'',
            ':idCaja'=>$this->_idCaja,
            ':idPersona'=>'', 
            ':montoTotal'=>'',
            ':montoFinal'=>'',
            ':tipoDoc' => '',
            ':obs' => '',
            ':inclIgv' => '',
            ':imp' => '',
            ':serie' => '',
            ':num' => '',
            ':idProducto' => '',
            ':cant1' => '',                
            ':cant2' => '',
            ':precio' => '',
            ':descripcion' => '',
            ':pago'=>'',
            ':idMetodo' =>'',
            ':numOpe' =>'',
            ':usuario'=> $this->_usuario     
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
}

?>
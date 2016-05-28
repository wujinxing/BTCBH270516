<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-09-2014 23:09:58 
* Descripcion : generarVentaModel.php
* ---------------------------------------
*/ 

class generarVentaModel extends Model{

    private $_flag;
    public $_idVenta;
    public $_idCotizacion, $_idSucursal;
    private $_idMoneda;
    private $_fecha1, $_fecha2, $_idSucursalGrid;
    private $_fecha, $_idCaja;
    private $_idProducto;
    private $_montoTotal, $_montoTotalFinal;
    private $_montoAsignado;
    private $_observacion;
    private $_codImpr;
    private $_idPersona;
    private $_moneda;
    private $_tipoDoc, $_incigv;
    private $_usuario;
    private $_serie, $_numero, $_liberarImpuesto;
    private $_idMetodo, $_numOpe;
    
    private $_cantidad1;
    private $_cantidad2;
    private $_precio;

    /*para el grid*/
    public $_iDisplayStart;
    private $_iDisplayLength;
    private $_iSortingCols;
    private $_sSearch;
    
    public function __construct() {
        parent::__construct();
        $this->_set();
    }
    
    private function _set(){
        $this->_flag                    = Formulario::getParam("_flag");
        $this->_idVenta   = Aes::de(Formulario::getParam("_idVenta"));    /*se decifra*/
        $this->_idCotizacion   = Aes::de(Formulario::getParam("_idCotizacion"));    /*se decifra*/
        $this->_idCaja   = Aes::de(Formulario::getParam(VGEVE.'lst_caja')); 
        $this->_usuario                 = Session::get("sys_idUsuario");
        
        $this->_fecha     = Functions::cambiaf_a_mysql(Formulario::getParam(VGEVE."txt_fecha"));
        $this->_montoTotal     = Functions::deleteComa(Formulario::getParam(VGEVE."txt_total"));
        $this->_montoTotalFinal     = Functions::deleteComa(Formulario::getParam(VGEVE."txt_totalF"));
        
        $this->_observacion     = Formulario::getParam(VGEVE."txt_obs");
        $this->_incigv         = Formulario::getParam(VGEVE.'lst_igv'); 
        $this->_liberarImpuesto  = (Formulario::getParam(GNOSE."chk_impuesto")==''?'0':'1'); 
        $this->_codImpr     = Formulario::getParam(VGEVE."txt_codImpr");
        $this->_idPersona     = Aes::de(Formulario::getParam(VGEVE."txt_idpersona"));
        $this->_moneda     = 1;
        $this->_tipoDoc     = Formulario::getParam(VGEVE."lst_tipoDoc");
        
        $this->_idMetodo     = Aes::de(Formulario::getParam(VGEVE."lst_metodoPago"));
        $this->_numOpe     = Formulario::getParam(VGEVE."txt_operacion");
        
        $this->_fecha1 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha1"));
        $this->_fecha2 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha2"));
        
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
        $this->_idMoneda   = Formulario::getParam("_idMoneda");
        
        $this->_iDisplayStart  =   Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength =   Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   =   Formulario::getParam("iSortingCols");
        $this->_sSearch        =   Formulario::getParam("sSearch");
    }
    
    public function getGridGenerarVenta(){
        $aColumns       =   array( 'id_docventa','cliente','sucursal','fecha','monto_total', 'tipo_ingreso DESC, monto_saldo','estado'); //para la ordenacion y pintado en html
        /*
	 * Ordenando, se verifica por que columna se ordenara
	 */
        $sOrder = "";
        for ( $i=0 ; $i<intval( $this->_iSortingCols ) ; $i++ ){
                if ( Formulario::getParam( "bSortable_".intval(Formulario::getParam("iSortCol_".$i)) ) == "true" ){
                        $sOrder .= " ".$aColumns[ intval( Formulario::getParam("iSortCol_".$i) ) ]." ".
                                (Formulario::getParam("sSortDir_".$i)==="asc" ? "asc" : 'desc') .",";
                }
        }
        $sOrder = substr_replace( $sOrder, "", -1 );
        $query = "call sp_ventaGenerarVentaGrid(:idSucursal, :fecha1,:fecha2,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":idSucursal"=> $this->_idSucursalGrid,
            ":fecha1"=> $this->_fecha1,
            ":fecha2"=> $this->_fecha2,
            ':iDisplayStart' => $this->_iDisplayStart,
            ':iDisplayLength' => $this->_iDisplayLength,
            ':sOrder' => $sOrder,
            ':sSearch' => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        
        return $data; 
       
    }
    
    public function postNewIngresoDirecto(){
         $query = "CALL sp_ventaGenerarVentaMantenimiento("
                . ":flag, :idVenta, :codigoImpr, :idCaja,"
                . ":idPersona, :montoTotal, :montoFinal, :tipoDoc, "
                . ":obs, :inclIgv, :imp, :serie,  "
                . ":num, :idProducto, :cant1, :cant2,  "
                . ":precio, :descripcion, :pago, :idMetodo, :numOpe, :usuario, "
                . ":idCotizacion"
            . "); ";
        $parms = array(
            ':flag'=> 3,
            ':idVenta'=> '',
            ':codigoImpr'=>'',
            ':idCaja'=>$this->_idCaja,
            ':idPersona'=>'', 
            ':montoTotal'=>'',
            ':montoFinal'=>$this->_montoTotalFinal,
            ':tipoDoc' => '',
            ':obs' => $this->_observacion,
            ':inclIgv' =>'',
            ':imp' => '',
            ':serie' => '',
            ':num' => '',
            ':idProducto' => '',
            ':cant1' => '',                
            ':cant2' => '',
            ':precio' => '',
            ':descripcion' => '',
            ':pago'=>'',
            ':idMetodo' => '',
            ':numOpe' => '',
            ':usuario'=> $this->_usuario,            
            ':idCotizacion'=> ''
        );        
        $data = $this->queryOne($query,$parms);
        return $data;
    }
        
    
    public function newGenerarVenta(){              
        $query = "CALL sp_ventaGenerarVentaMantenimiento("
                . ":flag, :idVenta, :codigoImpr, :idCaja,"
                . ":idPersona, :montoTotal, :montoFinal, :tipoDoc, "
                . ":obs, :inclIgv, :imp, :serie,  "
                . ":num, :idProducto, :cant1, :cant2,  "
                . ":precio, :descripcion, :pago, :idMetodo, :numOpe, :usuario, "
                . ":idCotizacion"
            . "); ";
               
        $parms = array(
            ':flag'=> 1,
            ':idVenta'=> $this->_idVenta,
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
            ':usuario'=> $this->_usuario,            
            ':idCotizacion'=> $this->_idCotizacion
        );
        $data = $this->queryOne($query,$parms);
                
        $idVenta = $data['idVenta'];
        if($data['result'] == 2)
            return $data;
        
        if($data['result'] == '1'){
            /*detalle de produccion*/
            foreach ($this->_idProducto as $key=>$idProducto) {
                $parmsx = array(
                    ':flag'=> 2,
                    ':idVenta'=>$idVenta,
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
                    ':usuario'=> $this->_usuario,
                    ':idCotizacion'=> $this->_idCotizacion
                );
                $this->execute($query,$parmsx);
              
            }
        }
        $datax = array('result'=>1);
        return $datax;
    }
      
    public function anularGenerarVenta(){
        $query = "call sp_ventaAnulacion(:flag,:idVenta,:usuario);";
        $parms = array(
            ':flag' =>'1',
            ':idVenta' => $this->_idVenta,
            ':usuario'=> $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }           
    
    public function getFindVenta(){
        $query = "
        SELECT
            d.`id_docventa`,  d.id_sucursal,         
            d.`codigo_impresion`,
            DATE_FORMAT(d.`fecha`,'%d/%m/%Y')AS fecha,
            d.`id_persona`,
            (select pp.nombrecompleto from mae_persona pp where pp.id_persona = d.id_persona) as cliente,            
            (select pp.empresacliente from mae_persona pp where pp.id_persona = d.id_persona) as empresa,
            (select `sigla` from pub_moneda mo where mo.id_moneda = d.`id_moneda`) as moneda,
            d.`monto_importe`, d.tipo_ingreso,            
            d.`observacion`,
            d.estado,
            d.`monto_asignado`,
            d.`monto_saldo`,
            d.porcentaje_igv,
            d.`id_moneda`,
            d.porcentaje_igv,
            d.incl_igv, d.flag_impuesto, d.monto_total_final            
          FROM `ven_documento` d
          WHERE d.`id_docventa` = :idVenta; ";
        
        $parms = array(
            ':idVenta'=>  $this->_idVenta
        );
        $data = $this->queryOne($query,$parms);      
        
        return $data;
    }    
    
    public function getFindVentaD(){
        $query = "
        SELECT
            dd.`cantidad_1`, dd.`cantidad_2`, dd.`cantidad_real`,
            dd.`id_detalle`, dd.`id_docventa`, dd.`id_producto`, dd.`importe`, dd.`precio`,
            p.`descripcion`, u.`sigla`, u.`cantidad_multiple`,
            dd.importe_afectado, dd.impuesto, dd.total_impuesto, dd.precio_final,
            dd.descripcion,           
            p.descripcion as producto
        FROM `ven_documentod` dd
                INNER JOIN `ven_producto` p ON p.`id_producto` = dd.`id_producto`
                INNER JOIN `ven_unidadmedida` u ON u.`id_unidadmedida` = p.`id_unidadmedida`
        WHERE dd.`id_docventa` =  :idVenta; ";
        
        $parms = array(
            ':idVenta'=>  $this->_idVenta
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
   
    public function getFindPagos(){
        $query = "SELECT
            p.`id_pago`,
            p.`id_docventa`,
            p.`id_moneda`,
            DATE_FORMAT(p.`fecha`,'%d/%m/%Y')AS fecha,            
            p.`monto_pagado`,
            p.`estado`,
            (SELECT `sigla` FROM pub_moneda mo WHERE mo.id_moneda = p.`id_moneda`) AS moneda,
            p.`id_metodopago`, 
            (SELECT mp.`descripcion` FROM `pub_metodopago` mp WHERE mp.`id_metodopago` = p.`id_metodopago`) AS metodopago,
            p.`numero_operacion`,
            (select tp.`descripcion` from `pub_tipodoc` tp where tp.`id_tipodoc` = p.id_tipodoc) as tipo_doc,
            p.serie, p.numero
          FROM `ven_pago` p
          WHERE `id_docventa` = :idVenta; ";
        
        $parms = array(
            ':idVenta'=>  $this->_idVenta
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }            
    
    //Como se trabaja con 2 documentos: Facturas o Boletas, por eso no se hizo mantenimiento de esta tabla.
    public function getTipoDocumento(){
        $query = "
        SELECT
            `id_tipodoc` as id,
            `descripcion`,
            `sigla`
          FROM `pub_tipodoc`
          WHERE estado = :estado; ";
        
        $parms = array(
            ':estado'=> 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }       
    
   // Generar Codigo de Impresión
    public function getGenerarCodigo(){
        $query = "
        SELECT
	CONCAT(LPAD(IF(MAX(RIGHT(`codigo_impresion`,6)) IS NULL = 1,1,(MAX(RIGHT(`codigo_impresion`,6)) + 1)),6,0)) AS cod
        FROM `ven_documento`
        WHERE SUBSTRING(`id_docventa`,1,4) = YEAR(CURDATE()); ";
        
        $parms = array();
        $data = $this->queryOne($query,$parms);      
        
        return $data;
    }          
    
}

?>
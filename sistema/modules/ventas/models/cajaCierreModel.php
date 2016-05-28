<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-12-2014 23:12:35 
* Descripcion : cajaCierreModel.php
* ---------------------------------------
*/ 

class cajaCierreModel extends Model{

    private $_flag;
    public $_idCajaCierre;
    private $_usuario;
    private $_fecha1;
    private $_fecha2;
    private $_idSucursalGrid;
    private $_idMetodoPago,$_montoMP, $_montoReal, $_montoDiferencia, $_observacion;
    private $_idDenominacion, $_cantMoneda;
    
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
        $this->_idCajaCierre   = Aes::de(Formulario::getParam("_idCajaCierre"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_fecha1 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha1"));
        $this->_fecha2 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha2"));
        
        $this->_idMetodoPago     = Formulario::getParam(CAJAC."hhddIdTipoMP"); #array
        $this->_montoMP     = Formulario::getParam(CAJAC."txt_importe");#array
        $this->_idDenominacion     = Formulario::getParam(CAJAC."hhddIdDenominacion");#array
        $this->_cantMoneda     = Formulario::getParam(CAJAC."txt_cantidad");#array
        $this->_montoReal     = Formulario::getParam(CAJAC."txt_totalCaja");
        $this->_montoDiferencia     = Formulario::getParam(CAJAC."txt_diferencia");
        $this->_observacion     = Formulario::getParam(CAJAC."txt_observacion");
        
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
    
    /*data para el grid: CajaCierre*/
    public function getCajaCierre(){
        $aColumns       =   array("id_caja","fecha_creacion","14","13","monto_inicial","total_ingresos","total_egresos","total_saldo","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaCajaCierreGrid(:idSucursal,:fecha1,:fecha2,:iDisplayStart,:iDisplayLength,:sOrder);";
        
        $parms = array(
            ":idSucursal"=> $this->_idSucursalGrid,
            ":fecha1"=> $this->_fecha1,
            ":fecha2"=> $this->_fecha2,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }

    /*seleccionar registro a editar: CajaApertura*/
    public function findCaja(){
         $query = "SELECT t.id_caja, t.id_moneda, t.moneda, t.sigla, t.sucursal, t.fecha_caja,
                t.monto_inicial, t.estado, t.sucursal_sigla, t.observacion,
                t.total_ventas, t.total_acuenta, t.total_ingresoDirecto, t.total_egresos, 
                t.saldo_ventas,
                ( t.total_acuenta + t.total_ingresoDirecto ) AS total_efectivo,
                ( t.monto_inicial + t.total_acuenta + t.total_ingresoDirecto ) AS total_ingresos,
                ( ( t.monto_inicial + t.total_acuenta + t.total_ingresoDirecto ) - t.total_egresos) AS saldo,
                t.fecha_cierre, t.cajero, t.total_otros
        FROM
        (SELECT c.id_caja, IF(c.estado = 'C', 'Cerrado','Abierto') AS estado, c.observacion, c.`fecha_cierre`,
        c.`monto_inicial`, c.id_moneda,
        (SELECT p.nombrecompleto FROM mae_persona p INNER JOIN mae_usuario u ON u.id_persona = p.id_persona WHERE u.id_usuario = c.usuario_cierre) AS cajero,
	(SELECT m.`descripcion` FROM `pub_moneda` m WHERE m.`id_moneda` = c.id_moneda) AS moneda,
	(SELECT m.`sigla` FROM `pub_moneda` m WHERE m.`id_moneda` = c.id_moneda) AS sigla,
	(SELECT v.nombre FROM ven_sucursal v WHERE v.id_sucursal = c.id_sucursal) AS sucursal,
        (SELECT v.sigla FROM ven_sucursal v WHERE v.id_sucursal = c.id_sucursal) AS sucursal_sigla,
	c.fecha_caja,                        
        IF((SELECT SUM(v.`monto_total_final`) FROM `ven_documento` v 
			INNER JOIN `ven_pago` p ON p.`id_docventa` = v.`id_docventa`
		WHERE v.estado IN ('E','P') AND p.estado = 'P'  AND v.`tipo_ingreso` = 'V' AND p.id_caja = c.`id_caja`) IS NULL,
		0, (
		SELECT SUM(v.`monto_total_final`) FROM `ven_documento` v 
			INNER JOIN `ven_pago` p ON p.`id_docventa` = v.`id_docventa`
		WHERE v.estado IN ('E','P') AND p.estado = 'P'  AND v.`tipo_ingreso` = 'V' AND p.id_caja = c.`id_caja`
		)
	 )AS total_ventas,
         IF((SELECT SUM(v.`monto_saldo`) FROM `ven_documento` v 
			INNER JOIN `ven_pago` p ON p.`id_docventa` = v.`id_docventa`
		WHERE v.estado IN ('E','P') AND p.estado = 'P'  AND v.`tipo_ingreso` = 'V' AND p.id_caja = c.`id_caja`) IS NULL,
		0, (
		SELECT SUM(v.`monto_saldo`) FROM `ven_documento` v 
			INNER JOIN `ven_pago` p ON p.`id_docventa` = v.`id_docventa`
		WHERE v.estado IN ('E','P') AND p.estado = 'P' AND v.`tipo_ingreso` = 'V' AND p.id_caja = c.`id_caja`
		)
	 )AS saldo_ventas,
	IF((SELECT SUM(p.`monto_pagado`) FROM `ven_pago` p INNER JOIN `pub_metodopago` mp ON mp.`id_metodopago` = p.`id_metodopago` WHERE mp.`sumar_caja` = 'S' AND p.estado = 'P' AND p.id_caja = c.`id_caja`) IS NULL, 
		0,(SELECT SUM(p.`monto_pagado`) FROM `ven_pago` p  INNER JOIN `pub_metodopago` mp ON mp.`id_metodopago` = p.`id_metodopago` WHERE mp.`sumar_caja` = 'S' AND p.estado = 'P' AND p.id_caja = c.`id_caja`))		
	 AS total_acuenta,	 
	 IF((SELECT SUM(p.`monto_pagado`) FROM `ven_pago` p INNER JOIN `pub_metodopago` mp ON mp.`id_metodopago` = p.`id_metodopago` WHERE mp.`sumar_caja` = 'N'  AND p.estado = 'P' AND p.id_caja = c.`id_caja`) IS NULL, 
		0,(SELECT SUM(p.`monto_pagado`) FROM `ven_pago` p  INNER JOIN `pub_metodopago` mp ON mp.`id_metodopago` = p.`id_metodopago` WHERE mp.`sumar_caja` = 'N'  AND p.estado = 'P' AND p.id_caja = c.`id_caja`))		
	 AS total_otros,		 
	IF((SELECT SUM(v.`monto_total_final`) FROM `ven_documento` v WHERE v.estado = 'E' AND v.`tipo_ingreso` = 'I' AND v.id_caja = c.`id_caja`) IS NULL,
		0, (SELECT SUM(v.`monto_total_final`) FROM `ven_documento` v WHERE v.estado = 'E' AND v.`tipo_ingreso` = 'I' AND v.id_caja = c.`id_caja`)) 
	AS total_ingresoDirecto,
	IF((SELECT SUM(e.`monto`) FROM `ven_egreso` e WHERE e.estado = 'E' AND e.id_caja = c.`id_caja` ) IS NULL, 
		0,(SELECT SUM(e.`monto`) FROM `ven_egreso` e WHERE e.estado = 'E' AND e.id_caja = c.`id_caja` )) AS total_egresos
        FROM `ven_movimientos_caja` c WHERE c.`id_caja` =  :idd) AS t";
        $parms = array(            
            ':idd' => $this->_idCajaCierre  
        );
        $data = $this->queryOne($query,$parms);
        
        return $data;
    }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    
    // Para Reporte
    public function getIngresosAll(){
        $query = "
        SELECT
            d.`id_docventa`,                      
            DATE_FORMAT(d.`fecha`,'%d/%m/%Y')AS fecha,
            d.`id_persona`,            
            d.observacion,
            d.`monto_total_final` as monto_importe
          FROM `ven_documento` d                 
          WHERE d.estado = 'E' AND d.`id_caja` = :idd and d.tipo_ingreso = 'I'
          ORDER BY d.id_docventa; ";
        
        $parms = array(
            ':idd'=>  $this->_idCajaCierre
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }    
    // Pagos de Caja del dia
    public function getPagosAll(){
        $query = "
        SELECT
            d.`id_docventa`,            
            d.`codigo_impresion`,
            DATE_FORMAT(d.`fecha`,'%d/%m/%Y')AS fecha,            
            (SELECT pp.nombrecompleto FROM mae_persona pp WHERE pp.id_persona = d.id_persona) AS cliente,
            (SELECT pp.empresacliente FROM mae_persona pp WHERE pp.id_persona = d.id_persona) AS empresa,            
            (SELECT `sigla` FROM pub_moneda mo WHERE mo.id_moneda = d.`id_moneda`) AS moneda,
             d.`monto_total_final` AS monto_importe,                              
             if(d.estado = 'M', 0.00, d.`monto_saldo`) as monto_saldo,            
            (SELECT  mp.`descripcion` FROM `pub_metodopago` mp WHERE mp.`id_metodopago` = p.`id_metodopago` ) AS metodopago,
            SUM(p.`monto_pagado`) AS monto_pagado
          FROM `ven_documento` d          
			INNER JOIN `ven_pago` p ON p.`id_docventa` = d.`id_docventa`
          WHERE d.estado IN ('E','P','M') AND p.estado = 'P' AND p.`id_caja` =  :idd AND d.tipo_ingreso = 'V'
          GROUP BY 1,2,3,4,5,6,7,8,9
          ORDER BY p.id_metodopago, d.codigo_impresion; ";
        
        $parms = array(
            ':idd'=>  $this->_idCajaCierre          
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }    
    // Para Reporte
    public function getEgresosAll(){
        $query = "
        SELECT
            e.`id_egreso`,
            e.`descripcion`,
            e.`fecha`,
            e.`monto`,
            (SELECT pm.sigla FROM pub_moneda pm WHERE pm.id_moneda = e.`id_moneda`) AS moneda,            
            e.`estado`, e.`fecha_creacion`,
            (select p.`nombrecompleto` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as empleado,
            (select p.`empresacliente` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as proveedor,
            (select p.`proveedor` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as tproveedor,
            (select p.`empleado` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as templeado
          FROM ven_egreso e
          WHERE e.estado = 'E' AND e.`id_caja` =  :idd
          ORDER BY e.`fecha_creacion` ; ";
        
        $parms = array(
            ':idd'=>   $this->_idCajaCierre             
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }          
    // Para Reporte
    public function findResumenAll(){                
        $query = "SELECT mp.`descripcion` AS metodopago, mc.`monto_manual`,
                    (SELECT m.`sigla` FROM pub_moneda m WHERE m.id_moneda = c.`id_moneda`) AS moneda
            FROM `ven_movimientos_cierre` mc
                    INNER JOIN `pub_metodopago` mp ON mp.`id_metodopago` = mc.`id_metodopago`
                    INNER JOIN `ven_movimientos_caja` c ON c.id_caja = mc.`id_caja`
            WHERE mc.`id_caja` = :idd
            ORDER BY mp.id_metodopago ";        
        $parms = array(
            ':idd' => $this->_idCajaCierre,
        );
        $data = $this->queryAll($query,$parms);      
        return $data;
    }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     //Mostrar denominaciones de monedas y billetes 
    public function findDenominacionAll($idMoneda, $tipo){                
        $query = "SELECT
                `id_denominacion`,
                `tipo`,
                `id_moneda`,
                `cantidad`
              FROM `pub_denominacion`
              WHERE `id_moneda` = :idMoneda AND `tipo` = :idTipo
              ORDER BY 4 DESC; ";        
        $parms = array(
            ":idMoneda" => $idMoneda,
            ":idTipo" => $tipo
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }   
    
    //Mostrar Metodos de pago
    public function findMetodoPagoAll(){                
        $query = "SELECT
                        mp.`id_metodopago`,
                        mp.`descripcion`,
                        mp.`icono`,
                        (SELECT IF(SUM(p.`monto_pagado`) IS NULL, 0, SUM(p.`monto_pagado`))  FROM `ven_pago` p 
                        WHERE p.estado = 'P' AND p.id_metodopago <> 1 AND p.`id_metodopago` = mp.`id_metodopago` 
                        AND p.id_caja = :idd ) AS ingresos
            FROM `pub_metodopago` mp
            WHERE mp.estado = :est";        
        $parms = array(
            ":idd" => $this->_idCajaCierre,
            ":est" => 'A'            
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }   
    
    //Mostrar Metodos de pago - Ventas / pago
    public function findMetodoPagoVentasAll(){                
        $query = "SELECT
                        mp.`id_metodopago`,
                        mp.`descripcion`,
                        mp.`icono`,
                        (SELECT IF(SUM(p.`monto_pagado`) IS NULL, 0, SUM(p.`monto_pagado`))  FROM `ven_pago` p 
                        WHERE p.estado = 'P' AND p.`id_metodopago` = mp.`id_metodopago` 
                        AND p.id_caja = :idd ) AS ingresos
            FROM `pub_metodopago` mp
            WHERE mp.estado = :est";        
        $parms = array(
            ":idd" => $this->_idCajaCierre,
            ":est" => 'A'            
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }   
        
    /*editar registro: postGenerarCierre*/
    public function postGenerarCierre(){
                       
       $query = "CALL sp_ventaGenerarCierreCaja(:flag,:key, :idCaja, :montoReal, :dife, :obs, :idMP, :montoMP, :idDeno, :cantMon, :usuario ); ";
                 
       foreach ($this->_idMetodoPago as $key=>$idMP){
            $parms = array(
                 ':flag'=> 1,
                 ':key' => '',
                 ':idCaja'=> $this->_idCajaCierre,
                 ':montoReal'=>'',
                 ':dife'=>'',
                 ':obs'=>'', 
                 ':idMP'=>  AesCtr::de($idMP),
                 ':montoMP'=>Functions::deleteComa($this->_montoMP[$key]),
                 ':idDeno' => '',
                 ':cantMon' => '', 
                 ':usuario'=> $this->_usuario
             );        
            $data = $this->queryOne($query,$parms);    
            
            if($data['result'] !== 1)
                return $data;
            
            if($data['result'] == '1'){                
                //Si es efectivo:
                if(AesCtr::de($idMP) == 1){
                    $idCierre = $data['idCierre'];
                    foreach ($this->_idDenominacion as $key=>$idDen) {
                        $parms = array(
                            ':flag'=> 2,
                            ':key' => $idCierre,
                            ':idCaja'=> $this->_idCajaCierre,
                            ':montoReal'=> '',
                            ':dife'=>'',
                            ':obs'=>'',
                            ':idMP'=>  '',
                            ':montoMP'=> '',
                            ':idDeno' => AesCtr::de($idDen),
                            ':cantMon' => Functions::deleteComa($this->_cantMoneda[$key]),          
                            ':usuario'=> $this->_usuario
                         );  
                         $this->execute($query,$parms);
                    }
                }
            }
       }
       // Actualizar Caja:
       if($data['result'] == '1'){            
            $parms = array(
                 ':flag'=> 3,
                 ':key' => '',
                 ':idCaja'=> $this->_idCajaCierre,
                 ':montoReal'=>Functions::deleteComa($this->_montoReal),
                 ':dife'=>Functions::deleteComa($this->_montoDiferencia),
                 ':obs'=>$this->_observacion,               
                 ':idMP'=>  '',
                 ':montoMP'=>'',
                 ':idDeno' => '',
                 ':cantMon' => '',
                 ':usuario'=> $this->_usuario
             );   
            $datax = $this->queryOne($query,$parms);             
       }
       $data = array_merge($data, $datax);
       return $data;
    }

    
    
}

?>
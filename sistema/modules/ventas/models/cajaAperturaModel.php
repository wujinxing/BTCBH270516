<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-12-2014 21:12:27 
* Descripcion : cajaAperturaModel.php
* ---------------------------------------
*/ 

class cajaAperturaModel extends Model{

    private $_flag;
    private $_idCajaApertura;
    private $_fecha1;
    private $_fecha2;
    private $_fecha;
    private $_usuario, $_idSucursal, $_idMoneda, $_idSucursalCombo, $_idSucursalGrid;    
    private $_montoInicial;
    
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
        $this->_idCajaApertura   = Aes::de(Formulario::getParam("_idCajaApertura"));    /*se decifra*/
        $this->_idSucursalCombo =  Aes::de(Formulario::getParam("_idSucursalCombo"));
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_fecha1 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha1"));
        $this->_fecha2 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha2"));
        $this->_montoInicial    = Functions::deleteComa(Formulario::getParam(CAJAA.'txt_inicio'));   
                        
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursal = Aes::de(Formulario::getParam(CAJAA."lst_sucursal"));
            $this->_idSucursalGrid = Formulario::getParam("_idSucursalGrid");
            $this->_fecha = Functions::cambiaf_a_mysql(Formulario::getParam(CAJAA."txt_fecha"));
        }else{
            $this->_idSucursal = Session::get('sys_idSucursal');
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
            $this->_fecha = date('Y-m-d');
        }                
        
        $this->_idMoneda = 1; // Por defecto es en SOLES
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: CajaApertura*/
    public function getCajaApertura(){
        $aColumns       =   array("id_caja","fecha_creacion","sucursal","sigla_moneda","monto_inicial","total_ingresos","total_egresos","total_saldo","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaCajaAperturaGrid(:idSucursal, :fecha1,:fecha2,:iDisplayStart,:iDisplayLength,:sOrder);";
        
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
    
    public function postNewApertura(){
        $query = "CALL sp_ventaAperturarCaja(:flag, :key, :idMon, :idSuc, :inicial, :fecha, :usuario);";
        $parms = array(
            ':flag' => 1,          
            ':key' => '',
            ':idMon'=> $this->_idMoneda,
            ':idSuc'=> $this->_idSucursal,
            ':inicial'=> $this->_montoInicial,
            ':fecha'=> $this->_fecha,
            ':usuario' => $this->_usuario  
        );
        
        $data = $this->queryOne($query,$parms);
        
        return $data;
    }    
    
    /*seleccionar registro a editar: CajaApertura*/
    public function findCajaApertura(){
         $query = "SELECT c.id_caja,
                        c.`monto_inicial`, c.id_moneda,
                        (SELECT m.`descripcion` FROM `pub_moneda` m WHERE m.`id_moneda` = c.id_moneda) as moneda,
                        (select v.nombre from ven_sucursal v where v.id_sucursal = c.id_sucursal) as sucursal
            FROM `ven_movimientos_caja` c
            WHERE c.`id_caja` = :idd ";
        $parms = array(            
            ':idd' => $this->_idCajaApertura  
        );
        
        $data = $this->queryOne($query,$parms);
        
        return $data;
    }
    
    /*editar registro: CajaApertura*/
    public function editCajaApertura(){
        $query = "CALL sp_ventaAperturarCaja(:flag, :key, :idMon, :idSuc, :inicial, :fecha, :usuario);";
        $parms = array(
            ':flag' => 2,          
            ':key' => $this->_idCajaApertura,
            ':idMon'=> $this->_idMoneda,
            ':idSuc'=> $this->_idSucursal,
            ':inicial'=> $this->_montoInicial,
            ':fecha'=> $this->_fecha,
            ':usuario' => $this->_usuario  
        );
        
        $data = $this->queryOne($query,$parms);
        
        return $data;
    }
    
    //Validar caja -> si no existe caja, no se puede registrar: ingresos o egresos.
    public function getValidarCaja(){
                        
        $query = "SELECT COUNT(*) as existe
		FROM ven_movimientos_caja
		WHERE `estado` = 'A' AND
		      `id_sucursal` = :idSucursal; ";        
        $parms = array(
            ":idSucursal" => Session::get('sys_idSucursal')
        );
        $data = $this->queryOne($query,$parms);      
        
        return $data;
    }  

    //Mostrar toda las cajas que estan abiertas, mostrando su Moneda y la fecha, filtrar por sucursal. 
    public function getDataCajaAll($idSucursal){
        
        if($idSucursal == '') $idSucursal = $this->_idSucursalCombo;
        
        $query = "SELECT trim(c.id_caja) as id_caja,
                        CONCAT(DATE_FORMAT(c.`fecha_caja`,'%d/%m/%Y'),' - ', 
                        (SELECT mo.`descripcion` FROM `pub_moneda` mo WHERE mo.id_moneda = c.id_moneda), ' - ', 
                        (SELECT ss.`sigla` FROM `ven_sucursal` ss WHERE ss.`id_sucursal` = c.id_sucursal), ' - ',
                        c.id_caja ) AS descripcion                                                  
             FROM ven_movimientos_caja c
             WHERE c.`estado` = 'A' AND
		   c.`id_sucursal` = :idSucursal; ";        
        $parms = array(
            ":idSucursal" => $idSucursal
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
    }   
    
    // Retorna el ultimo saldo de la caja cerrada, para poder aperturar Caja con ese monto.
    public function getUltimoSaldo($idSucursal){
        
         if($idSucursal == '') $idSucursal = $this->_idSucursalCombo;
         
        $query = "SELECT ci.`monto_manual` AS saldo, mc.`fecha_caja`
          FROM ven_movimientos_caja mc
		INNER JOIN `ven_movimientos_cierre` ci ON mc.`id_caja` = ci.`id_caja`
                WHERE mc.`estado` = 'C' AND
		      ci.`id_metodopago` = 1 AND
                      mc.`id_sucursal` =   :idSucursal
                ORDER BY mc.`id_caja` DESC; ";        
        $parms = array(
            ":idSucursal" => $idSucursal
        );
        
        $data = $this->queryOne($query,$parms);      
        
        return $data;
    }      
    
}

?>
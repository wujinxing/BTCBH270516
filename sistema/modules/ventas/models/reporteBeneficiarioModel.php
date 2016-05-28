<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-05-2016 18:05:40 
* Descripcion : reporteBeneficiarioModel.php
* Alias: VRPT7
* ---------------------------------------
*/ 

class reporteBeneficiarioModel extends Model{

    private $_flag;    
    private $_usuario;       
    public $_idBeneficiario;  
    public $_fecha1, $_fecha2, $_idSucursalGrid;
    
    /*para el grid*/
    public  $_iDisplayStart;
    private $_iDisplayLength;
    private $_iSortingCols;
    
    public function __construct() {
        parent::__construct();
        $this->_set();
    }
    
    private function _set(){
        $this->_flag        = Formulario::getParam("_flag");
        $this->_idBeneficiario   = Aes::de(Formulario::getParam("_idBeneficiario"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_fecha1 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha1"));
        $this->_fecha2 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha2"));
        
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursalGrid = Aes::de(Formulario::getParam("_idSucursalGrid"));
        }else{
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
        } 
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");        
    }
    
    /*data para el grid: ReporteBeneficiario*/
    public function getReporteBeneficiario(){
        $aColumns       =   array("id_egreso","sucursal","descripcion","fecha","monto" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaRptBeneficiarioGrid(:idBeneficiario,:idSucursal,:fecha1,:fecha2,:iDisplayStart,:iDisplayLength,:sOrder);";
        
        $parms = array(
            ":idBeneficiario"=> $this->_idBeneficiario,
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
    
    public function getFindResumenUnitario(){
        
        if ($this->_idBeneficiario == '' ) $this->_idBeneficiario = '-1';
        $query = "
        select
            e.`id_egreso`,
            e.`descripcion`,
            e.`fecha`,
            e.`monto`,
            (select pm.sigla from pub_moneda pm where pm.id_moneda = e.`id_moneda`) as moneda,
            e.`estado`,
            (select vs.nombre from ven_sucursal vs where vs.id_sucursal = e.id_sucursal) as sucursal,
            (select p.`nombrecompleto` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as empleado,   
            (select p.`empresacliente` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as proveedor,
            (select u.distrito from `mae_persona` p inner join ub_ubigeo u on u.id_ubigeo = p.id_ubigeo
                where p.`id_persona` = e.`id_beneficiado`) as ciudad,
            (select p.`proveedor` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as tproveedor,
            (select p.`empleado` from `mae_persona` p where p.`id_persona` = e.`id_beneficiado`) as templeado            
        FROM ven_egreso e
	WHERE e.estado = 'E' AND e.`id_sucursal` = :idSucursal and e.`id_beneficiado` = :idBeneficiario 
              AND e.fecha between :fecha1 AND :fecha2
        order by 3; ";
        
        $parms = array(
            ":idBeneficiario"=> $this->_idBeneficiario,
            ":idSucursal"=> $this->_idSucursalGrid,
            ":fecha1"=> $this->_fecha1,
            ":fecha2"=> $this->_fecha2
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
    
}

?>
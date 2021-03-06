<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-11-2014 00:11:17 
* Descripcion : reporteVentaFechaModel.php
* ---------------------------------------
*/ 

class reporteVentaFechaModel extends Model{

    private $_flag;
    public $_idReporteVentaFecha;     
    private $_usuario;
    private $_f1;
    private $_f2;        
    private $_idSucursalGrid;
    
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
        $this->_idReporteVentaFecha   = Aes::de(Formulario::getParam("_idReporteVentaFecha"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_f1    = Functions::cambiaf_a_mysql(Formulario::getParam("_f1"));
        $this->_f2    = Functions::cambiaf_a_mysql(Formulario::getParam("_f2"));          
        
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursalGrid = Formulario::getParam("_idSucursalGrid");
        }else{
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
        }  
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
    }
    
    /*data para el grid: ReporteVentaFecha*/
    public function getReporteVentaFecha(){
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
        
        $query = "call sp_ventaRptVentaFechaGrid(:idSucursal, :f1,:f2,:iDisplayStart,:iDisplayLength,:sOrder);";
        
        $parms = array(
            ":idSucursal"=> $this->_idSucursalGrid,
            ":f1" => $this->_f1,
            ":f2" => $this->_f2,            
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }          
    
}

?>
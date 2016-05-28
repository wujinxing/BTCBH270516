<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 07-04-2016 17:04:08 
* Descripcion : reporteVendedorModel.php
* ---------------------------------------
*/ 

class reporteVendedorModel extends Model{

    private $_flag;
    public $_f1, $_f2;    
    public $_idVendedor;    
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
        $this->_idVendedor   = Aes::de(Formulario::getParam("_idVendedor"));    /*se decifra*/        
        $this->_f1    = Functions::cambiaf_a_mysql(Formulario::getParam("_f1"));
        $this->_f2    = Functions::cambiaf_a_mysql(Formulario::getParam("_f2"));
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: ReporteVendedor*/
    public function getReporteVendedor(){
        $aColumns       =   array("","id_persona","nombrecompleto","telefono","5" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaRptVendedorGrid(:f1, :f2, :iDisplayStart,:iDisplayLength,:sOrder);";
        
        $parms = array(
            ":f1" => $this->_f1,
            ":f2" => $this->_f2,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getListaCotizaciones(){
                                           
        $query = "SELECT c.`id_cotizacion`, c.`fecha`, pe.nombrecompleto AS vendedor, c.`monto_total`,
            c.`incl_igv`, pe.id_persona, 
            (select p.empresacliente from mae_persona p where p.id_persona = c.`id_persona` ) as empresa,
	    (select p.`nombrecompleto` from mae_persona p where p.id_persona = c.`id_persona` ) as cliente,
            (SELECT mo.`sigla` FROM `pub_moneda` mo WHERE mo.`id_moneda` = c.`id_moneda`) AS moneda,
            c.`estado`,
            (SELECT cm.`fecha_creacion` FROM `ven_cotizacion_mov` cm 
                    WHERE cm.`id_cotizacion` = c.`id_cotizacion` AND c.`estado` = 'E' ORDER BY cm.`id_mov` DESC LIMIT 1 
            ) AS fecha_emitido,
            (SELECT cm.`fecha_creacion` FROM `ven_cotizacion_mov` cm 
                    WHERE cm.`id_cotizacion` = c.`id_cotizacion` AND c.`estado` = 'S' ORDER BY cm.`id_mov` DESC LIMIT 1 
            ) AS fecha_enviado,
            (SELECT cm.`fecha_creacion` FROM `ven_cotizacion_mov` cm 
                    WHERE cm.`id_cotizacion` = c.`id_cotizacion` AND c.`estado` = 'P' ORDER BY cm.`id_mov` DESC LIMIT 1 
            ) AS fecha_procesado
            FROM `ven_cotizacion` c
                  INNER JOIN `mae_usuario` u ON u.`id_usuario` = c.`usuario_creacion`                
                  INNER JOIN mae_persona pe ON pe.id_persona = u.id_persona
            WHERE c.estado IN ('E','S','P') AND DATE_FORMAT(c.fecha_creacion,'%Y-%m-%d') BETWEEN :f1 AND :f2 AND
                              pe.id_persona =  :idVendedor
            ORDER BY c.`fecha`, c.id_cotizacion asc";
        $parms = array(
           ':f1' => $this->_f1,
           ':f2' => $this->_f2,
           ':idVendedor' => $this->_idVendedor
          );
        $data = $this->queryAll($query,$parms);
        return $data;
    }  
        
    
}

?>
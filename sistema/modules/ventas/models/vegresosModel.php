<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 22-11-2014 20:11:18 
* Descripcion : vegresosModel.php
* ---------------------------------------
*/ 

class vegresosModel extends Model{

    private $_flag;
    private $_idVegresos;
    private $_fecha1, $_fecha2,$_idSucursalGrid;    
    private $_descripcion,  $_idCaja;
    private $_monto;
    private $_idSucursalCombo;
    private $_beneficiario;    
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
        $this->_idVegresos   = Aes::de(Formulario::getParam("_idVegresos"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");

        $this->_idCaja   = Aes::de(Formulario::getParam(VEGRE.'lst_caja')); 
        if( Session::get('sys_visible') == 'S' ){
            $this->_idSucursalGrid = Formulario::getParam("_idSucursalGrid");
        }else{
            $this->_idSucursalGrid = Session::get('sys_idSucursal');
        }            
        $this->_idSucursalCombo =  Aes::de(Formulario::getParam("_idSucursalCombo"));
        $this->_fecha1 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha1"));
        $this->_fecha2 = Functions::cambiaf_a_mysql(Formulario::getParam("_fecha2"));
        
        $this->_descripcion  = Formulario::getParam(VEGRE.'txt_descripcion');
        $this->_beneficiario  = Aes::de(Formulario::getParam(VEGRE.'lst_beneficiario'));
        $this->_monto     = str_replace(',','',Formulario::getParam(VEGRE.'txt_monto')); 
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Vegresos*/
    public function getVegresos(){
        $aColumns       =   array("id_egreso","sucursal","descripcion","fecha","monto","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaGenerarEgresosGrid(:idSucursal,:fecha1,:fecha2,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
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
    
    /*grabar nuevo registro: Vegresos*/
    public function newVegresos(){
        $query = "call sp_ventaGenerarEgresosMantenimiento(:flag,:key,:idCaja,:descripcion,:monto,:bene,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => '',
            ':idCaja' => $this->_idCaja,            
            ':descripcion' => $this->_descripcion,            
            ':monto' => $this->_monto, 
            ':bene' => $this->_beneficiario,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Vegresos*/
    public function findVegresos(){
         $query = "SELECT 
                `id_egreso`,
                `descripcion`,
                `fecha`,
                `monto`, 
                `id_moneda`, id_caja, id_sucursal,
                `estado` 
              FROM ven_egreso 
              WHERE  id_egreso = :idd; ";
        
        $parms = array(
            ':idd' => $this->_idVegresos
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*editar registro: Vegresos*/
    public function editVegresos(){
        $query = "call sp_ventaGenerarEgresosMantenimiento(:flag,:key,:idCaja,:descripcion,:monto,:bene,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idVegresos,
            ':idCaja' => $this->_idCaja,            
            ':descripcion' => $this->_descripcion,            
            ':monto' => '', 
            ':bene' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: Vegresos*/
    public function deleteVegresos(){
       $query = "call sp_ventaGenerarEgresosMantenimiento(:flag,:key,:idCaja,:descripcion,:monto,:bene,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idVegresos,
            ':idCaja' => '' ,
            ':descripcion' => '',
            ':monto' => '',
            ':bene' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function findDataBeneficiarioAll($idSucursal){
         if($idSucursal == '') $idSucursal = $this->_idSucursalCombo;
         
        $query = "SELECT t.id_persona, t.persona, t.tipo, t.orden
            FROM 
            (SELECT p.`id_persona`, p.`nombrecompleto` AS persona, 'Colaboradores' AS tipo, 1 AS orden
                FROM `mae_persona` p
                WHERE p.estado = 'A' and p.`empleado` = 'S' AND p.`id_sucursal` = :idSucursal1
            UNION ALL
                SELECT p.`id_persona`, p.`empresacliente` AS persona, 'Proveedores', 2 AS orden
                FROM `mae_persona` p
                WHERE p.estado = 'A' and p.`proveedor` = 'S' 
            UNION ALL
                SELECT p.`id_persona`, p.`empresacliente` AS persona, 'Empresa' AS tipo, 0 AS orden
                FROM `mae_persona` p
                WHERE p.estado = 'A' AND p.id_persona = 201500000002           
            ) AS t
            ORDER BY t.orden, t.persona; ";
        
        $parms = array(
            ':idSucursal1' => $idSucursal
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
}

?>
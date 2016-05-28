<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-09-2014 23:09:58 
* Descripcion : generarCotizacionModel.php
* ---------------------------------------
*/ 

class generarCotizacionModel extends Model{

    private $_flag;
    public $_idCotizacion, $_all;    
    
    private $_fecha;
    private $_idProducto;
    private $_montoTotal;
    private $_observacion;
    private $_idPersona;
    private $_moneda;
    private $_usuario;
    private $_cantidad1;
    private $_cantidad2;
    private $_precio, $_descripcion;
    private $_incigv;
    private $_idUsuarioMigrar;

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
        $this->_idCotizacion   = Aes::de(Formulario::getParam("_idCotizacion"));    /*se decifra*/
        $this->_usuario                 = Session::get("sys_idUsuario");
        
        $this->_fecha     = Functions::cambiaf_a_mysql(Formulario::getParam(VCOTI."txt_fecha"));
        $this->_montoTotal     = Functions::deleteComa(Formulario::getParam(VCOTI."txt_total"));
        $this->_observacion     = Formulario::getParam(VCOTI."txt_obs");
        $this->_idPersona     = Aes::de(Formulario::getParam(VCOTI."txt_idpersona"));
        $this->_moneda     = Formulario::getParam(VCOTI."lst_moneda");
        $this->_incigv         = Formulario::getParam(VCOTI.'lst_igv'); 
        
        $this->_idProducto     = Formulario::getParam(VCOTI."hhddIdProducto"); #array
        $this->_cantidad1     = Formulario::getParam(VCOTI."txt_cantidad1");#array
        $this->_cantidad2     = Formulario::getParam(VCOTI."txt_cantidad2");#array        
        $this->_precio     = Formulario::getParam(VCOTI."txt_precio");#array
        $this->_descripcion     = Formulario::getParam(VCOTI."txt_descripcion");#array
        
        $this->_idUsuarioMigrar = Aes::de(Formulario::getParam(VCOTI."lst_usuario"));
        
        
        if(Session::get('sys_defaultRol') == APP_COD_SADM || Session::get('sys_defaultRol') == APP_COD_ADMIN ){
            $this->_all = 'S';
        }else if (Session::get('sys_defaultRol') == APP_COD_CAJERO){
            $this->_all = 'C';        
        }else{
            $this->_all = 'N';
        }                                                   
        
        $this->_iDisplayStart  =   Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength =   Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   =   Formulario::getParam("iSortingCols");
        $this->_sSearch        =   Formulario::getParam("sSearch");
    }
    
    public function getGridGenerarCotizacion(){
        $aColumns       =   array( 'id_cotizacion','cliente','fecha','monto_total','estado'); //para la ordenacion y pintado en html
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
        $query = "call sp_ventaGenerarCotizacionGrid(:acceso,:idUsuario,:idSucursal, :iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":acceso" => $this->_all,
            ":idUsuario" => $this->_usuario,
            ":idSucursal"=> Session::get('sys_idSucursal'),
            ':iDisplayStart' => $this->_iDisplayStart,
            ':iDisplayLength' => $this->_iDisplayLength,
            ':sOrder' => $sOrder,
            ':sSearch' => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data; 
    }
    
    public function newGenerarCotizacion(){
        if($this->_flag == 3){//cuando se clona una cotizacion, hay q anularla
            $this->anularGenerarCotizacion();
            $this->_flag = 1; //retorna a 1 para ael SP
        }
                
        $query = "CALL sp_ventaGenerarCotizacionMantenimiento("
                . ":flag,"
                . ":idCotizacion,"
                . ":fecha,"
                . ":idPersona,"
                . ":moneda,"
                . ":montoTotal,"
                . ":obs, :icl, "
                . ":idProducto,"
                . ":cant1,"
                . ":cant2,"
                . ":precio, :descripcion, "
                . ":usuario"
            . "); ";
               
        $parms = array(
            ':flag'=> $this->_flag,
            ':idCotizacion'=> $this->_idCotizacion,
            ':fecha'=>$this->_fecha,
            ':idPersona'=>$this->_idPersona,
            ':moneda'=>$this->_moneda,
            ':montoTotal'=>$this->_montoTotal,
            ':obs' => $this->_observacion,
            ':icl' => $this->_incigv,
            ':idProducto' => '',
            ':cant1' => '',                
            ':cant2' => '',
            ':precio' => '',
            ':descripcion' => '',
            ':usuario'=> $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
              
        $idCotizacion = $data['idCotizacion'];
                
        if($data['result'] == '1'){
            /*detalle de produccion*/
            foreach ($this->_idProducto as $key=>$idProducto) {
                $parmsx = array(
                    ':flag'=> 2,
                    ':idCotizacion'=>$idCotizacion,
                    ':fecha'=>'',
                    ':idPersona'=>'',
                    ':moneda'=>'',
                    ':montoTotal'=>'',
                    ':obs' => '',
                    ':icl' => $this->_incigv,
                    ':idProducto' => AesCtr::de($idProducto),
                    ':cant1' => Functions::deleteComa($this->_cantidad1[$key]),                
                    ':cant2' => Functions::deleteComa($this->_cantidad2[$key]),
                    ':precio'=> Functions::deleteComa($this->_precio[$key]),
                    ':descripcion'=> $this->_descripcion[$key],
                    ':usuario'=> $this->_usuario
                );
                $this->execute($query,$parmsx);
              
            }
        }
        $datax = array('result'=>1);
        return $datax;
    }

    public function anularGenerarCotizacion (){
        $query = "call sp_ventaAnulacion( :flag, :idCot, :usuario);";
        $parms = array(
            ':flag' => 2,
            ':idCot' => $this->_idCotizacion,
            ':usuario'=> $this->_usuario
        );        
        $data = $this->queryOne($query,$parms);
        return $data;        
    }
    
    public function enviarCotizacion(){       
                
        $query = "CALL sp_ventaGenerarCotizacionMantenimiento("
                . ":flag,"
                . ":idCotizacion,"
                . ":fecha,"
                . ":idPersona,"
                . ":moneda,"
                . ":montoTotal,"
                . ":obs, :icl, "
                . ":idProducto,"
                . ":cant1,"
                . ":cant2,"
                . ":precio, :descripcion, "
                . ":usuario"
            . "); ";
               
        $parms = array(
            ':flag'=> 4,
            ':idCotizacion'=> $this->_idCotizacion,
            ':fecha'=>'',
            ':idPersona'=>'',
            ':moneda'=>'',
            ':montoTotal'=>'',
            ':obs' =>'',
            ':icl' => '',
            ':idProducto' => '',
            ':cant1' => '',                
            ':cant2' => '',
            ':precio' => '',
            ':descripcion' => '',
            ':usuario'=> $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function migrarCotizacion(){
         $query = "CALL sp_ventaGenerarCotizacionMantenimiento("
                . ":flag,"
                . ":idCotizacion,"
                . ":fecha,"
                . ":idPersona,"
                . ":moneda,"
                . ":montoTotal,"
                . ":obs, :icl, "
                . ":idProducto,"
                . ":cant1,"
                . ":cant2,"
                . ":precio, :descripcion, "
                . ":usuario"
            . "); ";
               
        $parms = array(
            ':flag'=> 5,
            ':idCotizacion'=> $this->_idCotizacion,
            ':fecha'=>'',
            ':idPersona'=>$this->_idUsuarioMigrar,
            ':moneda'=>'',
            ':montoTotal'=>'',
            ':obs' =>'',
            ':icl' => '',
            ':idProducto' => '',
            ':cant1' => '',                
            ':cant2' => '',
            ':precio' => '',
            ':descripcion' => '',
            ':usuario'=> $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
              
    public function getFindCotizacion(){
        $query = "
        SELECT
            d.`id_cotizacion`,            
            DATE_FORMAT(d.`fecha`,'%d/%m/%Y')AS fecha, 
            d.`fecha` as fechaRpt,            
            d.`id_persona`,
            (select pp.nombrecompleto from mae_persona pp where pp.id_persona = d.id_persona) as cliente,
            (select pp.empresacliente from mae_persona pp where pp.id_persona = d.id_persona) as empresa,
            (select concat(`sigla`,' ( ',`descripcion`,' )') from pub_moneda mo where mo.id_moneda = d.id_moneda) as descripcion_moneda,
            (select `sigla` from pub_moneda mo where mo.id_moneda = d.`id_moneda`) as moneda,   
            d.id_moneda,
            d.`observacion`,
            d.estado,
            d.monto_importe,
            d.porcentaje_igv,
            d.incl_igv,
            (SELECT usuario FROM mae_usuario WHERE id_usuario= d.`usuario_creacion`)AS mail_user,
            (SELECT pp.nombrecompleto FROM mae_usuario uu
			INNER JOIN mae_persona pp ON pp.id_persona = uu.id_persona
                WHERE id_usuario= d.`usuario_creacion`)AS vendedor,
            (SELECT pp.telefono FROM mae_usuario uu
			INNER JOIN mae_persona pp ON pp.id_persona = uu.id_persona
                WHERE id_usuario= d.`usuario_creacion`)AS telefono_vendedor,
             p.`nombrecompleto`,
             p.numerodocumento,
             p.`email`             
          FROM ven_cotizacion d
            INNER JOIN mae_persona p ON p.`id_persona` = d.`id_persona`
          WHERE d.id_cotizacion = :idCotizacion; ";
        
        $parms = array(
            ':idCotizacion'=>  $this->_idCotizacion
        );
        $data = $this->queryOne($query,$parms);      
        
        return $data;
    }    
    
    public function getFindCotizacionD(){
        $query = "
        SELECT
            dd.`cantidad_1`, dd.`cantidad_2`, dd.`cantidad_real`,
            dd.`id_detalle`, dd.`id_cotizacion`, dd.`id_producto`, dd.`importe`, dd.`precio`,
            u.`sigla`, u.`cantidad_multiple`,
            dd.importe_afectado, dd.impuesto, dd.total_impuesto, dd.precio_final,
            dd.descripcion,
            p.descripcion as producto
        FROM `ven_cotizaciond` dd
                INNER JOIN `ven_producto` p ON p.`id_producto` = dd.`id_producto`
                INNER JOIN `ven_unidadmedida` u ON u.`id_unidadmedida` = p.`id_unidadmedida`
        WHERE dd.id_cotizacion =  :idCotizacion; ";
        
        $parms = array(
            ':idCotizacion'=>  $this->_idCotizacion
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }   
    
    public function getEmpleadosAll(){
        $query = 'call sp_perBuscarPersona(:flag, :busq, :est, :acc, :idUs)';
        $parms = array(
            ':flag'=>  '3',
            ':busq' => '',
            ':est' => 'A',
            ':acc' => '',
            ':idUs' => ''
        );
        $data = $this->queryAll($query,$parms);      
        
        return $data;
        
    }
   
}

?>
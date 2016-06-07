<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 07-11-2014 00:11:47 
* Descripcion : vproductoModel.php
* ---------------------------------------
*/ 

class vproductoModel extends Model{

    private $_idVproducto;
    private $_chkdel;
    private $_nombre;
    private $_precio;
    private $_idUM;
    private $_usuario;
    public $_tab, $_funcionExterna;
    
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
        $this->_idVproducto   = Aes::de(Formulario::getParam("_idVproducto"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        
        $this->_tab    = Formulario::getParam("_tab");
        $this->_funcionExterna  = Formulario::getParam("_funcionExterna");
        
        $this->_chkdel  = Formulario::getParam(VPROD.'chk_delete');
        $this->_nombre     = Formulario::getParam(VPROD.'txt_descripcion');
        $this->_precio     = str_replace(',','',Formulario::getParam(VPROD.'txt_precio')); 
        $this->_idUM     = Formulario::getParam(VPROD.'lst_unidadMedida');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Vproducto*/
    public function getVproducto(){
        $aColumns       =   array("id_producto","descripcion","5","7","precio","estado" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_ventaProductoGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    /*grabar nuevo registro: Vproducto*/
    public function newVproducto(){
        $query = "call sp_ventaProductoMantenimiento(:flag,:key,:nombre,:precio,:idum,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':key' => $this->_idVproducto,
            ':nombre' => $this->_nombre,
            ':precio' => $this->_precio,            
            ':idum' => $this->_idUM, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*seleccionar registro a editar: Vproducto*/
    public function findVproducto(){
       $query = "SELECT
        `id_producto`,
        `descripcion`,
        `precio`,
        `estado`,
        `id_unidadmedida`
      FROM `ven_producto` WHERE id_producto = :idd; ";
        
        $parms = array(
            ':idd' => $this->_idVproducto
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /* Busqueda para Productos / Servicios para cotizacion y Ventas */
    public function getFindProductos(){
        $query = "
        SELECT 
            t.id_catalogo, CONCAT(t.nombre,' ' ,t.concentracion,' ',t.presentacion) AS descripcion,
            t.laboratorio,
            t.fraccion, t.receta_medica,
            t.familia, t.generico, t.clasificacion
        FROM( SELECT
                c.`id_catalogo`, c.`nombre`, c.`concentracion`, c.`fraccion`, c.`receta_medica`,
                (SELECT f.`descripcion` FROM `lgk_familia` f WHERE f.`id_familia` = c.`id_familia`) AS familia,
                (SELECT g.`descripcion` FROM `lgk_generico` g WHERE g.`id_generico` = c.`id_generico`) AS generico,
                (SELECT l.`sigla` FROM `lgk_laboratorio` l WHERE l.`id_laboratorio` = c.`id_laboratorio`) AS laboratorio,
                (SELECT p.`descripcion` FROM `lgk_presentacion` p WHERE p.`id_presentacion` = c.`id_presentacion`) AS presentacion,
                (SELECT cc.`descripcion_corta` FROM `lgk_clasificacion` cc WHERE cc.`id_clasificacion` = c.`id_clasificacion`) AS clasificacion,
                DATE_FORMAT(c.`fecha_creacion`,'%d/%m/%Y') AS fecha_creacion, c.estado	
        FROM `lgk_catalogogral` c
        WHERE c.`estado` = :est) AS t limit 100 ";
        
        $parms = array(
            ':est'=>  'A' 
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }     
    
    public function getBuscarProducto(){
        $aColumns       =   array("","nombre","laboratorio" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_buscarProductoGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }           
    
    /*editar registro: Vproducto*/
    public function editVproducto(){
        $query = "call sp_ventaProductoMantenimiento(:flag,:key,:nombre,:precio,:idum,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':key' => $this->_idVproducto,
            ':nombre' => $this->_nombre,
            ':precio' => $this->_precio,            
            ':idum' => $this->_idUM, 
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    /*eliminar varios registros: Vproducto*/
    public function deleteVproducto(){
        $query = "call sp_ventaProductoMantenimiento(:flag,:key,:nombre,:precio,:idum,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':key' =>  $this->_idVproducto,
            ':nombre' => '',
            ':precio' => '',   
            ':idum' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function getUnidadMedida(){
        $query = "SELECT id_unidadmedida,CONCAT(TRIM(nombre),' - ', TRIM(sigla)) AS nombre FROM ven_unidadmedida WHERE estado = :estado; ";
        
        $parms = array(
            ':estado' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
    public function postDesactivar(){
        $query = "UPDATE ven_producto SET
                    `estado` = 'I'
                WHERE `id_producto` = :id;";
        $parms = array(
            ':id' => $this->_idVproducto
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);    
        
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `ven_producto` SET
                    `estado` = 'A'
                WHERE `id_producto` = :id;";
        $parms = array(
            ':id' => $this->_idVproducto
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }                
    
    public function getMoneda(){
        $query = "select
                `id_moneda` as id,
                CONCAT(`sigla`,' - ',`descripcion`) as descripcion
              from `pub_moneda`
              where estado = :estado; ";
        
        $parms = array(
            ':estado' => 'A'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }    
    
    public function findServicioOne(){
       $query = "SELECT
        p.`id_producto`,
        p.`descripcion`,
        p.`precio`,
        p.`estado`,
        p.`id_unidadmedida`,
        um.sigla as unidadmedida
      FROM `ven_producto` p
        inner join ven_unidadmedida um on um.id_unidadmedida = p.id_unidadmedida
      WHERE id_producto = 201600000001; ";        
      $parms = array(                            
      );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
}

?>
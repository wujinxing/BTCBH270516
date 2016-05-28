<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-11-2014 17:11:18 
* Descripcion : vclienteModel.php
* ---------------------------------------
*/ 

class vClienteModel extends Model{

    private $_flag;
    public $_idPersona;
    private $_empresa;    
    private $_nombres;
    private $_sexo;
    private $_direccion;
    private $_email;
    private $_telefono;
    private $_numeroDoc;
    private $_ruc;
    private $_ubigeo;
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
        $this->_tab    = Formulario::getParam("_tab");
        $this->_funcionExterna  = Formulario::getParam("_funcionExterna");
        
        $this->_idPersona   = Aes::de(Formulario::getParam("_idPersona"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
                      
        $this->_empresa = str_replace('&#039;',"´",mb_strtoupper(Formulario::getParam(VRECL.'txt_empresa'),'UTF-8') );
        $this->_nombres = str_replace('&#039;',"´",Functions::ucname(Formulario::getParam(VRECL.'txt_nombres')));
        $this->_sexo = Formulario::getParam(VRECL.'rd_sexo');
        $this->_direccion = Formulario::getParam(VRECL.'txt_direccion');
        $this->_email = Formulario::getParam(VRECL.'txt_email');
        $this->_telefono = Formulario::getParam(VRECL.'txt_telefonos');
        $this->_numeroDoc = Formulario::getParam(VRECL.'txt_nrodocumento');
        $this->_ubigeo =  Aes::de(Formulario::getParam(VRECL.'lst_ubigeo'));
        $this->_ruc = Formulario::getParam(VRECL.'txt_ruc');
        
        $this->_usuario = Session::get('sys_idUsuario');
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Vcliente*/
    public function getVcliente(){
        $aColumns = array( 'id_persona','empresacliente ASC, nombrecompleto','telefono','7' ); //para la ordenacion y pintado en html

        /*
	 * Ordenando, se verifica por que columna se ordenara
	 */
        $sOrder = "";
        for ( $i=0 ; $i<intval( $this->_iSortingCols ) ; $i++ ){
                if ( Formulario::getParam( 'bSortable_'.intval(Formulario::getParam('iSortCol_'.$i)) ) == "true" ){
                        $sOrder .= " ".$aColumns[ intval( Formulario::getParam('iSortCol_'.$i) ) ]." ".
                                (Formulario::getParam('sSortDir_'.$i)==='asc' ? 'asc' : 'desc') .",";
                }
        }
        $sOrder = substr_replace( $sOrder, "", -1 );
        $query = "call sp_ventaClienteGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ':iDisplayStart' => $this->_iDisplayStart,
            ':iDisplayLength' => $this->_iDisplayLength,
            ':sOrder' => $sOrder,
            ':sSearch' => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
      
        return $data;
    }
    
    public function getBuscarCliente(){
        $aColumns       =   array("","nombrecompleto" ); //para la ordenacion y pintado en html
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
        
        $query = "call sp_buscarClienteGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }           
    
    /*grabar nuevo registro: Vcliente*/
    public function newVcliente(){
       $query = "call sp_ventaClienteMantenimiento(
                    :flag,
                    :idPersona,
                    :empresa,
                    :nombres,
                    :sexo,
                    :direccion,
                    :email,
                    :telefono,
                    :numeroDoc,
                    :ubigeo,
                    :ruc,
                    :ip,
                    :usuario
                );";
        $parms = array(
            ':flag' => 1,
            ':idPersona' => '',
            ':empresa' => $this->_empresa,
            ':nombres' => $this->_nombres,
            ':sexo' => $this->_sexo,
            ':direccion' => $this->_direccion,
            ':email' => $this->_email,
            ':telefono' => $this->_telefono,
            ':numeroDoc' => $this->_numeroDoc,
            ':ubigeo' => $this->_ubigeo,
            ':ruc' => $this->_ruc,
            ':ip'=> $_SERVER["REMOTE_ADDR"],
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);     
        return $data;
    }
    
    /*seleccionar registro a editar: Vcliente*/
    public function findVcliente(){
      $query = "SELECT 
                        p.id_persona,                         
                        p.empresacliente,
                        p.numerodocumento,
                        p.nombrecompleto,
                        p.id_ubigeo,
                        p.direccion,
                        p.email,
                        p.sexo,
                        p.telefono,
                        p.ruccliente                                            
                    FROM mae_persona p  
                    WHERE p.id_persona = :iddd;";

            $parms = array(
                ':iddd'=>$this->_idPersona
            );

            $data = $this->queryOne($query,$parms);
            return $data;
    }
    
    /*editar registro: Vcliente*/
    public function editVcliente(){
        $query = "call sp_ventaClienteMantenimiento(
                    :flag,
                    :idPersona,
                    :empresa,
                    :nombres,
                    :sexo,
                    :direccion,
                    :email,
                    :telefono,
                    :numeroDoc,
                    :ubigeo,
                    :ruc,
                    :ip,
                    :usuario
                );";
        $parms = array(
            ':flag' => 2,
            ':idPersona' => $this->_idPersona,
            ':empresa' => $this->_empresa,
            ':nombres' => $this->_nombres,
            ':sexo' => $this->_sexo,
            ':direccion' => $this->_direccion,
            ':email' => $this->_email,
            ':telefono' => $this->_telefono,
            ':numeroDoc' => $this->_numeroDoc,
            ':ubigeo' => $this->_ubigeo,
            ':ruc' => $this->_ruc,
            ':ip'=> $_SERVER["REMOTE_ADDR"],
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);     
        return $data;
    }
    
    public function deleteVcliente(){
      
        $query = "UPDATE `mae_persona` SET
                    `estado` = '0'
                WHERE `id_persona` = :idPersona;";
        $parms = array(
            ':idPersona' => $this->_idPersona
        );
        $this->execute($query,$parms);
        
        $data = array('result'=>1);
        return $data;
    }    
    
    public function postDesactivar(){
        $query = "UPDATE `mae_persona` SET
                    `estado` = 'I'
                WHERE `id_persona` = :idPersona;";
        $parms = array(
            ':idPersona' => $this->_idPersona
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
    
    public function postActivar(){
        $query = "UPDATE `mae_persona` SET
                    `estado` = 'A'
                WHERE `id_persona` = :idPersona;";
        $parms = array(
            ':idPersona' => $this->_idPersona
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }   
    
}

?>
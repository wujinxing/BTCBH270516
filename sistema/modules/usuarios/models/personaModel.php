<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 16-09-2014 03:09:14 
* Descripcion : personaModel.php
* ---------------------------------------
*/ 

class personaModel extends Model{

    private $_flag;    
    private $_idPersona;
    private $_nombres;
    private $_sexo;
    private $_direccion;
    private $_email;
    private $_telefono;
    private $_ubigeo;
    private $_fechaCumple;
    public $_usuario;
    private $_idIdioma;
    private $_cliente, $_empleado, $_proveedor;    
    private $_dni;
    private $_idSucursal;
    private $_ruc, $_empresa;
    public $_idPersonaSession;
    
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
        $this->_flag    = Formulario::getParam('_flag');
        $this->_idPersona     = Aes::de(Formulario::getParam('_idPersona'));    /*se decifra*/        
        $this->_nombres = Functions::ucname(Formulario::getParam(REPER.'txt_nombres'));
        $this->_sexo = Formulario::getParam(REPER.'rd_sexo');
        $this->_direccion = Formulario::getParam(REPER.'txt_direccion');
        $this->_email = Formulario::getParam(REPER.'txt_email');
        $this->_dni = Formulario::getParam(REPER.'txt_dni');
        $this->_telefono = Formulario::getParam(REPER.'txt_telefonos');
        $this->_ubigeo =  Aes::de(Formulario::getParam(REPER.'lst_ubigeo'));
        $this->_fechaCumple = Functions::cambiaf_a_mysql(Formulario::getParam(REPER.'txt_fechaNac'));
        $this->_idIdioma =  1;
        $this->_cliente = Formulario::getParam(REPER.'chk_cliente');
        $this->_empleado = Formulario::getParam(REPER.'chk_empleado');
        $this->_proveedor= Formulario::getParam(REPER.'chk_proveedor');
        $this->_usuario = Session::get('sys_idUsuario');
        $this->_idPersonaSession = Session::get('sys_idPersona');
        $this->_idSucursal =  Aes::de(Formulario::getParam(REPER.'lst_sucursal'));
        
        $this->_ruc = Formulario::getParam(REPER.'txt_ruc');
        $this->_empresa = str_replace('&#039;',"´",mb_strtoupper(Formulario::getParam(REPER.'txt_empresa'),'UTF-8') );
        
        $this->_iDisplayStart  =   Formulario::getParam('iDisplayStart'); 
        $this->_iDisplayLength =   Formulario::getParam('iDisplayLength'); 
        $this->_iSortingCols   =   Formulario::getParam('iSortingCols');
        $this->_sSearch        =   Formulario::getParam('sSearch');
    }
    
    public function getGridPersona() {
        $aColumns = array('id_persona','','nombrecompleto','email','telefono','7' ); //para la ordenacion y pintado en html

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
        
        $query = "call sp_perPersonaGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ':iDisplayStart' => $this->_iDisplayStart,
            ':iDisplayLength' => $this->_iDisplayLength,
            ':sOrder' => $sOrder,
            ':sSearch' => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
      
        return $data;
    }    
    
    public function mantenimientoPersona(){
        $query = "call sp_perPersonaMantenimiento(
                    :flag,
                    :idPersona,
                    :nombres,
                    :sexo,
                    :direccion,
                    :email,
                    :telefono,                  
                    :ubigeo,
                    :fechacumple,                    
                    :idioma,
                    :dni,
                    :cliente,
                    :empleado,
                    :proveedor,
                    :sucursal,
                    :empresa,
                    :ruc,
                    :usuario
                );";
        $parms = array(
            ':flag' => $this->_flag,
            ':idPersona' => $this->_idPersona,
            ':nombres' => $this->_nombres,
            ':sexo' => $this->_sexo,
            ':direccion' => $this->_direccion,
            ':email' => $this->_email,
            ':telefono' => $this->_telefono,
            ':ubigeo' => $this->_ubigeo,
            ':fechacumple' => $this->_fechaCumple,
            ':idioma' => $this->_idIdioma,
            ':dni' => $this->_dni,
            ':cliente' => ($this->_cliente!=''?'S':'N'),
            ':empleado' => ($this->_empleado!=''?'S':'N'),
            ':proveedor' => ($this->_proveedor!=''?'S':'N'),
            ':sucursal' => $this->_idSucursal,
            ':empresa' => $this->_empresa,
            ':ruc' => $this->_ruc,
            ':usuario' => $this->_usuario
            
        );
        $data = $this->queryOne($query,$parms);     
        return $data;
    }
    
    /*seleccionar registro a editar: Persona*/
    public function findPersona(){
        $query = "SELECT 
                        p.id_persona,                        
                        p.nombrecompleto,
                        p.numerodocumento,
                        p.id_ubigeo,
                        p.direccion,
                        p.email,
                        p.sexo,
                        p.telefono,
                        (select u.foto
                            from mae_usuario u where 
                                u.id_persona=p.id_persona
                         ) as foto,                        
                        p.fecha_cumple,                        
                        p.id_idioma, p.estado,
                        p.codigo_pic, p.link, p.id_sucursal,
                        p.cliente, p.empleado, p.proveedor, p.`ruccliente`, p.`empresacliente`,
                        (SELECT u.`distrito` FROM `ub_ubigeo` u WHERE u.`id_ubigeo` = p.`id_ubigeo`) AS ciudad,
                        (SELECT d.`departamento` FROM `ub_departamento` d WHERE d.`id_departamento` = SUBSTRING(p.`id_ubigeo`,1,5) ) AS departamento,
                        (SELECT pa.`descripcion` FROM `ub_pais` pa WHERE pa.`id_pais` = SUBSTRING(p.`id_ubigeo`,1,3) ) AS pais
                    FROM mae_persona p                    
                    WHERE p.id_persona = :idPersona;";

            $parms = array(
                ':idPersona'=>$this->_idPersona
            );

            $data = $this->queryOne($query,$parms);
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
    
    public function postFacebookPersonaUpdate($idFB, $email, $link, $fn){
        $query = " call sp_clienteFacebookMantenimiento(:flag,:id, :nom, :sexo, :dire, :email, :fono, :ubigeo, :fechnac, :idioma, "
                 . ":idFB, :linkFB, :ip, :usuario ) ;"; 
        $parms = array(
                ':flag'=> 3,
                ':id'=> $this->_idPersonaSession,                
                ':nom'=> '',
                ':sexo'=>  '',
                ':dire'=> '',
                ':email'=> $email,
                ':fono'=>  '',
                ':ubigeo' => '',
                ':fechnac'=>  $fn,
                ':idioma'=> '', 
                ':idFB'=> $idFB,
                ':linkFB'=> $link,
                ':ip'=> $_SERVER["REMOTE_ADDR"],
                ':usuario'=> $this->_usuario
            );     
        $data = $this->queryOne($query,$parms);     
        return $data;       
    }
    
    public function postFacebookFotografia($file){
        $query = " UPDATE `mae_usuario`
                SET `foto` = :foto
                WHERE `id_usuario` = :id ;"; 
        
        $parms = array(            
                ':foto' => $file,
                ':id' => $this->_usuario
                );
        $data = $this->queryOne($query,$parms);     
        return $data;      
    }    
        
}

?>
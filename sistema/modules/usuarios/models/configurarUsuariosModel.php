<?php
/*
 * Documento   : loginModel
 * Creado      : 30-ene-2014, 19:26:46
 * Autor       : RDCC
 * Descripcion :
 */
class configurarUsuariosModel extends Model{
    private $_flag;
    private $_key;
    private $_empleado;
    public $_idUsuario;
    private $_pass;
    private $_clave = '77777779';
    private $_mail;
    private $_roles;
    private $_usuario;
    private $_rol, $_notificacion;
    public  $_idPersona, $_estado;
    private $_bloqueo;
    
    /*para el grid*/
    private $_iDisplayStart;
    private $_iDisplayLength;
    private $_iSortingCols;
    private $_sSearch;
    private $_xsearch;
    
    public function __construct() {
        parent::__construct();
        $this->_set();
    }
    
    private function _set(){
        $this->_flag    = Formulario::getParam('_flag');
        $this->_key     = Aes::de(Formulario::getParam('_key'));    /*se decifra*/
        $this->_empleado = Aes::de(Formulario::getParam(T4.'txt_persona'));    /*se decifra*/
        $this->_idUsuario = Aes::de(Formulario::getParam('_idUsuario'));    /*se decifra*/
        $this->_idPersona = Aes::de(Formulario::getParam('_idPersona'));    /*se decifra*/
        $this->_roles  = Formulario::getParam(T4.'chk_roles');
        $this->_mail  = Formulario::getParam(T4.'txt_email');
        $this->_xsearch  = Formulario::getParam(T4.'_term');
        $this->_usuario = Session::get('sys_idUsuario');
        $this->_rol   = Formulario::getParam('_rol');              
        $this->_pass     = Aes::de(Formulario::getParam('_pass'));    /*se decifra*/             
        $this->_notificacion = (Formulario::getParam(T4."chk_notificacion") == NULL)? 'N':'S';
        $this->_email = Aes::de(Formulario::getParam('_usuario'));  
        $this->_bloqueo = (Formulario::getParam(T4.'txt_bloqueo'))*60000;  
        
        $this->_iDisplayStart  =   Formulario::getParam('iDisplayStart');
        $this->_iDisplayLength =   Formulario::getParam('iDisplayLength');
        $this->_iSortingCols   =   Formulario::getParam('iSortingCols');
        $this->_sSearch        =   Formulario::getParam('sSearch');        
    }
    
    public function getUsuarios(){
        $aColumns       =   array( 'ultimo_acceso','usuario','nombrecompleto' ); //para la ordenacion y pintado en html
        /*
	 * Ordenando, se verifica por que columna se ordenara
	 */
        $sOrder = "";
        for ( $i=0 ; $i<intval( $this->_iSortingCols ) ; $i++ ){
                if ( Formulario::getParam( 'bSortable_'.intval(Formulario::getParam('iSortCol_'.$i)) ) == "true" ){
                        $sOrder .= " ".$aColumns[ intval( Formulario::getParam('iSortCol_'.$i) ) ]." ".
                                (Formulario::getParam('sSortDir_'.$i)==='asc' ? 'asc' : 'desc') ." ";
                }
        }
        
        $query = "call sp_usuariosConfigurarUsuariosGrid(:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ':iDisplayStart' => $this->_iDisplayStart,
            ':iDisplayLength' => $this->_iDisplayLength,
            ':sOrder' => $sOrder,
            ':sSearch' => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getRoles(){
        
        if (Session::get('sys_defaultRol') == APP_COD_SADM){
            $query = " SELECT id_rol,rol FROM men_rol WHERE activo = :activo ";    
        }else{
            $query = " SELECT id_rol,rol FROM men_rol WHERE activo = :activo and not id_rol ='".APP_COD_SADM."' ";
        }
                
        $parms = array(
            ':activo' => '1',
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getRolesUser(){
        
        if (Session::get('sys_defaultRol') == APP_COD_SADM){
            $query = " SELECT 
                r.id_rol,
                r.rol,
                (SELECT COUNT(*) FROM `men_usuariorol` xx WHERE xx.`id_rol`=r.id_rol AND xx.`id_usuario`=:idUsuario) AS chk
        FROM men_rol r WHERE activo = :activo  ";  
        }else{
            $query = " SELECT 
                r.id_rol,
                r.rol,
                (SELECT COUNT(*) FROM `men_usuariorol` xx WHERE xx.`id_rol`=r.id_rol AND xx.`id_usuario`=:idUsuario) AS chk
                FROM men_rol r WHERE activo = :activo and not id_rol ='".APP_COD_SADM."' ";
        }
               
        $parms = array(
            ':idUsuario'=> $this->_idUsuario,
            ':activo' => '1',
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getEmpleados(){
        
        $query = 'call sp_perBuscarPersona(:flag, :nombre, :estado,:acceso,:idUsuario);';
        /*Rol: 1*/
        $parms = array(
            ':flag' => $this->_rol,
            ':nombre'=> $this->_xsearch,
            ':estado' => 'A',
            ':acceso' => Session::get('sys_all'),
            ':idUsuario' => $this->_usuario
        );
        $data = $this->queryAll($query,$parms);

        return $data;
    }
    
    public function getUsuario(){
        $query = "SELECT 
                u.`id_persona`,
                p.`nombrecompleto`,
                u.`usuario`,
                u.estado,
                u.foto,
                p.sexo,
                p.id_idioma,
                u.notificacion,
                u.tiempo_bloqueo
        FROM `mae_usuario` u 
            INNER JOIN mae_persona p ON p.`id_persona`=u.`id_persona`
        WHERE u.`id_usuario` = :idUsuario";
        
        $parms = array(
            ':idUsuario'=> $this->_idUsuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
      public function getUsuarioRender($idUsuario){
        $query = "SELECT 
                u.`id_persona`,
                p.`nombrecompleto`,
                u.`usuario`,
                u.estado,
                u.foto,
                p.sexo,
                u.fecha_final,
                u.notificacion,
                u.tiempo_bloqueo
        FROM `mae_usuario` u 
            INNER JOIN mae_persona p ON p.`id_persona`=u.`id_persona`
        WHERE u.`id_usuario` = :idUsuario";
        
        $parms = array(
            ':idUsuario'=> $idUsuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function mantenimientoUsuario(){
        $query = "call sp_usuariosConfigurarUsuariosMantenimiento(:flag,:key,:empleado,:usuario,:clave,:clavecomun,:user,:idRol,:ip, :nt, :bloqueo);";
        $parms = array(
            ':flag' => $this->_flag,
            ':key' => $this->_key,
            ':empleado' => $this->_empleado,
            ':usuario' => $this->_mail,
            ':clave' => md5($this->_clave.APP_PASS_KEY),
            ':clavecomun' =>$this->_clave,
            ':user' => $this->_usuario,
            ':idRol' => '',
            ':ip' => $_SERVER["REMOTE_ADDR"],
            ':nt' => $this->_notificacion,
            ':bloqueo' => $this->_bloqueo
        );
        $data = $this->queryOne($query,$parms);        
       
        if($data['lastIdUser'] !== NULL ){
            foreach ($this->_roles as $rol) {
                $query = "call sp_usuariosConfigurarUsuariosMantenimiento(:flag,:key,:empleado,:usuario,:clave,:clavecomun,:user,:idRol, :ip, :nt, :bloqueo);";
                $parms = array(
                    ':flag' => 2,
                    ':key' => $data['lastIdUser'],
                    ':empleado' =>'',
                    ':usuario' =>  '',
                    ':clave' => '',
                    ':clavecomun' =>'',
                    ':user' => $this->_usuario,
                    ':idRol' =>AesCtr::de($rol),
                    ':ip' => '',
                    ':nt' => '',
                    ':bloqueo' => ''                    
                );
                $this->execute($query,$parms);
            }
        }        
        return $data;
    }
    
    public function editarUsuario(){
        $query = "call sp_usuariosConfigurarUsuariosMantenimiento(:flag,:key,:empleado,:usuario,:clave,:clavecomun,:user,:idRol, :ip, :nt, :bloqueo);";
        $parms = array(
            ':flag' => 3,
            ':key' => $this->_idUsuario,
            ':empleado' => '',
            ':usuario' => $this->_mail,
            ':clave' => '',
            ':clavecomun' => '',
            ':user' => $this->_usuario,
            ':idRol' => '',
            ':ip' => $_SERVER["REMOTE_ADDR"],
            ':nt' => $this->_notificacion,
            ':bloqueo' => $this->_bloqueo
        );
        $data = $this->queryOne($query,$parms);
        
        $res = array('result'=>$data['result'],'duplicado'=>$data['duplicado']);
        
        if($data['result'] == 1){
            /*se borra roles*/
            if (Session::get('sys_defaultRol') == APP_COD_SADM){
                $query = "DELETE FROM men_usuariorol WHERE id_usuario = :idUsuario";
            }else{
                 $query = "DELETE FROM men_usuariorol WHERE id_usuario = :idUsuario and not id_rol ='".APP_COD_SADM."'  ";
            }
           
            $parms = array(
                ':idUsuario' => $this->_idUsuario
            );
            $this->execute($query,$parms);

            /*se graba nuevos roles*/
            foreach ($this->_roles as $rol) {
                $query = "call sp_usuariosConfigurarUsuariosMantenimiento(:flag,:key,:empleado,:usuario,:clave,:clavecomun,:user,:idRol,:ip, :nt, :bloqueo);";
                $parms = array(
                    ':flag' => 2,
                    ':key' =>  $this->_idUsuario,
                    ':empleado' =>'',
                    ':usuario' => '',
                    ':clave' => '',
                    ':clavecomun' => '',
                    ':user' => $this->_usuario,
                    ':idRol' => AesCtr::de($rol),
                    ':ip' => $_SERVER["REMOTE_ADDR"],
                    ':nt' => '',
                    ':bloqueo'=>''
                );
                $this->execute($query,$parms);
            }

            $res = array('result'=>1,'duplicado'=>0);
        }
        return $res;
    }
    
    public function deleteUsuario(){
        
        $query = "UPDATE mae_usuario SET 
                    estado = :estado,
                    fecha_baja = NOW()
            WHERE id_usuario = :idUsuario";
        $parms = array(
            ':idUsuario' => $this->_key,            
            ':estado' => '0'
        );
        $this->execute($query,$parms);
        
        $res = array('result'=>1);
        return $res;
    }
   
    /*grabar Envios de Acceso */
    public function postNewEnvioAcceso($id){
        $query = "call sp_usuariosEnvioAccesoMantenimiento(:flag,:idUsuario,:est,:c1,:c2,:usuario);";
        $parms = array(
            ':flag' => 1,
            ':idUsuario' => Aes::de($id),
            ':est' => 'P',
            ':c1' => '',
            ':c2' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postEditEnvioAcceso(){
        $query = "call sp_usuariosEnvioAccesoMantenimiento(:flag,:idUsuario,:est,:c1,:c2,:usuario);";
        $parms = array(
            ':flag' => 2,
            ':idUsuario' => $this->_idUsuario,
            ':est' => $this->_estado,
            ':c1' => '',
            ':c2' => '',
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);  
        
        return $data;
    } 
    /* Cambiar Clave de Usuario */ 
    public function postPass(){       
        $query = "call sp_usuariosEnvioAccesoMantenimiento(:flag,:idUsuario,:est,:c1,:c2,:usuario);";
        $parms = array(
            ':flag' => 3,
            ':idUsuario' => $this->_idUsuario,
            ':est' => 'A',
            ':c1' => md5($this->_pass.APP_PASS_KEY),
            ':c2' =>  $this->_pass,
            ':usuario' => $this->_usuario
        );
        $data = $this->queryOne($query,$parms);  

        return $data;
    } 
    /* Logeo Automatico */
    public function getValidarUsuario(){                      
        $query = "call sp_confUsuarioConsultas(:flag,:user,:pass);";
        $parms = array(
            ':flag' => 1,
            ':user' => $this->_email,
            ':pass' => md5($this->_pass.APP_PASS_KEY)
        );
        $data = $this->queryOne($query,$parms);
        return $data;        
    }
    
    public function postLastLogin() {
        $query = "UPDATE mae_usuario SET ultimo_acceso = :fecha where id_usuario = :usuario;";
        $parms = array(
            ':fecha'=> date('Y-m-d H:m:s'),
            ':usuario' => Session::get('sys_idUsuario')
        );
        $data = $this->queryAll($query, $parms);
        return $data;
    }
    
    public function getRolesUsuario() {
        $query = "call sp_confUsuarioConsultas(:flag,:criterio1,:criterio2);";
        $parms = array(
            ':flag' => 2,
            ':criterio1' => $this->_idUsuario,
            ':criterio2' => ''
        );
        $data = $this->queryAll($query, $parms);
        return $data;
    }    
       
    public function postBaja(){
        $query = "UPDATE `mae_usuario` SET
                    `estado` = 'B'
                WHERE `id_usuario` = :idd;";
        $parms = array(
            ':idd' => $this->_idUsuario
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
        
}
?>

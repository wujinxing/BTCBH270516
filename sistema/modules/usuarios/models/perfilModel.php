<?php

class perfilModel extends Model {

    private $_usuario;
    private $_idPersona;
    private $_nombres;
    private $_direccion;
    private $_email;
    private $_telefono, $_notificacion;
    private $_fechaCumple, $_idIdioma; 
    private $_idRolDefault;

    public function __construct() {
        parent::__construct();
        $this->_set();
    }

    private function _set() {
        $this->_nombres = Functions::ucname(Formulario::getParam(PERF . 'txt_nombres'));
        $this->_direccion = Formulario::getParam(PERF . 'txt_direccion');
        $this->_email = Formulario::getParam(PERF . 'txt_email');
        $this->_telefono = Formulario::getParam(PERF . 'txt_telefonos');
        $this->_fechaCumple = Functions::cambiaf_a_mysql(Formulario::getParam(PERF.'txt_fechaNac'));
        $this->_idIdioma =  Aes::de(Formulario::getParam(PERF.'lst_idiomas'));
        $this->_idRolDefault =  Aes::de(Formulario::getParam(PERF.'lst_rol'));
        $this->_notificacion = (Formulario::getParam(PERF."chk_notificacion") == NULL)? 'N':'S';
        
        $this->_usuario = Session::get('sys_idUsuario');
        $this->_idPersona = Session::get('sys_idPersona');
    }

    public function findMiPerfil() {
        $query = "SELECT 
                    p.nombrecompleto,                                  
                    p.id_ubigeo,
                    p.direccion,
                    p.email,
                    p.sexo,
                    p.telefono,
                    p.fecha_cumple,
                    p.id_idioma,
                    u.notificacion,
                    u.rol_defecto,
                    u.`usuario`, p.`emailFB`, p.`codigo_pic`, p.link
                FROM mae_persona p
                    inner join mae_usuario u on p.id_persona = u.id_persona
            WHERE p.id_persona = :idPersona;";

        $parms = array(
            ':idPersona' => $this->_idPersona
        );

        $data = $this->queryOne($query, $parms);
        return $data;
    }

    public function updatePerfil() {        
       $query = "call sp_indexPerfilMantenimiento(
                    :flag,
                    :idPersona,
                    :nombres,
                    :direccion,
                    :email,
                    :telefono,
                    :fechacumple,
                    :idioma,
                    :noti,
                    :idRol,
                    :usuario
                );";
        $parms = array(
            ':flag' => 1,
            ':idPersona' => $this->_idPersona,
            ':nombres' => $this->_nombres,
            ':direccion' => $this->_direccion,
            ':email' => $this->_email,
            ':telefono' => $this->_telefono,
            ':fechacumple' => $this->_fechaCumple,
            ':idioma' => $this->_idIdioma,
            ':noti' => $this->_notificacion,
            ':idRol' => $this->_idRolDefault,
            ':usuario' => $this->_usuario            
        );
        $data = $this->queryOne($query,$parms);
        
        if ($data['result'] == '1'){
            Session::set('sys_nombreUsuario', $data['nombrecompleto']);                
            Session::set('sys_email', $this->_email);
            Session::set('sys_fono', $this->_telefono);
            Session::set('sys_direccion', $this->_direccion);
            Session::set('sys_notificacion', $this->_notificacion);
            Session::set('sys_defaultRol', $this->_idRolDefault);
        }                         
        return $data;                                                        
    }
    
    public function listarRolesUsuario() {
        $query = "SELECT u.`id_rol`, r.`rol`
            FROM `men_usuariorol` u
                    INNER JOIN `men_rol` r ON r.`id_rol` = u.`id_rol`
            WHERE u.`id_usuario` = :idUsu
            ORDER BY 2 ;";

        $parms = array(
            ':idUsu' => $this->_usuario
        );

        $data = $this->queryAll($query, $parms);
        return $data;
    }
    
  public function postEliminarFB(){
         $query = " call sp_clienteFacebookMantenimiento(:flag, "
                 . ":id, :nom, :sexo, :dire, :email, :fono, :pais, :fechnac, :idioma, "
                 . ":idFB, :linkFB, :ip, :usuario ) ;"; 
        $parms = array(
                ':flag'=> 4,
                ':id'=> $this->_idPersona,
                ':nom'=> '',
                ':sexo'=>  '',
                ':dire'=> '',
                ':email'=> '',
                ':fono'=>  '',
                ':pais'=> '',
                ':fechnac'=>  '',
                ':idioma'=> '', 
                ':idFB'=> '',
                ':linkFB'=> '',
                ':ip'=> $_SERVER["REMOTE_ADDR"],
                ':usuario'=> $this->_usuario
            );     
        $data = $this->queryOne($query,$parms);     
        return $data;    
        
    }    
    
}

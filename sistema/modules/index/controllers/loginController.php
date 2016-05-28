<?php
/*
 * Documento   : loginController
 * Creado      : 30-ene-2014, 19:25:17
 * Autor       : RDCC
 * Descripcion :
 */
class loginController extends Controller{
    
    public function __construct() {
        $this->loadModel(array('modulo'=>'index','modelo'=>'login'));
        $this->loadController(array('modulo' => 'usuarios', 'controller' => 'configurarUsuarios'));
    }

    public function index() {
        $data = Obj::run()->loginModel->getValidar();
  
        if(isset($data['id_usuario'])){
            Session::set('sys_claveAdmin', $data['claveadmin']);
            Session::set('sys_idUsuario', $data['id_usuario']);
            Session::set('sys_idPersona', $data['id_persona']);
            Session::set('sys_usuario', $data['usuario']);
            Session::set('sys_nombreUsuario', $data['nombrecompleto']);
            Session::set('sys_sexo', $data['sexo']);
            Session::set('sys_email', $data['email']);
            Session::set('sys_fono', $data['telefono']);
            Session::set('sys_tipoAcceso', 'N'); //N:Normal
            Session::set('sys_pic', $data['codigo_pic']);            
            Session::set('sys_tiempoBloqueo', $data['tiempo_bloqueo']); 
            Session::set('sys_idSucursal', $data['id_sucursal']);
            
            Obj::run()->loginModel->postLastLogin();
            /*los roles*/
            Session::set('sys_roles', Obj::run()->loginModel->getRoles());
            
            $rol = Session::get('sys_roles');            
            /*asignando rol por defecto*/
            if(empty($data['rol_defecto'])){
                $defaultRol = $rol[0]['id_rol'];
            }else{
                 $defaultRol = $data['rol_defecto'];
            }      
            Session::set('sys_defaultRol',$defaultRol);
            //Directorio de Archivos###########################################################
            $rr = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario');
            $targetPath = 'public'.DS.'files'.DS.Session::get('sys_idUsuario');
            if(!file_exists($targetPath)):
                    mkdir($targetPath);                
            endif;                
            //Limpiar Archivos temporales - En caso no se hayan eliminado.            
            $directorio = opendir($rr); //ruta actual
            while ($archivo = readdir($directorio)){
                if (!is_dir($archivo)){      
                    Functions::eliminarArchivo($rr.DS.$archivo);                                
                }
            }
            //###########################################################            
        }
        echo json_encode($data);
    }    
        
    public function forgotpassword(){
        Obj::run()->View->render('forgotpassword',false);
    }
        
    public function getClave() {
        $data = Obj::run()->loginModel->getValidarClave();  
        if(isset($data['result']) && $data['result'] == 1){
            Session::set('sys_claveAdmin', '0');                 
        }
        echo json_encode($data);
    }   
    
    public function logout(){
        //Directorio de Archivos###########################################################
        $rr = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario');
        $targetPath = 'public'.DS.'files'.DS.Session::get('sys_idUsuario');
        if(!file_exists($targetPath)):
                mkdir($targetPath);                
        endif;                
        //Limpiar Archivos temporales - En caso no se hayan eliminado.            
        $directorio = opendir($rr); //ruta actual
        while ($archivo = readdir($directorio)){
            if (!is_dir($archivo)){      
                Functions::eliminarArchivo($rr.DS.$archivo);                                
            }
        }
        //###########################################################      
        Session::destroy();
        $result = array('result' =>1);
        echo json_encode($result);
    }
    // Recuperar Contraseña
    public function postAcceso(){        
        $email = Formulario::getParam('txtUser');
        $data = Obj::run()->loginModel->getBuscarUsuario();     
        
        if ($data == false){
            $data = array('result' => 3);
            echo json_encode($data);
            return ;
        }
                
        //Extraer datos de Correo
        $idd = Aes::en($data['id_usuario']);
        $nombres = $data['nombrecompleto'];
        $idIdioma = $data['id_idioma'];
        
        //Envio Acceso:
        $data = Obj::run()->configurarUsuariosController->postNewEnvioAcceso($idd);
        
        $msj = Obj::run()->mensajesPlantillaController->getPlantillaMensaje('RECUPERA', $idIdioma);     
        $data = Obj::run()->parametroController->getParametros('EMAIL');        
        $data1 = Obj::run()->parametroController->getParametros('EMPR');        
        $emailEmpresa = $data['valor'];
        $empresa = $data1['valor'];        
        
        $body = str_replace('\\','',$msj['cuerpo'] );
        $body = htmlspecialchars_decode($body,ENT_QUOTES);
                
        $body = str_replace('{{NOMBRES}}',$nombres, $body);
        $body = str_replace('{{URL_ENLACE}}', BASE_URL . 'usuarios/configurarUsuarios/confirm/'.$idd, $body);
        $body = str_replace('{{EMAIL}}',$email, $body);
        $body = str_replace('{{ANIO}}', date('Y'), $body); 
        
        $mail = new PHPMailer(); // defaults to using php "mail()"
        //$mail->IsSMTP();    
        $mail->SetFrom($emailEmpresa, $empresa);
        $mail->AddAddress($email, $nombres);
        $mail->Subject = $msj['asunto'];
        $mail->MsgHTML($body);        
        $mail->CharSet = 'UTF-8';

        if ($mail->Send()) {
            $data = array('result' => 1);
        } else {
            $data = array('result' => 2);
        }

        echo json_encode($data);
    }   
       
    public function getFacebook($id) {
        $data = Obj::run()->loginModel->getFacebook($id);

        if(isset($data['id_usuario'])){       
            Session::set('sys_claveAdmin', $data['claveadmin']);
            Session::set('sys_idUsuario', $data['id_usuario']);
            Session::set('sys_idPersona', $data['id_persona']);
            Session::set('sys_usuario', $data['usuario']);
            Session::set('sys_nombreUsuario', $data['nombrecompleto']);
            Session::set('sys_sexo', $data['sexo']);
            Session::set('sys_email', $data['email']);    
            Session::set('sys_fono', $data['telefono']);
            Session::set('sys_tipoAcceso', 'F'); //F:Facebook
            Session::set('sys_pic', $data['codigo_pic']);    
            Session::set('sys_tiempoBloqueo', $data['tiempo_bloqueo']); 
            Session::set('sys_idSucursal', $data['id_sucursal']);
            
            Obj::run()->loginModel->postLastLogin();
            /*los roles*/
            Session::set('sys_roles', Obj::run()->loginModel->getRoles());
            
            $rol = Session::get('sys_roles');            
            
            /*asignando rol por defecto*/
            if(empty($data['rol_defecto'])){
                $defaultRol = $rol[0]['id_rol'];
            }else{
                 $defaultRol = $data['rol_defecto'];
            }      
            Session::set('sys_defaultRol',$defaultRol);
            //Directorio de Archivos###########################################################
            $rr = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario');
            $targetPath = 'public'.DS.'files'.DS.Session::get('sys_idUsuario');
            if(!file_exists($targetPath)):
                    mkdir($targetPath);                
            endif;                
            //Limpiar Archivos temporales - En caso no se hayan eliminado.            
            $directorio = opendir($rr); //ruta actual
            while ($archivo = readdir($directorio)){
                if (!is_dir($archivo)){      
                    Functions::eliminarArchivo($rr.DS.$archivo);                                
                }
            }
            //########################################################### 
        }
        
        return $data;    
    }            
    
}
?>

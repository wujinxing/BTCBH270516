<?php
/*
 * --------------------------------------
 * creado por:  RDCC
 * fecha: 03.01.2014
 * indexController.php
 * --------------------------------------
 */
class configurarUsuariosController extends Controller{
    
    public function __construct() {
        $this->loadModel(array('modulo' => 'usuarios', 'modelo' => 'configurarUsuarios'));        
        $this->loadController(array('modulo'=>'configuracion','controller'=>'mensajesPlantilla'));   
        $this->loadController(array('modulo'=>'configuracion','controller'=>'parametro'));   
    }

    public function index(){ 
        $main = Session::getMain("CUS");
        if($main["permiso"]){
            Obj::run()->View->render("indexUsuario");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }      
    }

    public function getUsuarios(){ 
        $editar = Session::getPermiso('CUSED');
        $eliminar = Session::getPermiso('CUSDE');
        $mail = Session::getPermiso('CUSEE');
        $baja = Session::getPermiso('CUSBJ');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->configurarUsuariosModel->getUsuarios();
        
        if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';
            foreach ( $rResult as $aRow ){
                
                if($aRow['estado'] == 'A'){
                    $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                }elseif($aRow['estado'] == 'B'){
                    $estado = '<span class=\"label label-danger\">'.LABEL_BAJA.'</span>';
                }elseif($aRow['estado'] == 'C'){
                    $estado = '<span class=\"label label-info\">'.LABEL_CREADO.'</span>';
                }elseif($aRow['estado'] == 'P'){
                    $estado = '<span class=\"label bg-color-teal txt-color-white\">'.LABEL_PENDIENTE.'</span>';
                }elseif($aRow['estado'] == 'S'){
                    $estado = '<span class=\"label bg-color-blueLight txt-color-white\">'.LABEL_SUSPENDIDO.'</span>';
                }
                
                /*datos de manera manual*/
                $sOutput .= '["'.$aRow['fecha_acceso'].'","'.$aRow['usuario'].'","'.$aRow['nombrecompleto'].'","'.$aRow['roles'].'","'.$estado.'", ';

                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_usuario']);
                $encryptIdPersona = Aes::en($aRow['id_persona']);
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $sOutput .= '"<div class=\"btn-group\">';
                
                if($editar['permiso'] == 1){
                    $sOutput .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"configurarUsuarios.getEditUsuario(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                if($eliminar['permiso'] == 1){
                    if ($aRow['sistema'] == 0){
                        $sOutput .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"configurarUsuarios.postDeleteUsuario(\''.$encryptReg.'\')\">';
                        $sOutput .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }else{
                       $sOutput .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                       $sOutput .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                       $sOutput .= '</button>';
                    }
                }
                if ($mail['permiso']) {
                    $sOutput .= '<button type=\"button\" class=\"'.$mail['theme'].'\" title=\"' . $mail['accion'] . '\" onclick=\"configurarUsuarios.getMensaje(this,\'' . $encryptReg . '\',\'' . $aRow['nombrecompleto'] . '\',\'' . $aRow['usuario'] . '\',\'' . Aes::en($aRow['id_idioma']) . '\')\">';
                    $sOutput .= '    <i class=\"'.$mail['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                if ($baja['permiso']) {
                    if ($aRow['estado'] !== 'B'){
                        $sOutput .= '<button type=\"button\" class=\"'.$baja['theme'].'\" title=\"' . $baja['accion'] . '\" onclick=\"configurarUsuarios.postBaja(this,\'' . $encryptReg . '\')\">';
                        $sOutput .= '    <i class=\"'.$baja['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }else{
                        $sOutput .= '<button type=\"button\" class=\"'.$baja['theme'].'\" title=\"' . $baja['accion'] . '\" disabled >';
                        $sOutput .= '    <i class=\"'.$baja['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }
                }
                                
                $sOutput .= ' </div>" ';

                $sOutput = substr_replace( $sOutput, "", -1 );
                $sOutput .= '],';
            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;
    }
    
    public function getNuevoUsuario(){ 
        $nuevo   = Session::getPermiso("CUSNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewUsuario");
        }
    }
    
    public function getEditUsuario(){
        $editar   = Session::getPermiso("CUSED");
        if($editar["permiso"]){     
           Obj::run()->View->render("formEditUsuario");
        }      
    }
    
    public function getViewMensaje(){ 
        $mail = Session::getPermiso('CUSEE');
        if($mail["permiso"]){     
            $idd = Formulario::getParam('_idUsuario');
            $idIdioma = Aes::de(Formulario::getParam('_idioma'));        
            $nombres = Formulario::getParam('_nombres');
            $email = Formulario::getParam('_mail');

            $msj = Obj::run()->mensajesPlantillaController->getPlantillaMensaje('ACCESO01', $idIdioma);                              
            $body = str_replace('\\','',$msj['cuerpo'] );
            $body = htmlspecialchars_decode($body,ENT_QUOTES);                
            $body = str_replace('{{NOMBRES}}',$nombres, $body);
            $body = str_replace('{{URL_ENLACE}}', BASE_URL . 'usuarios/configurarUsuarios/confirm/'.$idd, $body);
            $body = str_replace('{{EMAIL}}',$email, $body);
            $body = str_replace('{{ANIO}}', date('Y'), $body); 

            Obj::run()->View->mensaje = $body;        
            Obj::run()->View->asunto = $msj['asunto'];
            Obj::run()->View->grabar = Session::getPermiso('CUSEE');
            Obj::run()->View->onclick = 'configurarUsuarios.postAcceso("'.$idd.'","'.$nombres.'","'.$email.'")';         
            Obj::run()->View->render('formViewMensaje');
        }
    }
    
    public static function getUsuario(){ 
        $rResult = Obj::run()->configurarUsuariosModel->getUsuario();
        return $rResult;
    }
    
     public static function getUsuarioRender($idUsu){ 
        $rResult = Obj::run()->configurarUsuariosModel->getUsuarioRender($idUsu);
        return $rResult;
    }        
    
    public static function getRolesUser(){ 
        $rResult = Obj::run()->configurarUsuariosModel->getRolesUser();
        return $rResult;
    }
    
    public function getFormEmpleado(){ 
        $buscar = Session::getPermiso('CUSBS');
        if($buscar["permiso"]){   
            Obj::run()->View->render('buscarEmpleado');
        }
    }
    
    public static function getRoles(){
        $rResult = Obj::run()->configurarUsuariosModel->getRoles();
        return $rResult;
    }
    
    public function getEmpleados(){ 
        $tab = $this->post('_tab');
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->configurarUsuariosModel->getEmpleados();
        
        if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';
            foreach ( $rResult as $key=>$aRow ){
                /*antes de enviar id se encrypta*/                
                $encryptReg2 = Aes::en($aRow['id_persona']);
                
                $nom = '<a href=\"javascript:;\" onclick=\"simpleScript.setInput'
                        . '({'.$tab.'txt_persona:\''.$encryptReg2.'\','
                        . ''.$tab.'txt_idpersona:\''.$encryptReg2.'\','
                        . ''.$tab.'txt_personadesc:\''.$aRow['nombrecompleto'].'\','
                        . ''.$tab.'txt_email:\''.$aRow['email'].'\''
                        . '},'
                        . '\'#'.T4.'formBuscarEmpleado\');\" >'
                        . ''.$aRow['nombrecompleto'].'</a>';
                
                /*datos de manera manual*/
                $sOutput .= '["'.(++$key).'","'.$nom.'" ';

                $sOutput .= '],';
            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;
    }
    
    public function postNuevoUsuario(){
        $grabar = Session::getPermiso("CUSGR");
        if($grabar["permiso"]){    
             $data = Obj::run()->configurarUsuariosModel->mantenimientoUsuario();
            echo json_encode($data);
        }               
    }
    
    public function postEditarUsuario(){
        $editar = Session::getPermiso("CUSACT");
        if($editar["permiso"]){      
             $data = Obj::run()->configurarUsuariosModel->editarUsuario();
            echo json_encode($data);
        }
    }
    
    public function postDeleteUsuario(){
        $eliminar = Session::getPermiso('CUSDE');
        if($eliminar["permiso"]){   
            $data = Obj::run()->configurarUsuariosModel->deleteUsuario();        
            echo json_encode($data);
        }
    }
    
    public function postPass(){
        $data1 = Obj::run()->configurarUsuariosModel->postPass();
        
        if ($data1['result'] == '1'){
            $data = Obj::run()->configurarUsuariosModel->getValidarUsuario(); 
            if(isset($data['id_usuario'])){
                Session::set('sys_idUsuario', $data['id_usuario']);
                Session::set('sys_idPersona', $data['id_persona']);
                Session::set('sys_usuario', $data['usuario']);
                Session::set('sys_nombreUsuario', $data['nombrecompleto']);                
                Session::set('sys_sexo', $data['sexo']);
                Session::set('sys_email', $data['email']);
                Session::set('sys_fono', $data['telefono']);
                Session::set('sys_direccion', $data['direccion']);
                Session::set('sys_tiempoBloqueo', $data['tiempo_bloqueo']); 
                Session::set('sys_idSucursal', $data['id_sucursal']);
                Obj::run()->configurarUsuariosModel->postLastLogin();
                /*los roles*/
                Session::set('sys_roles', Obj::run()->configurarUsuariosModel->getRolesUsuario());
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
        }
        
        echo json_encode($data1);
    }
      
    public function postNewEnvioAcceso($idd){
        //Grabar Envio Acceso:
        $data = Obj::run()->configurarUsuariosModel->postNewEnvioAcceso($idd); 
        return $data;
    }

    /* Envio de E-mail de Acceso al Sistema: envia Link para cambiar contraseña */
    public function postAcceso() {
        $mail = Session::getPermiso('CUSEE');
        if($mail["permiso"]){
            $idd = Formulario::getParam('_idUsuario');
            //$idIdioma = Aes::de(Formulario::getParam('_idioma'));        
            $nombres = Formulario::getParam('_nombres');
            $email = Formulario::getParam('_mail');

            $body= htmlspecialchars_decode( Formulario::getParam(T4.'txt_mensaje'),ENT_QUOTES);
            $asunto = Formulario::getParam(T4.'txt_asunto');

            $data = Obj::run()->parametroController->getParametros('EMAIL');   
            $data1 = Obj::run()->parametroController->getParametros('EMPR');                                      
            $emailEmpresa = $data['valor'];
            $empresa = $data1['valor'];                

             //Envio Acceso:
            $data2 = $this->postNewEnvioAcceso($idd);

            $mail = new PHPMailer(); // defaults to using php "mail()"
            //$mail->IsSMTP();    
            $mail->SetFrom($emailEmpresa, $empresa);
            $mail->AddAddress($email, $nombres);
            $mail->Subject = $asunto;
            $mail->MsgHTML($body);        
            $mail->CharSet = 'UTF-8';

            /* validar si dominio de correo existe */
            if ($mail->Send()) {
                $data = array('result' => 1);
            } else {
                $data = array('result' => 2);
            }
            echo json_encode($data);
        }
    }

    /* llama html para actualizar clave - Esto lo hace el usuario final */
    public function confirm($id) {
        Obj::run()->View->idd = $id;          
        $data = configurarUsuariosController::getUsuarioRender( AesCtr::de($id));
        
        $fechaFinal = $data['fecha_final'];
        $estadoOld = $data['estado'];
        $usuario = $data['usuario'];
                
        Obj::run()->View->data = $data;
        Obj::run()->View->ffinal = $fechaFinal;
        Obj::run()->View->usuario = Aes::en($usuario);
                
        if ($estadoOld === 'P'){
            /*Validacion */
            if (date('Y-m-d H:i:s') < $fechaFinal){                  
               Obj::run()->View->render('newClavePersona', false);        
            }else{
                // Actualizar Envio como Suspendido                   
                Obj::run()->configurarUsuariosModel->_idUsuario = AesCtr::de($id);                
                Obj::run()->configurarUsuariosModel->_estado = 'S';
                Obj::run()->configurarUsuariosModel->postEditEnvioAcceso();                  
                Obj::run()->View->render('errorPage', false);
            }                 
        }else{
            Obj::run()->View->render('errorPage', false);
        }                                    
    }    
    
    public function postBaja() {
        $baja = Session::getPermiso('CUSBJ');
        if($baja["permiso"]){   
            $data = Obj::run()->configurarUsuariosModel->postBaja();
            echo json_encode($data);
        }
    }
        
}

?>

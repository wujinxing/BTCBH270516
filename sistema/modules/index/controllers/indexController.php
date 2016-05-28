<?php
/*
 * --------------------------------------
 * creado por:  RDCC
 * fecha: 03.01.2014
 * indexController.php
 * --------------------------------------
 */
class indexController extends Controller{
    
    public function __construct() {
        $this->loadModel(array('modulo'=>'index','modelo'=>'index'));
        $this->loadModel(array('modulo'=>'index','modelo'=>'login'));        
        $this->loadController(array('modulo'=>'configuracion','controller'=>'mensajesPlantilla'));  
        $this->loadController(array('modulo'=>'configuracion','controller'=>'parametro')); 
    }

   public function index(){
        /* Visualizar Login o Ventana Principal del Sistema */
//        Session::destroy();
        if(Session::get('sys_idUsuario')){  
            if (Session::get('sys_claveAdmin') == 0 ){
                Session::set('sys_menu', $this->getMenu());            
                Obj::run()->View->render('index',false, false, true);
            }else{
                Obj::run()->View->usuario = Session::get('sys_usuario');
                Obj::run()->View->nameUsuario = Session::get('sys_nombreUsuario');
                Obj::run()->View->sexo = Session::get('sys_sexo');                               
                Obj::run()->View->render('accesoClave',false);     
            }            
        }else{                       
            Obj::run()->View->render('login',false, true);
        }                 
    }
    
    public function getFormViewFoto(){ 
        Obj::run()->View->render('formViewFoto');
    } 

    public function getPanelVentas(){
        Obj::run()->View->render('panelVentas');
    } 
   
    public function getPanelAdmin(){
        Obj::run()->View->render('panelAdmin');
    }      
    
    public function getPanelEjecutivoVentas(){
        Obj::run()->View->render('panelEjecutivoVenta');
    }     
    
    public function getPanelCajero(){
        Obj::run()->View->render('panelCajero');
    }       
    
    private function getMenu(){
        return Obj::run()->loginModel->getMenu();
    }
    
    public function getChangeRol(){
        
        $idRol =  $this->post('_idRol');
         
        foreach (Session::get('sys_roles') as $value) {
            if($value['id_rol'] == $idRol){
                Session::set('sys_defaultRol', $value['id_rol']);
            }
        }
        $result = array('result'=> 1);
        echo json_encode($result);
    }
    
    public static function getAccionesOpcion($opcion){
        return Obj::run()->loginModel->getAccionesOpcion($opcion);
    }
    
    public static function getDominios(){
        Obj::run()->View->render('dominios');
    }
    
    public static function getOpcionesUser(){
        Obj::run()->View->render('opcionesUser');
    }   
    
    public static function getModulos($dominio=''){
        Obj::run()->View->dominio = $dominio; 
        Obj::run()->View->render('menu');
    }
    
    public function getLock(){                       
        Obj::run()->View->usuario = Session::get('sys_usuario');
        Obj::run()->View->nameUsuario = Session::get('sys_nombreUsuario');
        Obj::run()->View->tipoAcceso = Session::get('sys_tipoAcceso');        
        Obj::run()->View->sexo = Session::get('sys_sexo'); 
        Session::destroy();
        //vista, ajax, acceso FB, vinculacion FB                
        Obj::run()->View->render('lock',true,true);                    
    }
    
    public function errorPage(){
        Obj::run()->View->render('errorPage',false);
    }
  
    /* Pagina de mensajes al logearse con Facebook */
    public function msjFacebook(){
        Obj::run()->View->_tp = Obj::run()->Request->_argumentos[0];
        Obj::run()->View->render('msjFacebook',false);
        
    }
    
    public function changeFoto(){
        Obj::run()->View->render('formChangeFoto');
    }
   
    public function postFoto() {
        $p = Obj::run()->indexModel->_usuario;
        $fileOld = Obj::run()->indexModel->_fileFoto;
        
        if (!empty($_FILES)) {
            $targetPath = ROOT . 'public' . DS .'img' .DS . 'fotos' . DS;
            $tempFile = $_FILES['file']['tmp_name'];
                        
            /*Buscar Foto anterior*/
            $targetFileEliminar = $targetPath . $fileOld;  
            if(!empty($fileOld)){
                Functions::eliminarArchivo($targetFileEliminar);   
            }
            /*Crear nombre de Foto*/
            $tipo = '.' . end(explode('.',$_FILES["file"]['name']));     
            $file = $p . '_' . Functions::nombreAleatorio() . $tipo;                                 
            $targetFile = $targetPath.$file;                
            if (move_uploaded_file($tempFile, $targetFile)) {
               $array = array("img" => $targetPath, 'archivo'=>$file, 'fel'=>$targetFileEliminar);
                /* Actualizar Foto en BD */
                Obj::run()->indexModel->postFoto($file);
               /* Imagen Ajustes */
                $this->getLibrary('Thumbnail/Thumbnail');
                $thumb = new thumbnail($targetFile);  
                $thumb->size_auto(200);
                $thumb->jpeg_quality(IMG_CALIDAD);
                $thumb->save($targetFile);                                           
            }
            echo json_encode($array);
        }
    }    
    
    public function deleteImagen() {
        $data = Obj::run()->indexModel->postFoto('');
        
        $file = Formulario::getParam('_doc');
        
        //$file = str_replace("/","\\", $file);
        
        $targetPath =  $file;
        if(!empty($file)){
            Functions::eliminarArchivo($targetPath);   
        }
        echo json_encode($data);
    }      
    
    public function deleteArchivo($c = ''){
        if ($c == '') $c = Formulario::getParam('_archivo');                    
                
        $filename = ROOT.'public'.DS.'files'.DS.$c;
        
        if(!empty($c)){
            Functions::eliminarArchivo($filename);               
        }
        echo $filename;
    }        
       
}

?>

<?php
class cambiarClaveController extends Controller{
    
    public function __construct() {
        $this->loadModel('cambiarClave');        
    }

    public function index(){ 
        $main = Session::getMain("CLAV");
        if($main["permiso"]){
            Obj::run()->View->render("indexCambiarClave");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta ventana.";
        }        
    }
    
    public function postCambiarClave(){        
        $editar = Session::getPermiso('CLAVACT');
        if ($editar['permiso']){
            $data = Obj::run()->cambiarClaveModel->cambiarClave();        
            echo json_encode($data);
        }        
    }
    
}
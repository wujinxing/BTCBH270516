<?php
class claveAdmController extends Controller{
    
    public function __construct() {
        $this->loadModel('claveAdm');        
    }

    public function index(){ 
        $main = Session::getMain("CLADM");
        if($main["permiso"]){
            Obj::run()->View->render("indexClaveAdm");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta ventana.";
        }        
    }
    
    public function postCambiarClave(){
        $editar = Session::getPermiso('CLADMACT');
        if ($editar['permiso']){
            $data = Obj::run()->claveAdmModel->cambiarClave();        
            echo json_encode($data);
        }        
    }
    
}
<?php

class perfilController extends Controller{
    
    public function __construct() {
        $this->loadModel(array('modulo' => 'usuarios', 'modelo' => 'perfil'));        
        $this->loadController(array('modulo'=>'configuracion','controller'=>'idiomas'));         
    }

    public function index(){ 
        $main = Session::getMain("PERF");
        if($main["permiso"]){
            Obj::run()->View->render("indexPerfil");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }      
    }
    
    public function findMiPerfil(){
        $data = Obj::run()->perfilModel->findMiPerfil();
        
        return $data;
    }
    
    public function postPerfil(){
        $editar   = Session::getPermiso('PERFACT');
        if($editar["permiso"]){
            $data = Obj::run()->perfilModel->updatePerfil();           
            echo json_encode($data);
        }       
    }
    public function postEliminarFB(){
        $eliminar = Session::getPermiso('PERFDE');
        if($eliminar["permiso"]){   
            $data = Obj::run()->perfilModel->postEliminarFB();
            if ($data['result'] == 1){
                 Session::set('sys_pic', 'X');
            }
            echo json_encode($data);
        }       
    }  
    
    public static function listarRolesUsuario(){
        $data = Obj::run()->perfilModel->listarRolesUsuario();
        
        return $data;
    }    
    
}
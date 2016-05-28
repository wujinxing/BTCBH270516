<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 16:12:08 
* Descripcion : paisController.php
* ---------------------------------------
*/    

class paisController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'pais'));
        $this->loadController(array('modulo'=>'configuracion','controller'=>'moneda')); 
        $this->loadController(array('modulo'=>'configuracion','controller'=>'idiomas')); 
    }
    
    public function index(){ 
        $main = Session::getMain("UBIG");
        if($main["permiso"]){
            Obj::run()->View->render("indexUbigeo");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }       
        
    }       
    
    /*carga formulario (newPais.phtml) para nuevo registro: Pais*/
    public function getFormNewPais(){
        $nuevo   = Session::getPermiso("UBIGNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewPais");
        }
        
    }
    
    /*carga formulario (editPais.phtml) para editar registro: Pais*/
    public function getFormEditPais(){
        $editar   = Session::getPermiso("UBIGED");
        if($editar["permiso"]){     
           Obj::run()->View->render("formEditPais");
        }        
    }
    
    /*busca data para editar registro: Pais*/
    public static function findPais(){
        $data = Obj::run()->paisModel->findPais();
            
        return $data;
    }
    
    /*envia datos para grabar registro: Pais*/
    public function postNewPais(){
        $grabar = Session::getPermiso("UBIGGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->paisModel->newPais();
            echo json_encode($data);
        }                
    }
    
    /*envia datos para editar registro: Pais*/
    public function postEditPais(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->paisModel->editPais();
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: Pais*/
    public function postDeletePais(){
        $eliminar = Session::getPermiso("UBIGDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->paisModel->deletePais();
            echo json_encode($data);
        }
    }      
    
    public static function listarPais(){
        $data = Obj::run()->paisModel->listarPais();
        return $data;
    } 
    
    public function postDesactivar(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->paisModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->paisModel->postActivar();
            echo json_encode($data);
        }        
    }           
    
}

?>
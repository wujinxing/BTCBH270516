<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 16:12:08 
* Descripcion : departamentoController.php
* ---------------------------------------
*/    

class departamentoController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'departamento'));
        $this->loadController(array('modulo'=>'configuracion','controller'=>'pais')); 
    }
    
    public function index(){ 
        $main = Session::getMain("UBIG");
        if($main["permiso"]){
            Obj::run()->View->render("indexUbigeo");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }           
    }       
    
    /*carga formulario (newDepartamento.phtml) para nuevo registro: Departamento*/
    public function getFormNewDepartamento(){
        $nuevo   = Session::getPermiso("UBIGNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewDepartamento");
        }
    }
    
    /*carga formulario (editDepartamento.phtml) para editar registro: Departamento*/
    public function getFormEditDepartamento(){
        $editar   = Session::getPermiso("UBIGED");
        if($editar["permiso"]){     
           Obj::run()->View->render("formEditDepartamento");
        }        
    }
    
    /*busca data para editar registro: Departamento*/
    public static function findDepartamento(){
        $data = Obj::run()->departamentoModel->findDepartamento();
            
        return $data;
    }
    
    /*envia datos para grabar registro: Departamento*/
    public function postNewDepartamento(){
        $grabar = Session::getPermiso("UBIGGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->departamentoModel->newDepartamento();
            echo json_encode($data);
        }        
    }
    
    /*envia datos para editar registro: Departamento*/
    public function postEditDepartamento(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->departamentoModel->editDepartamento();
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: Departamento*/
    public function postDeleteDepartamento(){
        $eliminar = Session::getPermiso("UBIGDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->departamentoModel->deleteDepartamento();
            echo json_encode($data);
        }
    }      
        
    public static function listarPais(){
        $data = Obj::run()->paisController->listarPais();
        return $data;
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->departamentoModel->postDesactivar();
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->departamentoModel->postActivar();
            echo json_encode($data);
        }
    }               
}

?>
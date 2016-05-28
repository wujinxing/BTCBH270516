<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 19-11-2014 22:11:59 
* Descripcion : reporteVentaDiaController.php
* ---------------------------------------
*/    

class reporteVentaDiaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'reporteVentaDia'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'cajaCierre'));
    }
    
    public function index(){ 
        Obj::run()->View->render("indexReporteVentaDia");
    }
    
    public static function getDataCajaAll() {
       $data = Obj::run()->reporteVentaDiaModel->getDataCajaAll();
       return $data;        
    }
    
    public function getFormDatosCaja(){              
        Obj::run()->View->render('formDataCajaDia');        
    }        
   
    public function postPDF(){
       Obj::run()->cajaCierreController->setCaja(Obj::run()->reporteVentaDiaModel->_idCaja);
       $data = Obj::run()->cajaCierreController->postPDF();
       return $data;        
    }
    
    public function postExcel(){
       Obj::run()->cajaCierreController->setCaja(Obj::run()->reporteVentaDiaModel->_idCaja);
       $data = Obj::run()->cajaCierreController->postPDF();
       return $data;        
    }    
}

?>
<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 12-09-2014 17:09:12 
* Descripcion : plantillaDocController.php
* ---------------------------------------
*/    

class plantillaDocController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'plantillaDoc'));
    }
    
    public function index(){ 
        $main = Session::getMain("PLTDC");
        if($main["permiso"]){
            Obj::run()->View->render("indexPlantillaDoc");   
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }          
    }
    
    public function getGridPlantillaDoc(){
        $editar   = Session::getPermiso('PLTDCED');        
        $clonar   = Session::getPermiso('PLTDCCL');
        $anular   = Session::getPermiso('PLTDCDE');                
        $sEcho    =   $this->post('sEcho');
        
        $rResult = Obj::run()->plantillaDocModel->getPlantillaDoc();
        
        $num = Obj::run()->plantillaDocModel->_iDisplayStart;
        if($num >= 10){
            $num++;
        }else{
            $num = 1;
        }
        
        if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';     
            
            foreach ( $rResult as $key=>$aRow ){
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_plantilla']);
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"plantillaDoc.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-warning btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"plantillaDoc.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-warning\">'.LABEL_DESACT.'</span>';
                    }
                }     
                                                                                                                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"plantillaDoc.getFormEditPlantillaDoc(\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($clonar['permiso'] ){
                    $axion .= '<button type=\"button\" class=\"'.$clonar['theme'].'\" title=\"'.$clonar['accion'].'\" onclick=\"plantillaDoc.getClonar(\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$clonar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($anular['permiso'] ){
                    $axion .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" onclick=\"plantillaDoc.postDeletePlantillDoc(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$anular['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['nombre'].'","'.$aRow['alias'].'","'.$aRow['fecha_creacion'].'","'.$estado.'",'.$axion.' ';
                
                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newPlantillaDoc.phtml) para nuevo registro: PlantillaDoc*/
    public function getFormNewPlantillaDoc(){
        $nuevo   = Session::getPermiso("PLTDCNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewPlantillaDoc");
        }
    }
    
    /*carga formulario (editPlantillaDoc.phtml) para editar registro: PlantillaDoc*/
    public function getFormEditPlantillaDoc(){
        $editar   = Session::getPermiso("PLTDCED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditPlantillaDoc");
        }
    }
    
    public function getFormClonarPlantillaDoc(){
        $clonar   = Session::getPermiso('PLTDCCL');
        if($clonar["permiso"]){     
            Obj::run()->View->render("formClonarPlantillaDoc");
        }
    }
        
    /*busca data para editar registro: PlantillaDoc*/
    public static function findPlantillaDoc(){
        $data = Obj::run()->plantillaDocModel->findPlantillaDoc();
            
        return $data;
    }
    
    /*envia datos para grabar registro: PlantillaDoc*/
    public function postNewPlantillaDoc(){
        $grabar = Session::getPermiso("PLTDCGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->plantillaDocModel->mantenimientoPlantillaDoc();      
            echo json_encode($data);
        }        
    }
    
    /*envia datos para editar registro: PlantillaDoc*/
    public function postEditPlantillaDoc(){
        $editar = Session::getPermiso("PLTDCACT");
        if($editar["permiso"]){      
            $data = Obj::run()->plantillaDocModel->mantenimientoPlantillaDoc();
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registros: PlantillaDoc*/
    public function postDeletePlantillaDoc(){
        $eliminar = Session::getPermiso("PLTDCDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->plantillaDocModel->mantenimientoPlantillaDoc();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar   = Session::getPermiso('PLTDCED');
        if($editar['permiso']){
            $data = Obj::run()->plantillaDocModel->postDesactivar();
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar   = Session::getPermiso('PLTDCED');
        if($editar['permiso']){
            $data = Obj::run()->plantillaDocModel->postDesactivar();
            echo json_encode($data);
        }
    }       
       
    public function getPlantillaDocumento($alias){
        $data = Obj::run()->plantillaDocModel->getPlantillaDocumento($alias);
        return $data;
    }    
}

?>
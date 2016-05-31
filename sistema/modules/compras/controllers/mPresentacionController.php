<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 30-05-2016 18:05:19 
* Descripcion : mPresentacionController.php
* ---------------------------------------
*/    

class mPresentacionController extends Controller{

    public function __construct() {
        $this->loadModel("mPresentacion");
    }
    
    public function index(){
        $main = Session::getMain("CPRES");
        if($main["permiso"]){
            Obj::run()->View->render("indexMPresentacion");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMPresentacion(){
        $editar   = Session::getPermiso('CPRESED');
        $eliminar = Session::getPermiso('CPRESDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mPresentacionModel->getMPresentacion();
        
        $num = Obj::run()->mPresentacionModel->_iDisplayStart;
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
            
            foreach ( $rResult as $aRow ){
                
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_presentacion']);
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"mPresentacion.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"mPresentacion.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-danger\">'.LABEL_DESACT.'</span>';
                    }
                }    
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mPresentacion.getFormEditMPresentacion(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mPresentacion.postDeleteMPresentacion(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                $c1 = addslashes($aRow['descripcion']);
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$c1.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMPresentacion.phtml) para nuevo registro: MPresentacion*/
    public function getFormNewMPresentacion(){
        $nuevo   = Session::getPermiso("CPRESNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMPresentacion");
        }
    }
    
    /*carga formulario (editMPresentacion.phtml) para editar registro: MPresentacion*/
    public function getFormEditMPresentacion(){
        $editar   = Session::getPermiso("CPRESED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMPresentacion");
        }
    }
    
    /*busca data para editar registro: MPresentacion*/
    public static function findMPresentacion(){
        $data = Obj::run()->mPresentacionModel->findMPresentacion();            
        return $data;
    }
    
    /*envia datos para grabar registro: MPresentacion*/
    public function postNewMPresentacion(){
        $grabar = Session::getPermiso("CPRESGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mPresentacionModel->newMPresentacion();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MPresentacion*/
    public function postEditMPresentacion(){
        $editar = Session::getPermiso("CPRESACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mPresentacionModel->editMPresentacion();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MPresentacion*/
    public function postDeleteMPresentacion(){
        $eliminar = Session::getPermiso("CPRESDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mPresentacionModel->deleteMPresentacion();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("CPRESACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mPresentacionModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("CPRESACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mPresentacionModel->postActivar();
            echo json_encode($data);
        }                
    }   
        
}

?>
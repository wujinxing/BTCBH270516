<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:51 
* Descripcion : mClasificacionController.php
* ---------------------------------------
*/    

class mClasificacionController extends Controller{

    public function __construct() {
        $this->loadModel("mClasificacion");
    }
    
    public function index(){
        $main = Session::getMain("CLASF");
        if($main["permiso"]){
            Obj::run()->View->render("indexMClasificacion");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMClasificacion(){
        $editar   = Session::getPermiso('CLASFED');
        $eliminar = Session::getPermiso('CLASFDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mClasificacionModel->getMClasificacion();
        
        $num = Obj::run()->mClasificacionModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_clasificacion']);
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"mClasificacion.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"mClasificacion.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mClasificacion.getFormEditMClasificacion(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    if($aRow['uso'] == 0):
                         $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mClasificacion.postDeleteMClasificacion(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                    else:
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    endif;
                }
                
                $axion .= ' </div>" ';
                $c1 = addslashes($aRow['descripcion']);
                $c2 = addslashes($aRow['descripcion_corta']);
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$c1.'","'.$c2.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMClasificacion.phtml) para nuevo registro: MClasificacion*/
    public function getFormNewMClasificacion(){
        $nuevo   = Session::getPermiso("CLASFNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMClasificacion");
        }
    }
    
    /*carga formulario (editMClasificacion.phtml) para editar registro: MClasificacion*/
    public function getFormEditMClasificacion(){
        $editar   = Session::getPermiso("CLASFED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMClasificacion");
        }
    }
    
    /*busca data para editar registro: MClasificacion*/
    public static function findMClasificacion(){
        $data = Obj::run()->mClasificacionModel->findMClasificacion();            
        return $data;
    }
    
    /*envia datos para grabar registro: MClasificacion*/
    public function postNewMClasificacion(){
        $grabar = Session::getPermiso("CLASFGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mClasificacionModel->newMClasificacion();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MClasificacion*/
    public function postEditMClasificacion(){
        $editar = Session::getPermiso("CLASFACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mClasificacionModel->editMClasificacion();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MClasificacion*/
    public function postDeleteMClasificacion(){
        $eliminar = Session::getPermiso("CLASFDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mClasificacionModel->deleteMClasificacion();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("CLASFACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mClasificacionModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("CLASFACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mClasificacionModel->postActivar();
            echo json_encode($data);
        }                
    }      
}

?>
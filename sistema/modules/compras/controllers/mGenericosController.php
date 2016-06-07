<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:34 
* Descripcion : mGenericosController.php
* ---------------------------------------
*/    

class mGenericosController extends Controller{

    public function __construct() {
        $this->loadModel("mGenericos");
    }
    
    public function index(){
        $main = Session::getMain("GENER");
        if($main["permiso"]){
            Obj::run()->View->render("indexMGenericos");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMGenericos(){
        $editar   = Session::getPermiso('GENERED');
        $eliminar = Session::getPermiso('GENERDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mGenericosModel->getMGenericos();
        
        $num = Obj::run()->mGenericosModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_generico']);
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"mGenericos.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"mGenericos.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mGenericos.getFormEditMGenericos(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    if($aRow['uso'] == 0):
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mGenericos.postDeleteMGenericos(this,\''.$encryptReg.'\')\">';
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
    
    /*carga formulario (newMGenericos.phtml) para nuevo registro: MGenericos*/
    public function getFormNewMGenericos(){
        $nuevo   = Session::getPermiso("GENERNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMGenericos");
        }
    }
    
    /*carga formulario (editMGenericos.phtml) para editar registro: MGenericos*/
    public function getFormEditMGenericos(){
        $editar   = Session::getPermiso("GENERED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMGenericos");
        }
    }
    
    /*busca data para editar registro: MGenericos*/
    public static function findMGenericos(){
        $data = Obj::run()->mGenericosModel->findMGenericos();            
        return $data;
    }
    
    public static function findGenericosAll(){
        $data = Obj::run()->mGenericosModel->findGenericosAll();            
        return $data;
    }
    
    /*envia datos para grabar registro: MGenericos*/
    public function postNewMGenericos(){
        $grabar = Session::getPermiso("GENERGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mGenericosModel->newMGenericos();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MGenericos*/
    public function postEditMGenericos(){
        $editar = Session::getPermiso("GENERACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mGenericosModel->editMGenericos();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MGenericos*/
    public function postDeleteMGenericos(){
        $eliminar = Session::getPermiso("GENERDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mGenericosModel->deleteMGenericos();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("GENERACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mGenericosModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("GENERACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mGenericosModel->postActivar();
            echo json_encode($data);
        }                
    }   
       
        
}

?>
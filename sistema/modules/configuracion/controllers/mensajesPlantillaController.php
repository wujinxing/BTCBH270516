<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 09-12-2014 17:12:42 
* Descripcion : mensajesPlantillaController.php
* ---------------------------------------
*/    

class mensajesPlantillaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'mensajesPlantilla'));
        $this->loadController(array('modulo'=>'configuracion','controller'=>'idiomas'));
    }
    
    public function index(){ 
        $main = Session::getMain("PMSJ");
        if($main["permiso"]){
            Obj::run()->View->render("indexMensajesPlantilla");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }       
        
    }
    
    public function getGridMensajes(){
        $editar   = Session::getPermiso('PMSJED');
        $clonar   = Session::getPermiso('PMSJCL');
        $eliminar   = Session::getPermiso('PMSJDE');
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mensajesPlantillaModel->getMensajes();
        
        $num = Obj::run()->mensajesPlantillaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_mensaje']);
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"mensajesPlantilla.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"mensajesPlantilla.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-danger\">'.LABEL_DESACT.'</span>';
                    }
                }     
                
                $chk = '<input id=\"c_'.(++$key).'\" type=\"checkbox\" name=\"'.PMSJ.'chk_delete[]\" value=\"'.$encryptReg.'\">';
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mensajesPlantilla.getFormEditMensajes(\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($clonar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$clonar['theme'].'\" title=\"'.$clonar['accion'].'\" onclick=\"mensajesPlantilla.getFormClonarMensajes(\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$clonar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mensajesPlantilla.postDeleteMensajes(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                }                                    
                
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['asunto'].'","'.$aRow['alias'].'","'.$aRow['idioma'].'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMensajes.phtml) para nuevo registro: Mensajes*/
    public function getFormNewMensajes(){
        $nuevo   = Session::getPermiso("PMSJNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMensajes");
        }        
    }
    
    /*carga formulario (editMensajes.phtml) para editar registro: Mensajes*/
    public function getFormEditMensajes(){
        $editar   = Session::getPermiso("PMSJED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMensajes");
        }
    }

    /*carga formulario (editMensajes.phtml) para editar registro: Mensajes*/
    public function getFormClonarMensajes(){
        $clonar   = Session::getPermiso('PMSJCL');
        if($clonar["permiso"]){     
            Obj::run()->View->render("formClonarMensajes");
        }
        
    }
    
    /*busca data para editar registro: Mensajes*/
    public static function findMensajes(){
        $editar   = Session::getPermiso("PMSJED");
        if($editar["permiso"]){  
            $data = Obj::run()->mensajesPlantillaModel->findMensajes();
            return $data;
        }                    
        return $data;
    }
    
    /*envia datos para grabar registro: Mensajes*/
    public function postNewMensajes(){
        $grabar = Session::getPermiso("PMSJGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mensajesPlantillaModel->newMensajes();
            echo json_encode($data);
        }        
    }
    
    /*envia datos para editar registro: Mensajes*/
    public function postEditMensajes(){
        $editar = Session::getPermiso("PMSJACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mensajesPlantillaModel->editMensajes();
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registros: Mensajes*/
    public function postDeleteMensajes(){
        $eliminar = Session::getPermiso("PMSJDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mensajesPlantillaModel->deleteMensajes();
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar   = Session::getPermiso('PMSJED');
        if($editar['permiso']){
            $data = Obj::run()->mensajesPlantillaModel->postDesactivar();
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar   = Session::getPermiso('PMSJED');
        if($editar['permiso']){
            $data = Obj::run()->mensajesPlantillaModel->postActivar();
            echo json_encode($data);
        }        
    }    
    
    public function getPlantillaMensaje($alias, $idioma = 1){
        $data = Obj::run()->mensajesPlantillaModel->getPlantillaMensaje($alias, $idioma);
        return $data;
    }    
    
    public static function listarIdiomas(){
        $data = Obj::run()->idiomasController->listarIdiomas();
        return $data;
    }     
    
}

?>
<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 30-05-2016 17:05:25 
* Descripcion : mLaboratoriosController.php
* ---------------------------------------
*/    

class mLaboratoriosController extends Controller{

    public function __construct() {
        $this->loadModel("mLaboratorios");
    }
    
    public function index(){
        $main = Session::getMain("RELAB");
        if($main["permiso"]){
            Obj::run()->View->render("indexMLaboratorios");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMLaboratorios(){
        $editar   = Session::getPermiso('RELABED');
        $eliminar = Session::getPermiso('RELABDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mLaboratoriosModel->getMLaboratorios();
        
        $num = Obj::run()->mLaboratoriosModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_laboratorio']);
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"mLaboratorios.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"mLaboratorios.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mLaboratorios.getFormEditMLaboratorios(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                     if($aRow['uso'] == 0):
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mLaboratorios.postDeleteMLaboratorios(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    else:
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled>';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    endif;
                }
                
                $axion .= ' </div>" ';
                
                $c1 = $aRow['descripcion'];
                $c2 = $aRow['sigla'];
                
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
    
    /*carga formulario (newMLaboratorios.phtml) para nuevo registro: MLaboratorios*/
    public function getFormNewMLaboratorios(){
        $nuevo   = Session::getPermiso("RELABNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMLaboratorios");
        }
    }
    
    /*carga formulario (editMLaboratorios.phtml) para editar registro: MLaboratorios*/
    public function getFormEditMLaboratorios(){
        $editar   = Session::getPermiso("RELABED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMLaboratorios");
        }
    }
    
    /*busca data para editar registro: MLaboratorios*/
    public static function findMLaboratorios(){
        $data = Obj::run()->mLaboratoriosModel->findMLaboratorios();            
        return $data;
    }
    
    /*envia datos para grabar registro: MLaboratorios*/
    public function postNewMLaboratorios(){
        $grabar = Session::getPermiso("RELABGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mLaboratoriosModel->newMLaboratorios();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MLaboratorios*/
    public function postEditMLaboratorios(){
        $editar = Session::getPermiso("RELABACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mLaboratoriosModel->editMLaboratorios();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MLaboratorios*/
    public function postDeleteMLaboratorios(){
        $eliminar = Session::getPermiso("RELABDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mLaboratoriosModel->deleteMLaboratorios();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("RELABACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mLaboratoriosModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("RELABACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mLaboratoriosModel->postActivar();
            echo json_encode($data);
        }                
    }     
        
}

?>
<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 31-05-2016 00:05:31 
* Descripcion : mFamiliaController.php
* ---------------------------------------
*/    

class mFamiliaController extends Controller{

    public function __construct() {
        $this->loadModel("mFamilia");
    }
    
    public function index(){
        $main = Session::getMain("FAM");
        if($main["permiso"]){
            Obj::run()->View->render("indexMFamilia");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMFamilia(){
        $editar   = Session::getPermiso('FAMED');
        $eliminar = Session::getPermiso('FAMDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mFamiliaModel->getMFamilia();
        
        $num = Obj::run()->mFamiliaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_familia']);
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"mFamilia.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"mFamilia.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mFamilia.getFormEditMFamilia(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    if($aRow['uso'] == 0):
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mFamilia.postDeleteMFamilia(this,\''.$encryptReg.'\')\">';
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
    
    /*carga formulario (newMFamilia.phtml) para nuevo registro: MFamilia*/
    public function getFormNewMFamilia(){
        $nuevo   = Session::getPermiso("FAMNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMFamilia");
        }
    }
    
    /*carga formulario (editMFamilia.phtml) para editar registro: MFamilia*/
    public function getFormEditMFamilia(){
        $editar   = Session::getPermiso("FAMED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMFamilia");
        }
    }
    
    /*busca data para editar registro: MFamilia*/
    public static function findMFamilia(){
        $data = Obj::run()->mFamiliaModel->findMFamilia();            
        return $data;
    }
    
    public static function findFamiliaAll(){
        $data = Obj::run()->mFamiliaModel->findFamiliaAll();            
        return $data;
    }    
    
    /*envia datos para grabar registro: MFamilia*/
    public function postNewMFamilia(){
        $grabar = Session::getPermiso("FAMGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mFamiliaModel->newMFamilia();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MFamilia*/
    public function postEditMFamilia(){
        $editar = Session::getPermiso("FAMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mFamiliaModel->editMFamilia();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MFamilia*/
    public function postDeleteMFamilia(){
        $eliminar = Session::getPermiso("FAMDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mFamiliaModel->deleteMFamilia();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("FAMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mFamiliaModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("FAMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mFamiliaModel->postActivar();
            echo json_encode($data);
        }                
    }   
          
}

?>
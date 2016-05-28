<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-04-2016 18:04:07 
* Descripcion : vSucursalController.php
* ---------------------------------------
*/    

class vSucursalController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'vSucursal'));
    }
    
    public function index(){ 
        $main = Session::getMain('SUCUR');
        if($main['permiso']){
            Obj::run()->View->render("indexVSucursal");        
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }            
    }
    
    public function getGridVSucursal(){
        $editar   = Session::getPermiso('SUCURED');
        $eliminar = Session::getPermiso('SUCURDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vSucursalModel->getVSucursal();
        
        $num = Obj::run()->vSucursalModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_sucursal']);
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"vSucursal.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"vSucursal.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"vSucursal.getFormEditVSucursal(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"vSucursal.postDeleteVSucursal(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['nombre'].'","'.$aRow['sigla'].'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newVSucursal.phtml) para nuevo registro: VSucursal*/
    public function getFormNewVSucursal(){
        $nuevo   = Session::getPermiso('SUCURNEW');
        if($nuevo['permiso']){     
            Obj::run()->View->render("formNewVSucursal");
        }
    }
    
    /*carga formulario (editVSucursal.phtml) para editar registro: VSucursal*/
    public function getFormEditVSucursal(){
        $editar   = Session::getPermiso('SUCURED');
        if($editar['permiso']){        
            Obj::run()->View->render("formEditVSucursal");
        }
    }
    
    /*busca data para editar registro: VSucursal*/
    public static function findVSucursal(){
        $data = Obj::run()->vSucursalModel->findVSucursal();
            
        return $data;
    }
        
    public static function getSucursalSigla($idd){
        $data = Obj::run()->vSucursalModel->getSucursalSigla($idd);            
        return $data;
    }
    
    /*envia datos para grabar registro: VSucursal*/
    public function postNewVSucursal(){
        $grabar = Session::getPermiso("SUCURGR");
        if($grabar['permiso']){
            $data = Obj::run()->vSucursalModel->newVSucursal();        
            echo json_encode($data);
        }        
    }
    
    /*envia datos para editar registro: VSucursal*/
    public function postEditVSucursal(){
        $editar = Session::getPermiso("SUCURACT");
        if($editar['permiso']){
            $data = Obj::run()->vSucursalModel->editVSucursal();      
            echo json_encode($data);
        }       
    }
    
    /*envia datos para eliminar registro: VSucursal*/
    public function postDeleteVSucursal(){
        $eliminar = Session::getPermiso("SUCURDE");
        if($eliminar['permiso']){
            $data = Obj::run()->vSucursalModel->deleteVSucursal();
            echo json_encode($data);
        }        
    }
       
    public static function getSucursalAll($permiso='N'){ 
        $data = Obj::run()->vSucursalModel->getSucursalAll($permiso);
        return $data;
    }   
    
    public static function getSucursalSiglaAll(){ 
        $data = Obj::run()->vSucursalModel->getSucursalSiglaAll();
        return $data;
    }      
    
    public function postDesactivar(){
        $editar   = Session::getPermiso('SUCURED');
        if($editar['permiso']){
            $data = Obj::run()->vSucursalModel->postDesactivar();        
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar   = Session::getPermiso('SUCURED');
        if($editar['permiso']){
            $data = Obj::run()->vSucursalModel->postActivar();        
            echo json_encode($data);
        }
    }          
}

?>
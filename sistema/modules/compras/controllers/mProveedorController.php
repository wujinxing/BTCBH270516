<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 08-06-2016 02:06:12 
* Descripcion : mProveedorController.php
* ---------------------------------------
*/    

class mProveedorController extends Controller{

    public function __construct() {
        $this->loadModel("mProveedor");
    }
    
    public function index(){
        $main = Session::getMain("PROVV");
        if($main["permiso"]){
            Obj::run()->View->render("indexMProveedor");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMProveedor(){
        $editar   = Session::getPermiso('PROVVED');
        $eliminar = Session::getPermiso('PROVVDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mProveedorModel->getMProveedor();
        
        $num = Obj::run()->mProveedorModel->_iDisplayStart;
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
                
                /*campo que maneja los estados, para el ejemplo aqui es ACTIVO, coloca tu campo*/
                if($aRow['estado'] == 1){
                    $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                }else{
                    $estado = '<span class=\"label label-danger\">'.LABEL_DESACT.'</span>';
                }
                
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['ID_REGISTRO']);
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mProveedor.getFormEditMProveedor(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mProveedor.postDeleteMProveedor(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['CAMPO 1'].'","'.$aRow['CAMPO 2'].'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMProveedor.phtml) para nuevo registro: MProveedor*/
    public function getFormNewMProveedor(){
        $nuevo   = Session::getPermiso("PROVVNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMProveedor");
        }
    }
    
    /*carga formulario (editMProveedor.phtml) para editar registro: MProveedor*/
    public function getFormEditMProveedor(){
        $editar   = Session::getPermiso("PROVVED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMProveedor");
        }
    }
    
    /*busca data para editar registro: MProveedor*/
    public static function findMProveedor(){
        $data = Obj::run()->mProveedorModel->findMProveedor();            
        return $data;
    }
    
    /*envia datos para grabar registro: MProveedor*/
    public function postNewMProveedor(){
        $grabar = Session::getPermiso("PROVVGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mProveedorModel->newMProveedor();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MProveedor*/
    public function postEditMProveedor(){
        $editar = Session::getPermiso("PROVVACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mProveedorModel->editMProveedor();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MProveedor*/
    public function postDeleteMProveedor(){
        $eliminar = Session::getPermiso("PROVVDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mProveedorModel->deleteMProveedor();        
            echo json_encode($data);
        }
    }
        
}

?>
<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-04-2016 16:04:52 
* Descripcion : metodoPagoController.php
* ---------------------------------------
*/    

class metodoPagoController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'metodoPago'));
    }
    
    public function index(){
        $main = Session::getMain("MEPAG");
        if($main["permiso"]){
            Obj::run()->View->render("indexMetodoPago");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMetodoPago(){
        $editar   = Session::getPermiso('MEPAGED');
        $eliminar = Session::getPermiso('MEPAGDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->metodoPagoModel->getMetodoPago();
        
        $num = Obj::run()->metodoPagoModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_metodopago']);
                
                 if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"metodoPago.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"metodoPago.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"metodoPago.getFormEditMetodoPago(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    if($aRow['uso'] == 0):
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"metodoPago.postDeleteMetodoPago(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    else:
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled>';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    endif;                    
                }
                
                $axion .= ' </div>" ';
                $icono = '<i class=\"'.$aRow['icono'].' fa-2x \" ></i>';
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['descripcion'].'","'.$icono.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMetodoPago.phtml) para nuevo registro: MetodoPago*/
    public function getFormNewMetodoPago(){
        $nuevo   = Session::getPermiso("MEPAGNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMetodoPago");
        }
    }
    
    /*carga formulario (editMetodoPago.phtml) para editar registro: MetodoPago*/
    public function getFormEditMetodoPago(){
        $editar   = Session::getPermiso("MEPAGED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMetodoPago");
        }
    }
    
    /*busca data para editar registro: MetodoPago*/
    public static function findMetodoPago(){
        $data = Obj::run()->metodoPagoModel->findMetodoPago();            
        return $data;
    }
    
    /*Mostrar metodo de pago para usar en otras ventanas */
    public static function findMetodoPagoAll(){
        $data = Obj::run()->metodoPagoModel->findMetodoPagoAll();            
        return $data;
    }
    
    /*envia datos para grabar registro: MetodoPago*/
    public function postNewMetodoPago(){
        $grabar = Session::getPermiso("MEPAGGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->metodoPagoModel->newMetodoPago();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MetodoPago*/
    public function postEditMetodoPago(){
        $editar = Session::getPermiso("MEPAGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->metodoPagoModel->editMetodoPago();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MetodoPago*/
    public function postDeleteMetodoPago(){
        $eliminar = Session::getPermiso("MEPAGDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->metodoPagoModel->deleteMetodoPago();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar = Session::getPermiso("MEPAGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->metodoPagoModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("MEPAGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->metodoPagoModel->postActivar();
            echo json_encode($data);
        }                
    }     
}

?>
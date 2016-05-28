<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 09-12-2014 16:12:55 
* Descripcion : monedaController.php
* ---------------------------------------
*/    

class monedaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'moneda'));
    }
    
    public function index(){ 
        $main = Session::getMain("MOND");
        if($main["permiso"]){
            Obj::run()->View->render("indexMoneda");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }          
        
    }
    
    public function getGridMoneda(){
        $editar   = Session::getPermiso('MONDED');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->monedaModel->getMoneda();
        
        $num = Obj::run()->monedaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_moneda']);
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"moneda.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-warning btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"moneda.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-warning\">'.LABEL_DESACT.'</span>';
                    }
                }     
                
                $chk = '<input id=\"c_'.(++$key).'\" type=\"checkbox\" name=\"'.MOND.'chk_delete[]\" value=\"'.$encryptReg.'\">';
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"moneda.getFormEditMoneda(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['descripcion'].'","'.$aRow['sigla'].'","'.$aRow['fecha_creacion'].'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMoneda.phtml) para nuevo registro: Moneda*/
    public function getFormNewMoneda(){
        $nuevo   = Session::getPermiso("MONDNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMoneda");
        }
    }
    
    /*carga formulario (editMoneda.phtml) para editar registro: Moneda*/
    public function getFormEditMoneda(){
        $editar   = Session::getPermiso("MONDED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMoneda");
        }
    }
    
    /*busca data para editar registro: Moneda*/
    public static function findMoneda(){
        $editar   = Session::getPermiso("MONDED");
        if($editar["permiso"]){  
            $data = Obj::run()->monedaModel->findMoneda();
            return $data;
        }
    }
    
    /*envia datos para grabar registro: Moneda*/
    public function postNewMoneda(){
        $grabar = Session::getPermiso("MONDGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->monedaModel->newMoneda();
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: Moneda*/
    public function postEditMoneda(){
        $editar = Session::getPermiso("MONDACT");
        if($editar["permiso"]){      
            $data = Obj::run()->monedaModel->editMoneda();
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: Moneda*/
    public function postDeleteMoneda(){
        $eliminar = Session::getPermiso("MONDDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->monedaModel->deleteMoneda();
            echo json_encode($data);
        }
    }
        
    public function postDesactivar(){
        $editar   = Session::getPermiso('MONDED');
        if($editar['permiso']){
            $data = Obj::run()->monedaModel->postDesactivar();
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar   = Session::getPermiso('MONDED');
        if($editar['permiso']){
            $data = Obj::run()->monedaModel->postActivar();
            echo json_encode($data);
        }
    }           
    
    public static function listarMoneda(){
        $data = Obj::run()->monedaModel->listarMoneda();        
        return $data;
    }    
}

?>
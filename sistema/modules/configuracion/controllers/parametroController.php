<?php
/*
* --------------------------------------
* fecha: 07-08-2014 02:08:17 
* Descripcion : parametroController.php
* --------------------------------------
*/    

class parametroController extends Controller{

    public function __construct() {
         $this->loadModel(array('modulo'=>'configuracion','modelo'=>'parametro'));
    }
    
    public function index(){ 
        $main = Session::getMain("PARM");
        if($main["permiso"]){
            Obj::run()->View->render('indexParametro');
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }       
    }
    
    public function getGridParametro(){
        $editar = Session::getPermiso('PARMED');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->parametroModel->getGridParametro();
        $num = Obj::run()->parametroModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_parametro']);

               if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"parametro.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"parametro.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-danger\">'.LABEL_DESACT.'</span>';
                    }
                }     
                $chk = '<input id=\"c_'.(++$key).'\" type=\"checkbox\" name=\"'.T100.'chk_delete[]\" value=\"'.$encryptReg.'\">';
                
                /*datos de manera manual*/
                $sOutput .= '["'.($num++).'","'.$aRow['nombre'].'","'.$aRow['valor'].'","'.$aRow['alias'].'","'.$estado.'", ';
                

                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $sOutput .= '"<div class=\"btn-group\">';
                
                if($editar['permiso'] == 1){
                    $sOutput .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"parametro.getEditarParametro(\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                
                $sOutput .= ' </div>" ';

                $sOutput = substr_replace( $sOutput, "", -1 );
                $sOutput .= '],';
            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;
    }
    public function getNuevoParametro(){ 
        $nuevo   = Session::getPermiso("PARMNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render('formNewParametro');
        }
    }
    
    public function getEditarParametro(){ 
        $editar   = Session::getPermiso("PARMED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditParametro");
        }        
    }
    
    public static function getParametro(){ 
        $data = Obj::run()->parametroModel->getParametro();
        
        return $data;
    }
    
    public function postNuevoParametro(){ 
        $grabar = Session::getPermiso("PARMGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->parametroModel->mantenimientoParametro();
            echo json_encode($data);
        }
    }
    
    public function postEditarParametro(){ 
        $editar = Session::getPermiso("PARMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->parametroModel->mantenimientoParametro();
            echo json_encode($data);
        }       
    }
    
    public function postDeleteParametro(){ 
        $eliminar = Session::getPermiso("PARMDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->parametroModel->mantenimientoParametro();     
            echo json_encode($data);
        }
    }
 
    public function postDesactivar(){
        $editar   = Session::getPermiso('PARMED');
        if($editar['permiso']){
            $data = Obj::run()->parametroModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar   = Session::getPermiso('PARMED');
        if($editar['permiso']){
            $data = Obj::run()->parametroModel->postDesactivar();
            echo json_encode($data);
        }     
    }        
    
    public function getParametros($p) {
        $data = Obj::run()->parametroModel->getParametros($p);
        return $data;
    }  
    
    public static function getParametroOne($p) {
        $data = Obj::run()->parametroModel->getParametros($p);
        return $data['valor'];
    }  
    
}

?>
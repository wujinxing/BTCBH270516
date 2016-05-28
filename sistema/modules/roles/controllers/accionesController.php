<?php
/*
 * Documento   : accionesController
 * Creado      : 05-jul-2014
 * Autor       : ..... .....
 * Descripcion : 
 */
class accionesController extends Controller{
    
    public function __construct() {
        $this->loadModel('acciones');
    }

    public function index(){}
    
    public function acciones(){
        $main = Session::getMain("ACC");
        if($main["permiso"]){
            Obj::run()->View->render("acciones");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }        
    }
    
    public function getAcciones(){ 
        $editar   = Session::getPermiso('ACCED');
        $eliminar = Session::getPermiso('ACCDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->accionesModel->getAcciones();
        
        if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';
            foreach ( $rResult as $aRow ){
                
                if($aRow['activo'] == 1){
                    $estado = '<span class=\"label label-success\">'.$aRow['estado'].'</span>';
                }else{
                    $estado = '<span class=\"label label-danger\">'.$aRow['estado'].'</span>';
                }
                $t='<button type=\"button\" class=\"'.$aRow['theme'].'\"><i class=\"'.$aRow['icono'].'\"></i></button>';
                /*datos de manera manual*/
                $sOutput .= '["'.$aRow['id_acciones'].'","'.$aRow['accion'].'","'.$t.'","'.$aRow['alias'].'","'.$estado.'", ';

                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_acciones']);

                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $sOutput .= '"<div class=\"btn-group\">';
                
                if($editar['permiso']){
                    $sOutput .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"acciones.getFormEditAccion(\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                if($eliminar['permiso']){
                    $sOutput .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"acciones.postDeleteAccion(\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
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
    
    public function getNuevaAccion(){
        $nuevo   = Session::getPermiso("ACCNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("nuevaAccion");
        }
    }
    
    public function getEditAccion(){ 
        $editar   = Session::getPermiso("ACCED");
        if($editar["permiso"]){     
            Obj::run()->View->key = $this->post('_key'); 
            Obj::run()->View->render('editarAccion');
        }        
    }
    
    public static function getAccion($id){ 
        $data = Obj::run()->accionesModel->getAccion($id);
        
        return $data;
    }
    
    public function postAccion(){ 
        $grabar = Session::getPermiso("ACCGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->accionesModel->mantenimientoAccion();
            echo json_encode($data);
        }         
    }
    
    public function postDeleteAccion(){ 
        $eliminar = Session::getPermiso("ACCDE");
        if($eliminar["permiso"]){      
            $data = Obj::run()->accionesModel->mantenimientoAccion();
            echo json_encode($data);
        }        
    }
    
}
?>

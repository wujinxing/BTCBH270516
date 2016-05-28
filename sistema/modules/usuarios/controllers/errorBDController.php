<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 23-11-2015 17:11:06 
* Descripcion : errorBDController.php
* ---------------------------------------
*/    

class errorBDController extends Controller{

    public function __construct() {
        $this->loadModel("errorBD");
    }
    
    public function index(){ 
        $main = Session::getMain("ERRBD");
        if($main["permiso"]){
            Obj::run()->View->render("indexErrorBD");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }      
    }
    
    public function getGridErrorBD(){
        $consultar   = Session::getPermiso('ERRBDCC');
        $eliminar = Session::getPermiso('ERRBDDE');
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->errorBDModel->getErrorBD();
        
        $num = Obj::run()->errorBDModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_error']);
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($consultar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$consultar['theme'].'\" title=\"'.$consultar['accion'].'\" onclick=\"errorBD.getFormEditErrorBD(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$consultar['icono'].'\"></i>';
                    $axion .= '</button>';
                }             
                
                if($eliminar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"errorBD.postDeleteErrorBD(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $fecha1 = new DateTime($aRow['fecha']);                               
                $c1 = $fecha1->format('d/m/Y h:i A');                                 
                $c2 = $aRow['ip'];
                
                $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
                $reemplazar=array(" ", " ", " ", " ");  
                $c3= str_ireplace($buscar,$reemplazar,$aRow['error']);                
                
                $sOutput .= '["'.($num++).'","'.$c1.'","'.$c2.'","'.$c3.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;
    }
        
    /*carga formulario (editErrorBD.phtml) para editar registro: ErrorBD*/
    public function getFormEditErrorBD(){
        Obj::run()->View->render("formEditErrorBD");
    }
    
    /*busca data para editar registro: ErrorBD*/
    public static function findErrorBD(){
        $consultar   = Session::getPermiso('ERRBDCC');
        if($consultar["permiso"]){
            $data = Obj::run()->errorBDModel->findErrorBD();            
            return $data;
        }
    }
       
    /*envia datos para eliminar registro: ErrorBD*/
    public function postDeleteErrorBD(){
        $eliminar = Session::getPermiso('ERRBDDE');
        if($eliminar["permiso"]){   
            $data = Obj::run()->errorBDModel->deleteErrorBD();       
            echo json_encode($data);
        }        
    }        
    
}

?>
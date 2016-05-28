<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 22-11-2014 20:11:18 
* Descripcion : vegresosController.php
* ---------------------------------------
*/    

class vegresosController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'vegresos'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vproducto')); 
        $this->loadController(array('modulo'=>'ventas','controller'=>'cajaApertura')); 
    }
    
    public function index(){ 
        $main = Session::getMain('VEGRE');
        if($main['permiso']){
            Obj::run()->View->render("indexVegresos");        
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }         
    }
    
    public function getGridVegresos(){
        $editar   = Session::getPermiso('VEGREED');
        $eliminar = Session::getPermiso('VEGREAN');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vegresosModel->getVegresos();
        
        $num = Obj::run()->vegresosModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_egreso']);
                
                switch($aRow['estado']){
                    case 'E':
                        $estado = '<span class=\"label label-default\">'.LABEL_EMITIDO.'</span>';
                        break;                  
                    case 'A':
                        $estado = '<span class=\"label label-danger\">'.LABEL_ANULADO.'</span>';
                        break;                 
                }
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                if($editar['permiso']){
                    if ($aRow['caja'] > 0){
                        $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"vegresos.getFormEditVegresos(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" disabled>';
                        $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }
                }
                if($eliminar['permiso']){
                     if( $aRow['estado'] !== 'A'){
                        if ($aRow['caja'] > 0){
                            $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"vegresos.postDeleteVegresos(this,\''.$encryptReg.'\')\">';
                            $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                            $axion .= '</button>';
                        }else{
                            $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled>';
                            $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                            $axion .= '</button>';
                        }
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled>';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }
                }                                                   
                $axion .= ' </div>" ';
                
                $beneficiario = '';
                if(($aRow['templeado'] == 'S' ) ){
                    $beneficiario = ' '.$aRow['empleado'];                   
                }elseif(($aRow['tproveedor'] == 'S' ) ){                  
                    $beneficiario = ' '.$aRow['proveedor'];                             
                }         
                if ($beneficiario !== ''){
                    $beneficiario = '  <span class=\"badge bg-color-pink font10 \"  >'.$beneficiario.'</span>';
                }
                $descripcion = preg_replace ('[\n|\r|\n\r]','',$aRow['descripcion']).$beneficiario;

                /*registros a mostrar*/
                $sOutput .= '["'.$aRow['id_egreso'].'","'.$aRow['sucursal'].'","'.$descripcion.'","'.Functions::cambiaf_a_normal($aRow['fecha']).'","'.$aRow['moneda'].' '.number_format($aRow['monto'],2).'","'.$estado.'", '.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newVegresos.phtml) para nuevo registro: Vegresos*/
    public function getFormNewVegresos(){
        $nuevo   = Session::getPermiso('VEGRENEW');
        if($nuevo['permiso']){     
            Obj::run()->View->render("formNewVegresos");
        }        
    }
    
    /*carga formulario (editVegresos.phtml) para editar registro: Vegresos*/
    public function getFormEditVegresos(){
        $editar   = Session::getPermiso('VEGREED');
        if($editar['permiso']){        
            Obj::run()->View->render("formEditVegresos");
        }
    }
    
    /*busca data para editar registro: Vegresos*/
    public static function findVegresos(){
        $data = Obj::run()->vegresosModel->findVegresos();
            
        return $data;
    }
    
    /*envia datos para grabar registro: Vegresos*/
    public function postNewVegresos(){
        $grabar = Session::getPermiso("VEGREGR");
        if($grabar['permiso']){
            $data = Obj::run()->vegresosModel->newVegresos();
            echo json_encode($data);
        }        
    }
    
    /*envia datos para editar registro: Vegresos*/
    public function postEditVegresos(){
        $editar = Session::getPermiso("VEGREACT");
        if($editar['permiso']){
            $data = Obj::run()->vegresosModel->editVegresos();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: Vegresos*/
    public function postDeleteVegresos(){
        $eliminar = Session::getPermiso("VEGREAN");
        if($eliminar['permiso']){
            $data = Obj::run()->vegresosModel->deleteVegresos();
            echo json_encode($data);
        }
    }    
       
    public static function getFindDataBeneficiarioAll($idSucursal){
        $data = Obj::run()->vegresosModel->findDataBeneficiarioAll($idSucursal);
            
        return $data;
    }  
    // Cuando se ejecuta desde onchange:
    public static function getFindDataBeneficiarioAllCombo(){
        $data = Obj::run()->vegresosModel->findDataBeneficiarioAll('');            
        echo json_encode($data);
    }         
    
}

?>
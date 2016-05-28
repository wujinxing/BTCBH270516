<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 10-05-2016 19:05:57 
* Descripcion : modificarVentaController.php
* ---------------------------------------
*/    

class modificarVentaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'modificarVenta'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'generarVenta'));        
    }
    
    public function index(){
        $main = Session::getMain("VMOVE");
        if($main["permiso"]){
            Obj::run()->View->render("indexModificarVenta");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridModificarVenta(){
        $editar   = Session::getPermiso('VMOVEMOD');
        $eliminar = Session::getPermiso('VMOVECAN');
        $exportarpdf   = Session::getPermiso('VMOVEEP');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->modificarVentaModel->getModificarVenta();
        
        $num = Obj::run()->modificarVentaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_docventa']);
                switch($aRow['estado']){
                    case 'E':
                        $estado = '<span class=\"label label-default\">'.LABEL_EMITIDO.'</span>';
                        break;                  
                    case 'A':
                        $estado = '<span class=\"label label-danger\">'.LABEL_ANULADO.'</span>';
                        break;   
                    case 'P':
                        $estado = '<span class=\"label label-warning\">'.LABEL_PAGADO.'</span>';
                        break;   
                    case 'M':
                        $estado = '<span class=\"label label-info\">'.LABEL_MODIFICADO.'</span>';
                        break;   
                    case 'I':
                        $estado = '<span class=\"label label-danger\">'.LABEL_INHABILITADO.'</span>';
                        break;  
                }
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                
                if($exportarpdf['permiso'] == 1){
                    $axion .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" onclick=\"modificarVenta.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($editar['permiso']){
                    //if( $aRow['estado'] !== 'M'){
                         if ($aRow['caja'] == 0){
                            $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"modificarVenta.getFormEditVenta(this,\''.$encryptReg.'\')\">';
                            $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                            $axion .= '</button>';
                        }else{
                            $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" disabled >';
                            $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                            $axion .= '</button>';
                        }
                    /*}else{
                        $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }*/
                }
                if($eliminar['permiso']){
                     if ($aRow['estado'] !== 'I'){
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"modificarVenta.getFormCancelarVenta(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';                     
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';  
                    }
                }
                
                $axion .= ' </div>" ';
                
                 $total = $aRow['moneda'].' '.number_format($aRow['monto_total_final'],2);                                              
                if($aRow['monto_saldo'] > 0 && $aRow['estado'] == 'E'){
                    $saldo = '<span class=\"badge bg-color-red\">'.$aRow['moneda'].' '.number_format($aRow['monto_saldo'],2).'</span>';
                }else{
                    $saldo = $aRow['moneda'].' '.number_format($aRow['monto_saldo'],2);
                }     
                $pagado = $aRow['moneda'].' '.number_format($aRow['monto_asignado'],2);
                
                $sucursal = $aRow['sucursal'];
                
                $idPersona = Aes::en($aRow['id_persona']);
                if((!empty($aRow['empresa']) || $aRow['empresa'] !== '' ) && (!empty($aRow['cliente']) || $aRow['cliente'] !== '' )){
                    $cliente = $aRow['empresa'].' | '.$aRow['cliente'];                   
                }else{
                     if(!empty($aRow['empresa']) || $aRow['empresa'] !== '' ){
                          $cliente = $aRow['empresa'];
                     }elseif (!empty($aRow['cliente']) || $aRow['cliente'] !== '' ){
                         $cliente = $aRow['cliente'];
                     }                   
                }    
                $nombre = '<a href=\"javascript:;\" onclick=\"persona.getDatosPersonales(\''.$idPersona.'\',\''.LABEL_DATOSCLIENTE.'\');\">'.Functions::textoLargo($cliente,45).'</a>';
                $nombre .= '<br /><span class=\"badge bg-color-blue font10 \"  >Creado por: '.$aRow['vendedor'].'</span>';
                $codigo = $aRow['id_docventa'];
                if (!empty($aRow['codigo_impresion']) ){
                    $codigo .= '<br /><span class=\"badge bg-color-orange font10 \"  >CI: '.$aRow['codigo_impresion'].'</span>';
                }   
                
                $sOutput .= '["'.$codigo.'","'.$nombre.'","'.$sucursal.'","'.  Functions::cambiaf_a_normal($aRow['fecha']).'","'.$total.'","'.$pagado.'","'.$saldo.'","'.$estado.'",'.$axion.' ';
                

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (editVenta.phtml) para editar registro: GenerarVenta*/
    public function getFormEditVenta(){
        Obj::run()->View->render("formEditGenerarVenta");
    }
    
    public function getFormCancelarVenta(){
        Obj::run()->View->render("formNewCancelarVenta");
    }
    
    public function postPDF(){
        $exportarpdf   = Session::getPermiso('VMOVEEP');        
        if($exportarpdf['permiso']){   
            $data = Obj::run()->generarVentaController->generarDocPDF();        
            return $data;
        }
    }
    
    public function postModificarVenta(){
        $grabar = Session::getPermiso('VMOVEMOD');
        if($grabar['permiso']){  
            $data = Obj::run()->modificarVentaModel->modificarGenerarVenta();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: GenerarVenta*/
    public function postCancelarVenta(){
        $editar = Session::getPermiso("VMOVEMOD");
        if($editar['permiso']){ 
            $data = Obj::run()->modificarVentaModel->cancelarVenta();
            echo json_encode($data);
        }
    }
        
}

?>
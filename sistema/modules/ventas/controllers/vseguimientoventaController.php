<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-11-2014 17:11:41 
* Descripcion : vseguimientoventaController.php
* ---------------------------------------
*/    

class vseguimientoventaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'vseguimientoventa'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'generarVenta'));           
    }
    
    public function index(){ 
        $main = Session::getMain('VSEVE');
        if($main['permiso']){
           Obj::run()->View->render("indexVseguimientoventa");      
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }                           
    }
    
    public function getFormIndexPagoVenta(){ 
        $pagar   = Session::getPermiso('VSEVEPG');        
        if($pagar['permiso']){     
            Obj::run()->View->render("indexPagoVenta");
        }
    }   
    
    public function getGridVseguimientoventa(){
        $pagar   = Session::getPermiso('VSEVEPG');
        $exportarpdf   = Session::getPermiso('VSEVEEP');
        $editar   = Session::getPermiso('VSEVEED');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vseguimientoventaModel->getVseguimientoventa();                
        
        $num = Obj::run()->vseguimientoventaModel->_iDisplayStart;
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
                 
                if($pagar['permiso'] ){
                    $axion .= '<button type=\"button\" class=\"'.$pagar['theme'].'\" title=\"'.$pagar['accion'].'\" onclick=\"vseguimientoventa.getIndexPago(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$pagar['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                     $axion .= '<button type=\"button\" class=\"'.$pagar['theme'].'\" title=\"'.$pagar['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$pagar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($exportarpdf['permiso'] == 1){
                    $axion .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" onclick=\"vseguimientoventa.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($editar['permiso']){
                    if( $aRow['estado'] !== 'M'){
                         if ($aRow['caja'] == 0){
                            $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"vseguimientoventa.postEditarGenerarVenta(this,\''.$encryptReg.'\')\">';
                            $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                            $axion .= '</button>';
                        }else{
                            $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" disabled >';
                            $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                            $axion .= '</button>';
                        }
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }
                }
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $total = $aRow['moneda'].' '.number_format($aRow['monto_total_final'],2);                                              
                if($aRow['monto_saldo'] > 0 && $aRow['estado'] == 'E'){
                    $saldo = '<span class=\"badge bg-color-red\">'.$aRow['moneda'].' '.number_format($aRow['monto_saldo'],2).'</span>';
                }else{
                    $saldo = $aRow['moneda'].' '.number_format($aRow['monto_saldo'],2);
                }     
                
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
                $sOutput .= '["'.$codigo.'","'.$nombre.'","'.$sucursal.'","'.  Functions::cambiaf_a_normal($aRow['fecha']).'","'.$total.'","'.$saldo.'","'.$estado.'",'.$axion.' ';
                
                $sOutput .= '],';
                
            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
   public function getGridPagoVenta(){
        
        $anular = Session::getPermiso('VSEVEAN');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vseguimientoventaModel->getPagoVenta();
        
        $num = Obj::run()->vseguimientoventaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_pago']);
                
                if($aRow['estado'] == 'E'){
                    $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.LABEL_EMITIDO.'\" ><i class=\"fa fa-clock-o\"></i> '.LABEL_EMITIDO.'</button>';
                }elseif($aRow['estado'] == 'A'){
                    $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.LABEL_ANULADO.'\" ><i class=\"fa fa-ban\"></i> '.LABEL_ANULADO.'</button>';
                }elseif($aRow['estado'] == 'M'){
                    $estado = '<button type=\"button\" class=\"btn btn-info btn-xs\" title=\"'.LABEL_MODIFICADO.'\" ><i class=\"fa fa-pencil\"></i> '.LABEL_MODIFICADO.'</button>';
                }elseif($aRow['estado'] == 'I'){
                    $estado = '<button type=\"button\" class=\"btn btn-info btn-xs\" title=\"'.LABEL_INHABILITADO.'\" ><i class=\"fa fa-times\"></i> '.LABEL_INHABILITADO.'</button>';
                }elseif($aRow['estado'] == 'P'){
                    $estado = '<button type=\"button\" class=\"btn btn-warning btn-xs\" title=\"'.LABEL_PAGADO.'\" ><i class=\"fa fa-money\"></i> '.LABEL_PAGADO.'</button>';
                }elseif($aRow['estado'] == 'R'){
                    $estado = '<button type=\"button\" class=\"btn btn-info btn-xs\" title=\"'.LABEL_PROGRAMADO.'\" ><i class=\"fa fa-clock-o\"></i> '.LABEL_PROGRAMADO.'</button>';
                }                           
     
                $axion = '"<div class=\"btn-group\">';
                
                if($anular['permiso']){
                    if ($aRow['estado'] !== 'A'){
                        if ($aRow['caja'] > 0){
                            $axion .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" onclick=\"vseguimientoventa.postAnularPago(this,\''.$encryptReg.'\')\">';
                            $axion .= '    <i class=\"'.$anular['icono'].'\"></i>';
                            $axion .= '</button>';
                        }else{
                            $axion .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" disabled>';
                            $axion .= '    <i class=\"'.$anular['icono'].'\"></i>';
                            $axion .= '</button>';
                        }
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$anular['icono'].'\"></i>';
                        $axion .= '</button>';
                    }                    
                }       
                
                $axion .= ' </div>" ';
                
                $f = new DateTime($aRow['fecha']);                               
                $c1 = $f->format('d/m/Y');              
                $c2 = $aRow['metodoPago'];
                $c4 = $aRow['moneda'].' '.number_format($aRow['monto_pagado'],2);
              
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$c1.'","'.$c2.'","'.$c4.'","'.$estado.'",'.$axion.'';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }          
    
    /*Formulario para pagar Cuenta - Saldo de Cliente*/
    public function getFormPagarVenta(){
        $nuevo   = Session::getPermiso('VSEVENEW');
        if($nuevo['permiso']){   
            Obj::run()->View->render("formNewPagarVenta");
        }
    }
    
    /*envia datos para editar registro: Vseguimientoventa*/
    public function postPagarVenta(){
        $grabar = Session::getPermiso("VSEVEGR");
        if($grabar['permiso']){   
            $data = Obj::run()->vseguimientoventaModel->newPagoVenta();       
            echo json_encode($data);
        }
    }
    
    public function postAnularPago(){
        $anular = Session::getPermiso('VSEVEAN');
        if($anular['permiso']){
            $data = Obj::run()->vseguimientoventaModel->anularPago();
            echo json_encode($data);
        }                      
    }
    
    public function postPDF(){
        $exportarpdf   = Session::getPermiso('VSEVEEP');        
        if($exportarpdf['permiso']){   
            $data = Obj::run()->generarVentaController->generarDocPDF();        
            return $data;
        }
    }
    
    public static function findVenta(){ 
        $data = Obj::run()->vseguimientoventaModel->findVenta();        
        return $data;
    }   
    
    public static function findVentaPago(){ 
        $data = Obj::run()->vseguimientoventaModel->findVentaPago();        
        return $data;
    }  
   
    /*envia datos para eliminar registro: GenerarVenta*/
    public function postModificarGenerarVenta(){
        $editar = Session::getPermiso("VSEVEED");
        if($editar['permiso']){ 
            $data = Obj::run()->vseguimientoventaModel->modificarGenerarVenta();
            echo json_encode($data);
        }
    }
    
}

?>
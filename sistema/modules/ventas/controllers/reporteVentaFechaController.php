<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-11-2014 00:11:17 
* Descripcion : reporteVentaFechaController.php
* ---------------------------------------
*/    

class reporteVentaFechaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'reporteVentaFecha'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'cajaCierre'));
    }
    
    public function index(){ 
        Obj::run()->View->render("indexReporteVentaFecha");
    }
    
    public function getGridReporteVentaFecha(){
        $consultar   = Session::getPermiso('VRPT2CC');
        $exportarpdf   = Session::getPermiso('VRPT2EP');
        $exportarexcel  = Session::getPermiso('VRPT2EX');
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->reporteVentaFechaModel->getReporteVentaFecha();
        
        $num = Obj::run()->reporteVentaFechaModel->_iDisplayStart;
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
                
                $encryptReg = Aes::en($aRow['id_caja']);
                
                /*campo que maneja los estados, para el ejemplo aqui es ACTIVO, coloca tu campo*/
                if($aRow['estado'] == 'A'){
                    $estado = '<span class=\"label label-success\">'.LABEL_APERTURA.'</span>';
                }else{
                    $estado = '<span class=\"label label-danger\">'.LABEL_CIERRE.'</span>';
                } 
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($exportarpdf['permiso'] && $aRow['estado'] == 'C'){
                    $axion .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" onclick=\"reporteVentaFecha.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                    $axion .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($exportarexcel['permiso'] && $aRow['estado'] == 'C'){
                    $axion .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" onclick=\"reporteVentaFecha.postExcel(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                    $axion .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';     
                
                
                $f = new DateTime($aRow['fecha_creacion']);
		$c1 = $f->format('d/m/Y h:i A');                                             
                $c7 =  $aRow['sucursal'];    
                $c2 =  $aRow['descripcion_moneda'];             
                $c3 =  number_format($aRow['monto_inicial'],2);             
                $c4 =  number_format($aRow['total_ingresos'],2);             
                $c5 =  number_format($aRow['total_egresos'],2);             
                $c6 =  number_format($aRow['total_saldo'],2);             
                /*registros a mostrar*/
                $sOutput .= '["'.$aRow['id_caja'].'","'.$c1.'","'.$c7.'","'.$c2.'","'.$c3.'","'.$c4.'","'.$c5.'","'.$c6.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
   
     public function postPDF(){         
       Obj::run()->cajaCierreController->setCaja(Obj::run()->reporteVentaFechaModel->_idReporteVentaFecha);
       $data = Obj::run()->cajaCierreController->postPDF();
       return $data;        
    }
    
    public function postExcel(){
       Obj::run()->cajaCierreController->setCaja(Obj::run()->reporteVentaFechaModel->_idReporteVentaFecha);
       $data = Obj::run()->cajaCierreController->postPDF();
       return $data;        
    }
    
}

?>
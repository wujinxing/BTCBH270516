<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-11-2014 22:11:42 
* Descripcion : vConsultaSaldoController.php
* ---------------------------------------
*/    

class vConsultaSaldoController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'vConsultaSaldo'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'generarVenta'));         
    }
    
    public function index(){ 
        Obj::run()->View->render("indexVConsultaSaldo");
    }
    
    public function getGridVConsultaSaldo(){        
        $pdf   = Session::getPermiso('VCSCLEP');
        $excel = Session::getPermiso('VCSCLEX');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vConsultaSaldoModel->getVConsultaSaldo();
        
        $num = Obj::run()->vConsultaSaldoModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_persona']);
                               
                if((!empty($aRow['empresa']) || $aRow['empresa'] !== '' ) && (!empty($aRow['cliente']) || $aRow['cliente'] !== '' )){
                    $cliente = $aRow['empresa'].' | '.$aRow['cliente'];                   
                }else{
                     if(!empty($aRow['empresa']) || $aRow['empresa'] !== '' ){
                          $cliente = $aRow['empresa'];
                     }elseif (!empty($aRow['cliente']) || $aRow['cliente'] !== '' ){
                         $cliente = $aRow['cliente'];
                     }                   
                }
                
                $sucursal = $aRow['sucursal'];
                
                $nombre = '<a href=\"javascript:;\" onclick=\"persona.getDatosPersonales(\''.$encryptReg.'\',\''.LABEL_DATOSCLIENTE.'\');\">'.$cliente.'</a>';
                $total = $aRow['moneda'].' '.number_format($aRow['monto_saldo'],2);                     
                                
                $axion = '"<div class=\"btn-group\">';
                 
                if($pdf['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$pdf['theme'].'\" title=\"'.$pdf['accion'].'\" onclick=\"vConsultaSaldo.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$pdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($excel['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$excel['theme'].'\" title=\"'.$excel['accion'].'\" onclick=\"vConsultaSaldo.postExcel(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$excel['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                                    
                
                /*datos de manera manual*/
                $sOutput .= '["'.($num++).'","'.$nombre.'","'.$sucursal.'","'.$total.'",'.$axion.' ';
                                
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
        $exportarpdf   = Session::getPermiso('VCSCLEP');
        if($exportarpdf['permiso']){  
            $key= Functions::nombreAleatorio(0);
            $c = 'credito_venta_'.Obj::run()->vConsultaSaldoModel->_idPersona.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;
            
            $mpdf = new mPDF('c');                                  
            $mpdf->SetHTMLHeader('<br/><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" />','',TRUE);
            $mpdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>
                                    <td width="33%"><span style="font-weight: bold;">{DATE j/m/Y}</span></td>
                                    <td width="33%" align="center" style="font-weight: bold;">{PAGENO}/{nbpg}</td>
                                    <td width="33%" style="text-align: right; ">'.LB_EMPRESA.'</td>
                                 </tr></table>');

            $html = $this->getHtmlRptResumen01($mpdf);         

            //$mpdf->WriteHTML($html);
            $mpdf->Output($ar,'F');

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }        
    }
    
    public function postExcel(){
        $exportarexcel = Session::getPermiso('VCSCLEX');   
        if($exportarexcel['permiso']){     
            $key= Functions::nombreAleatorio(0);
            $c = 'credito_venta_'.Obj::run()->vConsultaSaldoModel->_idPersona.$key.'.xls';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;          

            $html = $this->getHtmlRptResumen01("EXCEL");

            $f=fopen($ar,'wb');
            if(!$f){$data = array('result'=>2);}
            fwrite($f,  utf8_decode($html));
            fclose($f);

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }
    }
    
    private function getHtmlRptResumen01($mpdf){
        $dataC = Obj::run()->vConsultaSaldoModel->getFindResumenUnitario();
        
        if((!empty($dataC[0]['cliente']) || $dataC[0]['cliente'] !== '' ) && (!empty($dataC[0]['empresa']) || $dataC[0]['empresa'] !== '' )){
            $cliente = $dataC[0]['empresa'].' / '.$dataC[0]['cliente'];                   
        }else{
             if(!empty($dataC[0]['cliente']) || $dataC[0]['cliente'] !== '' ){
                  $cliente = $dataC[0]['cliente'];
             }elseif (!empty($dataC[0]['empresa']) || $dataC[0]['empresa'] !== '' ){
                 $cliente = $dataC[0]['empresa'];
             }                   
        }   
        $ciudad = $dataC[0]['ciudad'];            
        $mon = $dataC[0]['moneda'].' ';
        $sucursal = $dataC[0]['sucursal'];
        
        $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<thead><tr><th style="width:5%">N°</th>';
        $htmlDet .= '<th style="width:15%">ID Venta</th>';                       
        $htmlDet .= '<th style="width:12%">Fecha</th>';
        $htmlDet .= '<th style="width:30%">Producto / Servicio</th>';
        $htmlDet .= '<th style="width:10%">Cantidad</th>';        
        $htmlDet .= '<th style="width:12%">Precio</th>';
        $htmlDet .= '<th style="width:12%">Importe</th>';
        $htmlDet .= '<th style="width:12%">Deuda</th>';
        $htmlDet .= '</tr></thead>';    
        $i=1;
        $saldo = 0;
        $cantidad = 0;
        $devueltos = 0;
        $importe = 0;
        $sumDeuda=0;
        $copiadoID = '';
        foreach ($dataC as $value) {                
            $cantidad += $value['cantidad'];
            $importe += $value['importe'];                        
            $idVenta = $value['id_docventa'];                                    
            if($idVenta !== $copiadoID){     
                $idVentaMostrar = $value['id_docventa'];
                $sumDeuda  += $value['monto_saldo'];      
                $deuda = $mon.number_format($value['monto_saldo'],2);
            }else{
                $deuda = $mon.'0.00';
                $idVentaMostrar = '';
            }
            
            if($value['id_producto'] == 201600000001){
                $longitud = strlen($value['descripcion']);
                if($longitud > 100){
                    $producto = 'CONCEPTO DE SERVICIO';
                }else{
                    $producto = htmlspecialchars_decode($value['descripcion'],ENT_QUOTES);
                }
            }else{
                $producto = htmlspecialchars_decode($value['producto'],ENT_QUOTES);
            }
            
            $htmlDet .= '<tr>
                <td style="text-align:center">'.($i++).'</td>
                <td style="text-align:center">'.$idVentaMostrar.'&nbsp;</td>
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value['fecha']).'</td>                
                <td style="text-align:left">'.$producto.'</td>
                <td style="text-align:right">'.number_format($value['cantidad']).'</td>
                <td style="text-align:right">'.$mon.number_format($value['precio'],2).'</td>
                <td style="text-align:right">'.$mon.number_format($value['importe'],2).'</td>
                <td style="text-align:right">'.$deuda.'</td>
            </tr>           
            ';     
            $copiadoID = $idVenta;    
        }
        
        $htmlDet .= '<tr><td colspan="4" style="text-align:right;" ></td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.number_format($cantidad).'</td>';
        $htmlDet .= '<td style="text-align:right;" ></td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($importe,2).'</td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($sumDeuda,2).'</td>';
        $htmlDet .= '</tr>';
        $htmlDet .='</table>';               
                          
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('SALDO01');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        
        $html = str_replace('{{CIUDAD}}',$ciudad, $html);
        $html = str_replace('{{CLIENTE}}',$cliente, $html);  
        $html = str_replace('{{SUCURSAL}}',$sucursal, $html);  
        $html = str_replace('{{DETALLE_RESUMEN}}',$htmlDet, $html);
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                               
            $mpdf->WriteHTML($html);      
        }      
    }
    
    
    public function postPDFGeneral(){
        $exportarpdf   = Session::getPermiso('VCSCLEP');
        if($exportarpdf['permiso']){  
            $key= Functions::nombreAleatorio(0);
            $c = 'credito_ventas_sevend'.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;           

            $mpdf = new mPDF('c');                                  
            $mpdf->SetHTMLHeader('<br/><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" />','',TRUE);
            $mpdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>
                                    <td width="33%"><span style="font-weight: bold;">{DATE j/m/Y}</span></td>
                                    <td width="33%" align="center" style="font-weight: bold;">{PAGENO}/{nbpg}</td>
                                    <td width="33%" style="text-align: right; ">'.LB_EMPRESA.'</td>
                                 </tr></table>');

            $html = $this->getHtmlRptResumen02($mpdf);         

            //$mpdf->WriteHTML($html);
            $mpdf->Output($ar,'F');

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }        
    }
    
    public function postExcelGeneral(){
        $exportarexcel = Session::getPermiso('VCSCLEX');   
        if($exportarexcel['permiso']){     
            $key= Functions::nombreAleatorio(0);
            $c = 'credito_ventas_sevend'.$key.'.xls';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;                                   

            $html = $this->getHtmlRptResumen02("EXCEL");

            $f=fopen($ar,'wb');
            if(!$f){$data = array('result'=>2);}
            fwrite($f,  utf8_decode($html));
            fclose($f);

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }
    }
    
    private function getHtmlRptResumen02($mpdf){
        $dataC = Obj::run()->vConsultaSaldoModel->getFindResumenGeneral();                    
        $sucursal = $dataC[0]['sucursal'];
        
        $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<thead><tr><th colspan="8">Clientes</th></tr>';
        $htmlDet .= '<tr><th style="width:5%">N°</th>';
        $htmlDet .= '<th style="width:10%">ID Venta</th>';                                                                 
        $htmlDet .= '<th style="width:10%">Fecha</th>';
        $htmlDet .= '<th style="width:20%">Producto / Servicio</th>';
        $htmlDet .= '<th style="width:10%">Cantidad</th>';                
        $htmlDet .= '<th style="width:12%">Precio</th>';
        $htmlDet .= '<th style="width:12%">Importe</th>';
        $htmlDet .= '<th style="width:12%">Deuda</th>';
        $htmlDet .= '</tr></thead>';          
        $saldo = 0;
        $cantidad = 0;
        $devueltos = 0;
        $importe = 0;
        $deuda=0; $sumDeuda=0;
        $copiado = '';  
        $copiadoID = '';
        foreach ($dataC as $value) {                
            
            if((!empty($value['cliente']) || $value['cliente'] !== '' ) && (!empty($value['empresa']) || $value['empresa'] !== '' )){
                $cliente = $value['empresa'].' / '.$value['cliente'];                   
            }else{
                 if(!empty($value['cliente']) || $value['cliente'] !== '' ){
                      $cliente = $value['cliente'];
                 }elseif (!empty($value['empresa']) || $value['empresa'] !== '' ){
                     $cliente = $value['empresa'];
                 }                   
            }             
            $cliente .= ' / '.$value['ciudad'];
            $cliente = Functions::htmlRender($cliente);
            $cantidad += $value['cantidad'];
            $importe += $value['importe'];            
            
            if($cliente !== $copiado){
                $i=1;
                $htmlDet .= '<tr><td colspan="8"><strong>'.$cliente.'</strong></td></tr>';
            }
            $idVenta = $value['id_docventa'];
            if($idVenta !== $copiadoID){  
                $sumDeuda  += $value['monto_saldo'];      
                $deuda = $mon.number_format($value['monto_saldo'],2);
            }else{
                $deuda = $mon.'0.00';
            }            
            
            if($value['id_producto'] == 201600000001 ){
                $producto = 'SERVICIO';
            }else{
                $producto = htmlspecialchars_decode($value['producto'],ENT_QUOTES);
            }
            
            $htmlDet .= '<tr>
                <td style="text-align:center">'.($i).'</td>
                <td style="text-align:center">'.$idVenta.'&nbsp;</td>
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value['fecha']).'</td>                
                <td style="text-align:left">'.$producto.'</td>
                <td style="text-align:right">'.number_format($value['cantidad']).'</td>
                <td style="text-align:right">'.$mon.number_format($value['precio'],2).'</td>
                <td style="text-align:right">'.$mon.number_format($value['importe'],2).'</td>
                <td style="text-align:right">'.$deuda.'</td>
            </tr>';            
            
            if($value['tVenta'] == $i ){
                $htmlDet .= '<tr><td colspan="4" style="text-align:right;" ></td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.number_format($cantidad).'</td>';
                $htmlDet .= '<td style="text-align:right;" ></td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($importe,2).'</td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$sumDeuda.'</td>';
                $htmlDet .= '</tr>';
                $saldo = 0;
                $cantidad = 0;
                $devueltos = 0;
                $importe = 0;                
                $sumDeuda=0;
            }
            $i++;
            $copiado = $cliente;   
            $copiadoID = $idVenta;    
        }        
       
        $htmlDet .='</table>';               
                          
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('SALDO02');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        $html = str_replace('{{SUCURSAL}}',$sucursal, $html);
        $html = str_replace('{{DETALLE_RESUMEN}}',$htmlDet, $html);
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                               
            $mpdf->WriteHTML($html);      
        }      
    }
    
}

?>
<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-05-2016 17:05:16 
* Descripcion : reporteResumenClienteController.php
* ---------------------------------------
*/    

class reporteResumenClienteController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'reporteResumenCliente'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));  
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc')); 
        $this->loadController(array('modulo'=>'configuracion','controller'=>'parametro'));
    }
    
    public function index(){
        $main = Session::getMain("VRPT5");
        if($main["permiso"]){
            Obj::run()->View->render("indexReporteResumenCliente");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridReporteResumenCliente(){
        $pdf   = Session::getPermiso('VRPT5EP');
        $excel = Session::getPermiso('VRPT5EX');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->reporteResumenClienteModel->getReporteResumenCliente();
        
        $num = Obj::run()->reporteResumenClienteModel->_iDisplayStart;
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
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($pdf['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$pdf['theme'].'\" title=\"'.$pdf['accion'].'\" onclick=\"reporteResumenCliente.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$pdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($excel['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$excel['theme'].'\" title=\"'.$excel['accion'].'\" onclick=\"reporteResumenCliente.postExcel(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$excel['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                
                 if((!empty($aRow['empresa']) || $aRow['empresa'] !== '' ) && (!empty($aRow['cliente']) || $aRow['cliente'] !== '' )){
                    $cliente = $aRow['empresa'].' | '.$aRow['cliente'];                   
                }else{
                     if(!empty($aRow['empresa']) || $aRow['empresa'] !== '' ){
                          $cliente = $aRow['empresa'];
                     }elseif (!empty($aRow['cliente']) || $aRow['cliente'] !== '' ){
                         $cliente = $aRow['cliente'];
                     }                   
                }              
                 $cliente = '<a href=\"javascript:;\" onclick=\"persona.getDatosPersonales(\''.$encryptReg.'\',\''.LABEL_DATOSCLIENTE.'\');\">'.$cliente.'</a>';
                 $idCliente = '<span class=\"badge bg-color-orange font10 \"  >'.$aRow['id_persona'].'</span>';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$idCliente.'","'.$cliente.'","'.$aRow['ciudad'].'","'.$aRow['ventas'].'",'.$axion.' ';

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
        $exportarpdf   = Session::getPermiso('VRPT4EP');
        if($exportarpdf['permiso']){  
            $key = Functions::nombreAleatorio(0);
            $c = 'resumen_venta_'.Obj::run()->reporteResumenClienteModel->_idPersona.$key.'.pdf';
            
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
        $exportarexcel = Session::getPermiso('VRPT4EX');   
        if($exportarexcel['permiso']){     
            $key = Functions::nombreAleatorio(0);
            $c = 'resumen_venta_'.Obj::run()->reporteResumenClienteModel->_idPersona.$key.'.xls';
            
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
        $dataC = Obj::run()->reporteResumenClienteModel->getFindResumenUnitario();
        $detallePago = Obj::run()->reporteResumenClienteModel->getFindPagosUnitario();
        $f1 = Functions::cambiaf_a_normal(Obj::run()->reporteResumenClienteModel->_fecha1);
        $f2 = Functions::cambiaf_a_normal(Obj::run()->reporteResumenClienteModel->_fecha2);
        
        if((!empty($dataC[0]['cliente']) || $dataC[0]['cliente'] !== '' ) && (!empty($dataC[0]['empresa']) || $dataC[0]['empresa'] !== '' )){
            $cliente = $dataC[0]['empresa'].' / '.$dataC[0]['cliente'];                   
        }else{
             if(!empty($dataC[0]['cliente']) || $dataC[0]['cliente'] !== '' ){
                  $cliente = $dataC[0]['cliente'];
             }elseif (!empty($dataC[0]['empresa']) || $dataC[0]['empresa'] !== '' ){
                 $cliente = $dataC[0]['direccion'];
             }                   
        }   
        $ciudad = $dataC[0]['ciudad'];    
        $mon = $dataC[0]['moneda'].' ';
        $sucursal = $dataC[0]['sucursal'];
        
        $htmlDet .='<style>.fila{background: #EFEFEF;}</style>';
        $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<thead><tr><th style="width:5%">N°</th>';
        $htmlDet .= '<th style="width:11%">ID Venta</th>';                       
        $htmlDet .= '<th style="width:11%">N° Orden</th>';
        $htmlDet .= '<th style="width:11%">Fecha Venta</th>';
        $htmlDet .= '<th style="width:11%">Total</th>';
        $htmlDet .= '<th style="width:11%">Pagado</th>';
        $htmlDet .= '<th style="width:11%">Deuda</th>';
        $htmlDet .= '</tr></thead>';    
        $i=1;
        $importe = 0;
        $sumDeuda=0;
        $sumPagado=0;
        $pagado= 0;
        $copiadoID = '';
        foreach ($dataC as $value) {                
            $importe += $value['monto_total_final'];                        
            $idVenta = $value['id_docventa'];
            $sumPagado +=$value['monto_asignado'];   
            $sumDeuda  += $value['monto_saldo'];      
                
            $deuda = $mon.number_format($value['monto_saldo'],2);
            $pagado = $mon.number_format($value['monto_asignado'],2);
            
            if($i%2 !== 0) $fila = 'class="fila"';
            else $fila = '';
                                   
            $htmlDet .= '<tr '.$fila.'>
                <td style="text-align:center">'.($i++).'</td>
                <td style="text-align:center">'.$idVenta.'&nbsp;</td>
                <td style="text-align:center">'.$value['codigo_impresion'].'&nbsp;</td>
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value['fecha']).'</td>                
                <td style="text-align:right"><b>'.$mon.number_format($value['monto_total_final'],2).'</b></td>
                <td style="text-align:right"><b>'.$pagado.'</b></td>
                <td style="text-align:right">'.$deuda.'</td>
            </tr>';                       
            $copiadoID = $idVenta;    
        }
        
        $htmlDet .= '<tr><td colspan="4" style="text-align:right;" ></td>';        
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($importe,2).'</td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($sumPagado,2).'</td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($sumDeuda,2).'</td>';
        $htmlDet .= '</tr>';
        $htmlDet .='</table>';               
                                    
        if(($detallePago[0]['id_pago']) !== NULL) {
            $sumPagos = 0;
            $htmlPag = '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
            $htmlPag .= '<thead><tr>'
                    . '  <th style="width:5%">N°</th>';            
            $htmlPag .= '<th style="width:13%">ID Venta</th>';
            $htmlPag .= '<th style="width:10%">F. Venta</th>';
            $htmlPag .= '<th style="width:10%">T. Venta</th>';
            $htmlPag .= '<th style="width:11%">F. Pago</th>';   
            $htmlPag .= '<th style="width:10%">Método P.</th>'; 
            $htmlPag .= '<th style="width:10%">T. Doc.</th>'; 
            $htmlPag .= '<th style="width:12%">Serie-Número</th>'; 
            $htmlPag .= '<th style="width:10%">Estado</th>';  
            $htmlPag .= '<th style="width:11%">Monto</th>';  
            
            $htmlPag .= '</tr></thead>';    
            $j=1;
            foreach ($detallePago as $value2) {    
                $sumPagos += $value2['monto_pagado'];
                switch ($value2['estado']){
                    case 'E': $estado = LABEL_EMITIDO; break;
                    case 'A': $estado = LABEL_ANULADO; break;    
                    case 'P': $estado = LABEL_PAGADO; break;    
                    case 'M': $estado = LABEL_MODIFICADO; break;  
                    case 'I': $estado = LABEL_INHABILITADO; break;  
                    case 'R': $estado = LABEL_PROGRAMADO; break;
                }              
                 if($value2['estado'] === 'E' || $value2['estado'] === 'R' )
                    $metodPago = '- NINGUNO -';
                else
                    $metodPago = $value2['metodopago'];
                
                $htmlPag .= '<tr >
                <td style="text-align:center">'.($j++).'</td>
                <td style="text-align:center">'.  $value2['id_docventa'].'&nbsp;</td>                
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value2['fecha_venta']).'</td>                
                <td style="text-align:right">'.$mon.number_format($value2['monto_total'],2).'</td>                           
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value2['fecha']).'</td>       
                <td style="text-align:center">'.  $metodPago.'</td>               
                <td style="text-align:center">'.  $value2['tipo_doc'].'</td>                
                <td style="text-align:center">'.  $value2['serie'].'-'.$value2['numero'].'</td>     
                <td style="text-align:center">'.  $estado.'</td>                     
                <td style="text-align:right">'.$mon.number_format($value2['monto_pagado'],2).'</td>                
                </tr>';
            }            
            $htmlPag .= '<tr><td colspan="9" style="text-align:right;" ></td>';
            $htmlPag .= '<td colspan="1"  style="text-align:right; font-weight:bold;">'.$mon.number_format($sumPagos,2).'</td>';
            $htmlPag .= '</tr>';
            $htmlPag .='</table>';                
        }        
        
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('VENTA01');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        
        $html = str_replace('{{CIUDAD}}',$ciudad, $html);
        $html = str_replace('{{F1}}',$f1, $html);
        $html = str_replace('{{F2}}',$f2, $html);
        $html = str_replace('{{CLIENTE}}',$cliente, $html);   
        $html = str_replace('{{SUCURSAL}}',$sucursal, $html);  
        $html = str_replace('{{DETALLE_RESUMEN}}',$htmlDet, $html);
        $html = str_replace('{{DETALLE_PAGOS}}',$htmlPag, $html);
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                               
            $mpdf->WriteHTML($html);      
        }      
    }
    
    
    public function postPDFGeneral(){
        $exportarpdf   = Session::getPermiso('VRPT4EP');
        if($exportarpdf['permiso']){  
            $key = Functions::nombreAleatorio(0);
            $c = 'resumen_ventas_sevend'.$key.'.pdf';
            
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
        $exportarexcel = Session::getPermiso('VRPT4EX');   
        if($exportarexcel['permiso']){     
            $key = Functions::nombreAleatorio(0);
            $c = 'resumen_ventas_sevend'.$key.'.xls';
            
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
        $dataC = Obj::run()->reporteResumenClienteModel->getFindResumenGeneral();
        $f1 = Functions::cambiaf_a_normal(Obj::run()->reporteResumenClienteModel->_fecha1);
        $f2 = Functions::cambiaf_a_normal(Obj::run()->reporteResumenClienteModel->_fecha2);
        
        $htmlDet ='<style>.fila{background: #EFEFEF;}</style>'; 
        $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<thead><tr><th colspan="12">Clientes</th></tr>';
        $htmlDet .= '<tr><th style="width:5%">N°</th>';
        $htmlDet .= '<th style="width:30%">ID Venta</th>';                                                                 
        $htmlDet .= '<th style="width:12%">Fecha</th>';
        $htmlDet .= '<th style="width:20%">Producto</th>';
        $htmlDet .= '<th style="width:10%">Cantidad</th>';   
        $htmlDet .= '<th style="width:10%">Devueltos</th>';
        $htmlDet .= '<th style="width:12%">Precio</th>';
        $htmlDet .= '<th style="width:12%">T. Venta</th>';
        $htmlDet .= '<th style="width:12%">Pagado</th>';
        $htmlDet .= '<th style="width:12%">Deuda</th>';
        $htmlDet .= '<th colspan="2" style="width:20%">Responsable</th>';                                               
        $htmlDet .= '</tr></thead>';          
        $saldo = 0;
        $cantidad = 0;
        $devueltos = 0;
        $importe = 0;
        $sumTCantidad = 0;
        $sumTDevueltos = 0;
        $sumTImporte = 0;
        $sumTDeuda = 0;
        $deuda=0; $sumDeuda=0;
        $pagado = 0; $sumPagado = 0; $sumTPagado = 0;
        $copiado = '';  
        $copiadoID = '';
        foreach ($dataC as $value) {                
            
            if((!empty($value['cliente']) || $value['cliente'] !== '' ) && (!empty($value['direccion']) || $value['direccion'] !== '' )){
                $cliente = $value['direccion'].' / '.$value['cliente'];                   
            }else{
                 if(!empty($value['cliente']) || $value['cliente'] !== '' ){
                      $cliente = $value['cliente'];
                 }elseif (!empty($value['direccion']) || $value['direccion'] !== '' ){
                     $cliente = $value['direccion'];
                 }                   
            }             
            $cliente .= ' / '.$value['ciudad'].' [ '.$value['tipoCliente'].' ]';
            $cantidad += $value['cantidad'];
            $importe += $value['importe'];            
            $devueltos += $value['devolucion'];   
            if($cliente !== $copiado){
                $i=1;
                $htmlDet .= '<tr><td colspan="12"><strong>'.$cliente.'</strong></td></tr>';
            }
            $idVenta = $value['id_docventa'];
            if($idVenta !== $copiadoID){  
                $sumDeuda  += $value['monto_saldo'];      
                $deuda = $mon.number_format($value['monto_saldo'],2);
                $sumTDeuda += $value['monto_saldo'];  
                $pagado = $mon.number_format($value['monto_asignado'],2);  
                $sumPagado += $value['monto_asignado'];
                $sumTPagado += $value['monto_asignado'];
            }else{
                $deuda = $mon.'0.00';
            }                             
            $htmlDet .= '<tr>
                <td style="text-align:center">'.($i).'</td>
                <td style="text-align:center">'.$idVenta.'&nbsp;</td>
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value['fecha']).'</td>                
                <td style="text-align:left">'.$value['producto'].'</td>
                <td style="text-align:right">'.number_format($value['cantidad']).'</td>
                <td style="text-align:right">'.number_format($value['devolucion']).'</td>
                <td style="text-align:right">'.$mon.number_format($value['precio_dsto'],2).'</td>
                <td style="text-align:right"><b>'.$mon.number_format($value['importe'],2).'</b></td>
                <td style="text-align:right">'.$pagado.'</td>                
                <td style="text-align:right">'.$deuda.'</td>
                <td colspan="2" style="text-align:left">'.$value['responsable'].'</td>
            </tr>';                                              
            
            if($value['tVenta'] == $i ){
                $htmlDet .= '<tr><td colspan="4" style="text-align:right;" ></td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.number_format($cantidad).'</td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.number_format($devueltos).'</td>';                
                $htmlDet .= '<td style="text-align:right;" ></td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($importe,2).'</td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$sumPagado.'</td>';
                $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$sumDeuda.'</td>';
                $htmlDet .= '<td colspan="2" style="text-align:right;" ></td>';
                $htmlDet .= '</tr>';
                $sumTCantidad += $cantidad;
                $sumTDevueltos += $devueltos; 
                $sumTImporte  += $importe;  
                $saldo = 0;
                $cantidad = 0;
                $devueltos = 0;
                $importe = 0;                
                $sumDeuda=0;
                $sumPagado = 0;
            }
            $i++;
            $copiado = $cliente;   
            $copiadoID = $idVenta;    
        }        
        $htmlDet .= '<tr><td colspan="4" style="text-align:right;" ></td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.number_format($sumTCantidad).'</td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.number_format($sumTDevueltos).'</td>';                
        $htmlDet .= '<td style="text-align:right;" ></td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($sumTImporte,2).'</td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($sumTPagado,2).'</td>';
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($sumTDeuda,2).'</td>';        
        $htmlDet .= '<td colspan="2" style="text-align:right;" ></td>';
        $htmlDet .= '</tr>';                  
        $htmlDet .='</table>';               
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('VENTA02');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        $html = str_replace('{{F1}}',$f1, $html);
        $html = str_replace('{{F2}}',$f2, $html);
        $html = str_replace('{{DETALLE_RESUMEN}}',$htmlDet, $html);
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                               
            $mpdf->WriteHTML($html);      
        }      
    }
    
        
}

?>
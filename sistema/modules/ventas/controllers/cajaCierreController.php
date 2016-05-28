<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-12-2014 23:12:35 
* Descripcion : cajaCierreController.php
* ---------------------------------------
*/    

class cajaCierreController extends Controller{

    public function __construct(){
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'cajaCierre'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));    
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc'));   
        $this->loadController(array('modulo'=>'configuracion','controller'=>'parametro'));
    }
    
    public function index(){ 
        Obj::run()->View->render("indexCajaCierre");
    }
    
    public function getGridCajaCierre(){
        $generar    = Session::getPermiso("CAJACGN");
        //$reajustar    = Session::getPermiso("CAJACRPG");
        $exportarpdf   = Session::getPermiso('CAJACEP');
        $exportarexcel = Session::getPermiso('CAJACEX');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->cajaCierreModel->getCajaCierre();
        
        $num = Obj::run()->cajaCierreModel->_iDisplayStart;
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
                
                $axion = '"<div class=\"btn-group\">';
                 
                if($generar['permiso'] && $aRow['estado'] == 'A'  ){
                    $axion .= '<button type=\"button\" class=\"'.$generar['theme'].'\" title=\"'.$generar['accion'].'\" onclick=\"cajaCierre.getFormNewCierre(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$generar['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                    $axion .= '<button type=\"button\" class=\"'.$generar['theme'].'\" title=\"'.$generar['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$generar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                if($exportarpdf['permiso'] && $aRow['estado'] == 'C'){
                    $axion .= '<button id=\"btnCaja'.$aRow['id_caja'].'\" type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" onclick=\"cajaCierre.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                    $axion .= '<button id=\"btnCaja'.$aRow['id_caja'].'\" type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($exportarexcel['permiso'] && $aRow['estado'] == 'C'){
                    $axion .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" onclick=\"cajaCierre.postExcel(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                    $axion .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                                                
                $axion .= ' </div>" ';
                $f = new DateTime($aRow['fecha_creacion']);
		$c1 = $f->format('h:i A');   
                $fecha = Functions::cambiaf_a_normal($aRow['fecha_caja']);
                $c1 = $fecha.' '.$c1;
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

    public function getFormNewCierre(){
        $nuevo   = Session::getPermiso("CAJACGN");
        if($nuevo["permiso"]){     
            Obj::run()->View->render("formNewCierreCaja");
        }
    }
    
    public static function setCaja($id){
        Obj::run()->cajaCierreModel->_idCajaCierre = $id;
    }
    
    /*busca data para editar registro: Caja Cierre*/
    public static function findCaja(){
        $data = Obj::run()->cajaCierreModel->findCaja();
        return $data;
    }
    
    public static function findDenominacionAll($idMoneda, $tipo){
        $data = Obj::run()->cajaCierreModel->findDenominacionAll($idMoneda, $tipo);            
        return $data;
    }
    
    public static function findMetodoPagoAll(){
        $data = Obj::run()->cajaCierreModel->findMetodoPagoAll();            
        return $data;
    }   
    
    public static function findMetodoPagoVentasAll(){
        $data = Obj::run()->cajaCierreModel->findMetodoPagoVentasAll();            
        return $data;
    }   
    
    public function postGenerarCierre(){
        $grabar = Session::getPermiso("CAJACGN");
        if($grabar['permiso']){
            $data = Obj::run()->cajaCierreModel->postGenerarCierre();
            echo json_encode($data);
        }        
    }    
    
    public function postPDF(){
        $exportarpdf   = Session::getPermiso('CAJACEP');
        if($exportarpdf['permiso']){            
            $key= Functions::nombreAleatorio(0);
            $c = 'caja_cierre_'.Obj::run()->cajaCierreModel->_idCajaCierre.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

            $mpdf = new mPDF('c');                   
            $mpdf->keepColumns = true;
            //{DATE j-m-Y}
            
            $mpdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>
                                    <td width="33%"><span style="font-weight: bold;">{DATE j/m/Y}</span></td>
                                    <td width="33%" align="center" style="font-weight: bold;">{PAGENO}/{nbpg}</td>
                                    <td width="33%" style="text-align: right; ">'.LB_EMPRESA.'</td>
                                 </tr></table>');

            $html = $this->getHtmlGenerarReporte($mpdf);         

            //$mpdf->WriteHTML($html);
            $mpdf->Output($ar,'F');

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }        
    }
    
    public function postExcel(){
        $exportarexcel = Session::getPermiso('CAJACEX');   
        if($exportarexcel['permiso']){     
            $key= Functions::nombreAleatorio(0);
            $c = 'venta_'.Obj::run()->cajaCierreModel->_idCajaCierre.$key.'.xls';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

            $html = $this->getHtmlGenerarReporte("EXCEL");

            $f=fopen($ar,'wb');
            if(!$f){$data = array('result'=>2);}
            fwrite($f,  utf8_decode($html));
            fclose($f);

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }
    }
    
    private function getHtmlGenerarReporte($mpdf){
        $dataC = Obj::run()->cajaCierreModel->findCaja();
        $dataI = Obj::run()->cajaCierreModel->getPagosAll();
        $dataI2 = Obj::run()->cajaCierreModel->getIngresosAll();
        $dataE = Obj::run()->cajaCierreModel->getEgresosAll();
        $dataR = Obj::run()->cajaCierreModel->findResumenAll();
        
        //Ingresos        
        $htmlIngreso = '<h2>Detalle de Ingresos y Egresos</h2>';
        $htmlIngreso .= '<h3>Ventas</h3>';
        $htmlIngreso .= '<table id="td2" border="1" style="border-collapse:collapse">';
        $htmlIngreso .= '<thead><tr>';
        $htmlIngreso .= '   <th style="width:5%">N°</th>';
        $htmlIngreso .= '   <th style="width:15%">N° Orden</th>';
        $htmlIngreso .= '   <th style="width:20%">Total</th>';
        $htmlIngreso .= '   <th style="width:20%">Pagos a cta</th>';
        $htmlIngreso .= '   <th style="width:20%">Saldo</th>';
        $htmlIngreso .= '</tr></thead>';
        $i =1;
        foreach ($dataI as $value) {                  
            $totalS +=   $value['monto_importe'];
            $acuentaS += $value['monto_pagado'];
            $saldoS += $value['monto_saldo'];
            $monS = $value['moneda'].' ';                                

            if(!empty($value['empresa']) ){ 
                $nombre = $value['empresa'];
            }elseif (!empty($value['cliente'])){ 
                $nombre = $value['cliente'];
            }
            
            $htmlIngreso .= '<tr>
                <td style="text-align:center">'.($i++).'</td>
                <td style="text-align:center">'.(empty($value['codigo_impresion'])?'— No tiene —':$value['codigo_impresion']).'</td>
                <td style="text-align:right">'.$monS.number_format($value['monto_importe'],2).'</td>
                <td style="text-align:right"><b>'.$monS.number_format($value['monto_pagado'],2).'</b><br/><span style="font-size:9px; color:#999">'.$value['metodopago'].'</span></td>
                <td style="text-align:right">'.$monS.number_format($value['monto_saldo'],2).'</td>
            </tr>';            
        }    
        $htmlIngreso .= '<tr><td colspan="2"></td><td style="text-align:right; font-weight:bold;">'.$monS.number_format($totalS,2).'</td>'
                .'<td style="text-align:right; font-weight:bold;">'.$monS.number_format($acuentaS,2).'</td>'
                .'<td style="text-align:right; font-weight:bold;">'.$monS.number_format($saldoS,2).'</td>'
                . '</tr>';                       
        $htmlIngreso .='</table>';   
        
        
        //Ingreso Directo
        $htmlIngreso .= '<h3>Ingreso por Transferencia</h3>';
        $htmlIngreso .= '<table id="td2" border="1" style="border-collapse:collapse">';
        $htmlIngreso .= '<thead><tr>';
        $htmlIngreso .= '   <th style="width:5%">N°</th>';                        
        $htmlIngreso .= '   <th style="width:70%">Concepto</th>';
        $htmlIngreso .= '   <th style="width:20%">Importe</th>';
        $htmlIngreso .= '</tr></thead>';
        $i =1;
        $totalS = 0;
        foreach ($dataI2 as $value) {                             
            $totalS +=   $value['monto_importe'];         
            $monS = $value['moneda'].' ';                                
                      
            $htmlIngreso .= '<tr>
                <td style="text-align:center">'.($i++).'</td>
                <td>'.$value['observacion'].'</td>
                <td style="text-align:right">'.$monS.number_format($value['monto_importe'],2).'</td>
            </tr>';            
        }    
        $htmlIngreso .= '<tr><td colspan="2"></td><td style="text-align:right; font-weight:bold;">'.$monS.number_format($totalS,2).'</td>'
                . '</tr>';                       
        $htmlIngreso .='</table>';   
        
        //Egresos
        $htmlEgreso = '<h3>Egresos</h3>';
        $htmlEgreso .= '<table id="td2" border="1" style="border-collapse:collapse">';
        $htmlEgreso .= '<thead><tr>';
        $htmlEgreso .= '   <th style="width:5%">N°</th>';
        $htmlEgreso .= '   <th style="width:70%">Concepto</th>';
        $htmlEgreso .= '   <th style="width:20%">Monto</th>';
        $htmlEgreso .= '</tr></thead>';
        $i =1;
        foreach ($dataE as $value) {                             
            $egresosS += $value['monto'];
            $mon = $value['moneda'].' ';
            $beneficiario = '';
            if(($value['templeado'] == 'S' ) ){
                $beneficiario = ' | '.$value['empleado'];                   
            }elseif(($value['tproveedor'] == 'S' ) ){                  
                $beneficiario = ' | '.$value['proveedor'];                             
            }     
            $htmlEgreso .= '<tr>
                <td style="text-align:center">'.($i++).'</td>
                <td>'.preg_replace ('[\n|\r|\n\r]','',$value['descripcion']).$beneficiario.'</td>
                <td style="text-align:right">'.$mon.number_format($value['monto'],2).'</td>
            </tr>';            
        }    
        $htmlEgreso .= '<tr><td colspan="2"></td><td style="text-align:right; font-weight:bold;">'.$monS.number_format($egresosS,2).'</td>'
                . '</tr>';                    
        $htmlEgreso .='</table>';   
                
        //Resumen Ingresos de Caja.               
        //Resumen Final:
        $mon = $dataC['sigla'].' ';        
        $htmlResumen = '<table id="td2" border="0" style="border-collapse:collapse">';        
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<td style="text-align:left">Pagos a Cta. (Efectivo):</td>';                
        $htmlResumen .= '<td style="text-align:right">'.$mon.number_format($dataC['total_acuenta'],2).'</td>';
        $htmlResumen .= '</tr>';   
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<td style="text-align:left">Total I. Transferencia (Efectivo):</td>';                
        $htmlResumen .= '<td style="text-align:right">'.$mon.number_format($dataC['total_ingresoDirecto'],2).'</td>';
        $htmlResumen .= '</tr>';   
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<td style="text-align:left">Otros Ingresos:</td>';                
        $htmlResumen .= '<td style="text-align:right">'.$mon.number_format($dataC['total_otros'],2).'</td>';
        $htmlResumen .= '</tr>'; 
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<tr><td colspan="2"><hr></td><tr>';        
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<td style="text-align:left">Saldo Inicial (Efectivo):</td>';                
        $htmlResumen .= '<td style="text-align:right">'.$mon.number_format($dataC['monto_inicial'],2).'</td>';
        $htmlResumen .= '</tr>';   
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<td style="text-align:left">Total Efectivo:</td>';                
        $htmlResumen .= '<td style="text-align:right">'.$mon.number_format($dataC['total_efectivo'],2).'</td>';
        $htmlResumen .= '</tr>';
        $htmlResumen .= '<tr><td colspan="2"><hr></td>';
        $htmlResumen .= '<tr>';        
        $htmlResumen .= '<td style="text-align:left">Total Ingresos:</td>';                
        $htmlResumen .= '<td style="text-align:right"><b>'.$mon.number_format($dataC['total_ingresos'],2).'</b></td>';
        $htmlResumen .= '</tr>';         
        $htmlResumen .= '<tr>';
        $htmlResumen .= '<td style="text-align:left">Total Egresos:</td>';                
        $htmlResumen .= '<td style="text-align:right"><b>'.$mon.number_format($dataC['total_egresos'],2).'</b></td>';
        $htmlResumen .= '</tr>';   
        $htmlResumen .= '<tr><td colspan="2"><hr></td><tr>';
        $htmlResumen .= '<tr>';        
        $htmlResumen .= '<td style="text-align:left">Saldo Caja (Efectivo):</td>';                
        $htmlResumen .= '<td style="text-align:right"><b>'.$mon.number_format($dataC['saldo'],2).'</b></td>';
        $htmlResumen .= '</tr>';                  
        $htmlResumen .='</table>';   
        
        $htmlResumen .= '<h4>Observaciones</h4>';
        if($dataC['observacion'] !== ''){
            $htmlResumen .= '<p>'.nl2br($dataC['observacion']).'</p>';
        }else{
            $htmlResumen .= '<p>NINGUNA.</p>';
        }     
        
        $htmlResumen .= '<h3>Detalle de Caja</h3><table id="td2" border="1" style="border-collapse:collapse">';
        $htmlResumen .= '<thead><tr>';
        $htmlResumen .= '   <th style="width:5%">N°</th>';
        $htmlResumen .= '   <th style="width:70%">Método de Pago</th>';
        $htmlResumen .= '   <th style="width:20%">Monto</th>';
        $htmlResumen .= '</tr></thead>';
        $i =1;
        $totalS = 0;
        foreach ($dataR as $value) {                             
            $totalS += $value['monto_manual'];
            $monS = $value['moneda'].' ';
            
            $htmlResumen .= '<tr>
                <td style="text-align:center">'.($i++).'</td>
                <td>'.$value['metodopago'].'</td>
                <td style="text-align:right; font-weight:bold;">'.$mon.number_format($value['monto_manual'],2).'</td>
            </tr>';            
        }    
        $htmlResumen .= '<tr><td colspan="2" style="text-align:left;"><b>Total de Caja</b></td><td style="text-align:right; font-weight:bold; color:#A90329">'.$monS.number_format($totalS,2).'</td>'
                . '</tr>';                    
        $htmlResumen .='</table>';  
                         
        
        
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('CIERRCAJA');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        $html = str_replace('{{FECHA}}',  Functions::cambiaf_a_normal($dataC['fecha_caja']), $html);
        $html = str_replace('{{SUCURSAL}}',$dataC['sucursal'].' ('.$dataC['sucursal_sigla'].')', $html);
        $html = str_replace('{{ESTADO}}',$dataC['estado'], $html);
        $html = str_replace('{{MONEDA}}',$dataC['moneda'], $html);
        $html = str_replace('{{CAJA}}',$dataC['id_caja'].'&nbsp;', $html);
        
        $html = str_replace('{{DETALLE_INGRESOS}}',$htmlIngreso, $html);
        $html = str_replace('{{DETALLE_EGRESOS}}',$htmlEgreso, $html);
        $html = str_replace('{{DETALLE_CAJA}}',$htmlResumen, $html);
    
              
                
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                 
            if( !empty($dataC['fecha_cierre'])){
                $obs .= '<b>Fecha Cierre: </b>'.  Functions::formato_fecha($dataC['fecha_cierre'],"%A, %d de %B del %Y a las ").date('h:i A', strtotime($dataC['fecha_cierre']));
                $obs .= '<br/><b>Cajero(a):</b> '. ($dataC['cajero']).'';
            }  
            
             $mpdf->SetHTMLHeader('<table width="100%" style="vertical-align: bottom; font-family: Arial; font-size: 10pt; color: #000000;"><tr>
                                    <td width="50%"><span style="font-weight: bold;"><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" /></span></td>
                                    <td width="50%" style="text-align: right;">'.$obs.'</td>
                                 </tr></table>','',TRUE);
            
            $mpdf->WriteHTML($html);      
        }      
    }
      
    
}

?>
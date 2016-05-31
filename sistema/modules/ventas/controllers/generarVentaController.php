<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-09-2014 23:09:58 
* Descripcion : generarVentaController.php
* ---------------------------------------
*/    

class generarVentaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'generarVenta'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vproducto')); 
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));         
        $this->loadController(array('modulo'=>'configuracion','controller'=>'parametro'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'cajaApertura')); 
        $this->loadController(array('modulo'=>'configuracion','controller'=>'metodoPago'));
    }
    
    public function index(){ 
        $main = Session::getMain('VGEVE');
        if($main['permiso']){
            Obj::run()->View->render("indexGenerarVenta");    
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }           
    }
       
    public function getGridGenerarVenta(){
       $exportarpdf   = Session::getPermiso('VGEVEEP');
       $exportarexcel = Session::getPermiso('VGEVEEX');       
       $anular = Session::getPermiso("VGEVEAN");
       $pagar   = Session::getPermiso('VSEVEPG');
       
       $sEcho          =   $this->post('sEcho');
        
       $rResult = Obj::run()->generarVentaModel->getGridGenerarVenta();
       
       $num = Obj::run()->generarVentaModel->_iDisplayStart;
       if($num >= 10){
            $num++;
       }else{
            $num = 1;
       }
       
       if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            $idx =1;
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';
            foreach ( $rResult as $key=>$aRow ){
                             
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
                
                $sucursal = $aRow['sucursal'];
                
                $nombre = '<a href=\"javascript:;\" onclick=\"persona.getDatosPersonales(\''.$idPersona.'\',\''.LABEL_DATOSCLIENTE.'\');\">'.  Functions::textoLargo($cliente,35).'</a>';
                $nombre .= '<br /><span class=\"badge bg-color-blue font10 \"  >Creado por: '.$aRow['vendedor'].'</span>';
                
                $total = $aRow['moneda'].' '.number_format($aRow['monto_total_final'],2);       
                if ($aRow['tipo_ingreso'] == 'V'){
                    if($aRow['monto_saldo'] > 0 && $aRow['estado'] == 'E'){
                        $saldo = '<span class=\"badge bg-color-red\">'.$aRow['moneda'].' '.number_format($aRow['monto_saldo'],2).'</span>';
                    }else{
                        $saldo = $aRow['moneda'].' '.number_format($aRow['monto_saldo'],2);
                    }                                
                }else{
                    $saldo = '————';
                }

                $codigo = $aRow['id_docventa'];
                if ($aRow['tipo_ingreso'] == 'V'){
                    if (!empty($aRow['codigo_impresion']) ){
                        $codigo .= '<br /><span class=\"badge bg-color-orange font10 \"  >CI: '.$aRow['codigo_impresion'].'</span>';
                    }   
                }else{
                    $codigo .= '<br /><span class=\"badge bg-color-red txt-color-white font10 \" >Ingreso</span>';
                }
                
                
                $fecha = Functions::cambiaf_a_normal($aRow['fecha']);
                $fecha .= '<br /><span class=\"badge bg-color-blueBlank font10 \"  >Caja: '.$aRow['id_caja'].'</span>';
                        
                
                /*datos de manera manual*/
                $sOutput .= '["'.$codigo.'","'.$nombre.'","'.$sucursal.'","'.  $fecha.'","'.$total.'","'.$saldo.'","'.$estado.'", ';
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $sOutput .= '"<div class=\"btn-group\">';                      
                
                 if($pagar['permiso'] && $aRow['tipo_ingreso'] == 'V'){
                    $sOutput .= '<button type=\"button\" class=\"'.$pagar['theme'].'\" title=\"'.VGEVE_32.'\" onclick=\"vseguimientoventa.getIndexPago(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$pagar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }else{
                    $sOutput .= '<button type=\"button\" class=\"'.$pagar['theme'].'\" title=\"'.$pagar['accion'].'\" disabled >';
                    $sOutput .= '    <i class=\"'.$pagar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                if($exportarpdf['permiso'] == 1 ){
                    $sOutput .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" onclick=\"generarVenta.postPDF(this,\''.$encryptReg.'\',\''.$aRow['codigo_impresion'].'\')\">';
                    $sOutput .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }else{
                     $sOutput .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" disabled >';
                    $sOutput .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                if($exportarexcel['permiso'] == 1 && $aRow['tipo_ingreso'] == 'V'){
                    $sOutput .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" onclick=\"generarVenta.postExcel(this,\''.$encryptReg.'\',\''.$aRow['codigo_impresion'].'\')\">';
                    $sOutput .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }else{
                    $sOutput .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" disabled >';
                    $sOutput .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                if($anular['permiso']){
                    if( $aRow['estado'] !== 'A'){
                        if ($aRow['caja'] > 0){
                            $sOutput .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" onclick=\"generarVenta.postAnularGenerarVenta(this,\''.$encryptReg.'\')\">';
                            $sOutput .= '    <i class=\"'.$anular['icono'].'\"></i>';
                            $sOutput .= '</button>';
                        }else{
                            $sOutput .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" disabled >';
                            $sOutput .= '    <i class=\"'.$anular['icono'].'\"></i>';
                            $sOutput .= '</button>';
                        }
                    }else{
                        $sOutput .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" disabled >';
                        $sOutput .= '    <i class=\"'.$anular['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }
                }
                                
                $sOutput .= ' </div>" ';

                $sOutput = substr_replace( $sOutput, "", -1 );
                $sOutput .= '],';
                $idx++;
            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;
    }
        
    public static function setIdVenta($id){
        Obj::run()->generarVentaModel->_idVenta = $id;
    }
    
    /*carga formulario (newGenerarVenta.phtml) para nuevo registro: GenerarVenta*/
    public function getFormNewGenerarVenta(){
       $nuevo   = Session::getPermiso("VGEVENEW");
       if($nuevo['permiso']){   
            Obj::run()->View->render("formNewGenerarVenta");
       }
    }
    
    public function getFormNewIngresoDirecto(){
       $nuevo   = Session::getPermiso("VGEVENEW");
       if($nuevo['permiso']){   
            Obj::run()->View->render("formNewIngresoDirecto");
       }
    }
    
    /*carga formulario (editGenerarVenta.phtml) para editar registro: GenerarVenta*/
    public function getFormProcesarGenerarVenta(){
        Obj::run()->View->render("formProcesarGenerarVenta");
    }
    
    /* Busqueda de Productos */
    public function getFormBuscarProductos(){
        $buscar = Session::getPermiso('VGEVEBS'); 
        if($buscar['permiso']){   
            Obj::run()->View->buscar = Session::getPermiso('VGEVEBS');        
            Obj::run()->View->nuevo = Session::getPermiso('VGEVENEW');
            Obj::run()->View->ventana = VGEVE;
            Obj::run()->View->onclickAdd = 'generarVentaScript.addProducto();';  
            Obj::run()->View->render("formBuscarProductos");
        }
    }    
    //Buscar productos al abrir ventana:    
    public function getFindProductos(){
        $data = Obj::run()->generarVentaModel->getFindProductos();
        
        return $data;
    }
    
    public function generarDocPDF(){
        $key= Functions::nombreAleatorio(0);
        $c = 'venta_'.Obj::run()->generarVentaModel->_idVenta.$key.'.pdf';
        $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

        $mpdf = new mPDF('c');          
        $dataPie = Obj::run()->plantillaDocController->getPlantillaDocumento('CARTAPIE');       
        $htmlPie = str_replace('\\','',$dataPie['cuerpo'] );     
        $htmlPie =   htmlspecialchars_decode($htmlPie,ENT_QUOTES);                        
        //{DATE j-m-Y}
        $mpdf->SetHTMLHeader('<br/><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" />','',TRUE);
        $mpdf->SetHTMLFooter($htmlPie.'<br/><table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>
                                <td width="33%"><span style="font-weight: bold;">{DATE j/m/Y}</span></td>
                                <td width="33%" align="center" style="font-weight: bold;">{PAGENO}/{nbpg}</td>
                                <td width="33%" style="text-align: right; ">'.LB_EMPRESA.'</td>
                             </tr></table>');

        $html = $this->getHtmlGenerarVenta($mpdf);         

        //$mpdf->WriteHTML($html);
        $mpdf->Output($ar,'F');

        $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
        echo json_encode($data);
    }
    
    public function postPDF(){
        $exportarpdf   = Session::getPermiso('VGEVEEP');
        if($exportarpdf['permiso']){     
            $this->generarDocPDF();
        }        
    }
    
    public function generarDocExcel(){
        $key= Functions::nombreAleatorio(0);
        $c = 'venta_'.Obj::run()->generarVentaModel->_idVenta.$key.'.xls';
        $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

        $html = $this->getHtmlGenerarVenta("EXCEL");

        $f=fopen($ar,'wb');
        if(!$f){$data = array('result'=>2);}
        fwrite($f,  utf8_decode($html));
        fclose($f);

        $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
        echo json_encode($data);
    }

    public function postExcel(){
        $exportarexcel = Session::getPermiso('VGEVEEX');   
        if($exportarexcel['permiso']){     
             $this->generarDocExcel();
        }
    }
    
    private function getHtmlGenerarVenta($mpdf){
        $dataC = Obj::run()->generarVentaModel->getFindVenta();
        $dataD = Obj::run()->generarVentaModel->getFindVentaD();
        $dataPago= Obj::run()->generarVentaModel->getFindPagos();
        
        $mon = $dataC['moneda'].' ';              
        switch ($dataC['estado']){
            case 'E': $estado = LABEL_EMITIDO; break;
            case 'A': $estado = LABEL_ANULADO; break;
            case 'P': $estado = LABEL_PAGADO; break;    
            case 'M': $estado = LABEL_MODIFICADO; break;  
            case 'I': $estado = LABEL_INHABILITADO; break;  
        }                        
        
        if((!empty($dataC['empresa']) || $dataC['empresa'] !== '' ) && (!empty($dataC['cliente']) || $dataC['cliente'] !== '' )){
            $cliente = $dataC['empresa'].' | '.$dataC['cliente'];                   
        }else{
             if(!empty($dataC['empresa']) || $dataC['empresa'] !== '' ){
                  $cliente = $dataC['empresa'];
             }elseif (!empty($dataC['cliente']) || $dataC['cliente'] !== '' ){
                 $cliente = $dataC['cliente'];
             }                   
        }          
        $cliente = Functions::htmlRender($cliente);
        
        if ($dataC['tipo_ingreso'] === 'V'):
            $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
            $htmlDet .= '<thead><tr><th style="width:5%">Item</th>';
            $htmlDet .= '<th style="width:40%">Descripción del Producto / Servicio</th>';
            $htmlDet .= '<th style="width:12%">Unid. Medid</th>';
            $htmlDet .= '<th style="width:12%">Cantidad</th>';
            $htmlDet .= '<th style="width:12%">Precio</th>';
            $htmlDet .= '<th style="width:12%">Importe</th>';
            $htmlDet .= '</tr></thead>';        

            $i =1;
            $subtotal = 0;
            $impuesto = 0;
            $total = 0;

            foreach ($dataD as $value) {

                if ($value['cantidad_multiple'] == '1'){
                    $cantidad = number_format($value['cantidad_1'],2).' x '.number_format($value['cantidad_2'],2);
                }else{
                    $cantidad = number_format($value['cantidad_real'],2);
                }
                if($value['id_producto'] == 201600000001)
                    $descripcion = nl2br( htmlspecialchars_decode($value['descripcion'],ENT_QUOTES));
                else
                    $descripcion =  $value['producto'];

                $htmlDet .= '<tr>
                    <td valign="top" style="text-align:center">'.($i++).'</td>
                    <td valign="top">'.$descripcion.'</td>
                    <td valign="top" style="text-align:center">'.$value['sigla'].'</td>                    
                    <td valign="top" style="text-align:center">'.$cantidad.'</td>
                    <td valign="top" style="text-align:right;">'.$mon.number_format($value['precio'],2).'</td>
                    <td valign="top" style="text-align:right">'.$mon.number_format($value['importe'],2).'</td>
                </tr>';
                $subtotal +=$value['importe_afectado'];
                $impuesto += $value['impuesto'];
                $total += $value['total_impuesto'];
            }            
             $htmlDet .= '<tr>';
            if ($dataC['incl_igv'] == 'N')
                $htmlDet .= '<td colspan="4" style="text-align:right"><h3>NO INCLUYE IGV  </h3></td>';        
            else
                $htmlDet .= '<td colspan="4" style="text-align:right"></td>';
            $htmlDet .= '<td>Sub Total:</td><td  style="text-align:right; font-weight:bold;">'.$mon.number_format($subtotal,2).'</td></tr>';
            $htmlDet .= '<tr><td colspan="4"></td><td>Impuesto ('.($dataC['porcentaje_igv']*100).'%):</td><td style="text-align:right; font-weight:bold;">'.$mon.number_format($impuesto,2).'</td></tr>';
            $htmlDet .= '<tr>';
            if ($dataC['incl_igv'] == 'S')
                $htmlDet .= '<td colspan="4" style="text-align:right"><h3>INCLUYE IGV  </h3></td>';        
            else
                $htmlDet .= '<td colspan="4" style="text-align:right"></td>';        

            $htmlDet .= '<td style="text-align:right;" >Total:</td><td style="text-align:right; font-weight:bold;">'.$mon.number_format($total,2).'</td></tr>';
            $htmlDet .='</table>';

            // Detalle de Pagos:
            $htmlPag .= '<table id="td2" style="border-collapse:collapse;" border="1">';
            $htmlPag .= '<tr><th style="width:7%">Item</th>';
            $htmlPag .= '<th style="width:15%">Fecha</th>';               
            $htmlPag .= '<th style="width:20%">Método de Pago</th>'; 
            $htmlPag .= '<th style="width:20%">Tipo Documento</th>'; 
            $htmlPag .= '<th style="width:15%">Serie-Número</th>'; 
            $htmlPag .= '<th style="width:10%">Monto</th>';        
            $htmlPag .= '<th style="width:10%">Estado</th>';   
            $htmlPag .= '</tr>';        
            $i =1;      
            foreach ($dataPago as $value) {
                $mon =   $value['moneda'].' ';
                 switch ($value['estado']){
                    case 'E': $estado = LABEL_EMITIDO; break;
                    case 'A': $estado = LABEL_ANULADO; break;    
                    case 'P': $estado = LABEL_PAGADO; break;    
                    case 'M': $estado = LABEL_MODIFICADO; break;  
                    case 'I': $estado = LABEL_INHABILITADO; break;  
                    case 'R': $estado = LABEL_PROGRAMADO; break;
                }               
                if($value['estado'] === 'E' || $value['estado'] === 'R' )
                    $metodPago = '- NINGUNO -';
                else
                    $metodPago = $value['metodopago'];
                
                $htmlPag .= '<tr>
                    <td style="text-align:center">'.($i++).'</td>
                    <td style="text-align:center;">'.$value['fecha'].'</td>
                    <td style="text-align:center;">'.$metodPago.'</td>
                    <td style="text-align:center;">'.$value['tipo_doc'].'</td>
                    <td style="text-align:center;">'.$value['serie'].'-'.$value['numero'].'</td>
                    <td style="text-align:right;">'.$mon.number_format($value['monto_pagado'],2).'</td>
                    <td style="text-align:center;">'.$estado.'</td>
                </tr>';        
            }        
            $htmlPag .='</table>';        

            $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('DOCVENTA');                              
            $html = str_replace('\\','',$dataHtml['cuerpo'] );        
            $html =   htmlspecialchars_decode($html,ENT_QUOTES);
            $html = str_replace('{{CODIGO}}',$dataC['id_docventa'].'&nbsp;', $html); 
            $html = str_replace('{{FECHA}}',$dataC['fecha'], $html);
            $html = str_replace('{{CLIENTE}}',$cliente, $html);
            $html = str_replace('{{ESTADO}}',$estado, $html);
            $html = str_replace('{{TIPODOC}}',$dataC['tipoDoc'], $html);
            $html = str_replace('{{CODIMPRESION}}',$dataC['codigo_impresion'], $html);
            $html = str_replace('{{NUMDOC}}',$dataC['numDoc'], $html);

            $html = str_replace('{{DETALLE_VENTA}}',$htmlDet, $html);
            $html = str_replace('{{DETALLE_PAGO}}',$htmlPag, $html);
            $obs = htmlspecialchars_decode(str_replace('\\','',$dataC['observacion'] ),ENT_QUOTES);
            $html = str_replace('{{OBSERVACION}}',$obs, $html);   
            
        elseif ($dataC['tipo_ingreso'] === 'I'):
            $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('DOCINGRESO');                              
            $html = str_replace('\\','',$dataHtml['cuerpo'] );        
            $html =   htmlspecialchars_decode($html,ENT_QUOTES);
            $html = str_replace('{{CODIGO}}',$dataC['id_docventa'].'&nbsp;', $html); 
            $html = str_replace('{{FECHA}}',$dataC['fecha'], $html);
            $html = str_replace('{{CLIENTE}}',$cliente, $html);
            $html = str_replace('{{ESTADO}}',$estado, $html);
            $obs  = '<hr><b>Concepto</b><br/>';                  
            $obs .= htmlspecialchars_decode(str_replace('\\','',$dataC['observacion'] ),ENT_QUOTES);
            $obs .= '<hr><br/> <b>Ingreso: </b><br />'.$dataC['moneda'].' '.number_format($dataC['monto_total_final'],2);
            $html = str_replace('{{DETALLE_INGRESO}}',$obs, $html); 
        endif;                   
             
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                   
            if($dataC['estado'] == 'A'){
               $mpdf->SetWatermarkText('A N U L A D O');
               $mpdf->watermarkTextAlpha = 1;
               $mpdf->showWatermarkText = true;      
            } 
            $mpdf->WriteHTML($html);      
        }      
    }
    
    /*envia datos para grabar registro: GenerarVenta*/
    public function postNewGenerarVenta(){
        $grabar = Session::getPermiso('VGEVEGR');
        if($grabar['permiso']){  
            $data = Obj::run()->generarVentaModel->newGenerarVenta();        
            echo json_encode($data);
        }
    }
    
    public function postNewIngresoDirecto(){
        $grabar = Session::getPermiso('VGEVEGR');
        if($grabar['permiso']){  
            $data = Obj::run()->generarVentaModel->postNewIngresoDirecto();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: GenerarVenta*/
    public function postEditGenerarVenta(){
        $data = Obj::run()->generarVentaModel->editGenerarVenta();
        
        echo json_encode($data);
    }
    
    /*envia datos para eliminar registro: GenerarVenta*/
    public function postAnularGenerarVenta(){
        $anular = Session::getPermiso("VGEVEAN");
        if($anular['permiso']){ 
            $data = Obj::run()->generarVentaModel->anularGenerarVenta();
            echo json_encode($data);
        }
    }
        
    public function getFindVenta(){
        $data = Obj::run()->generarVentaModel->getFindVenta();
        
        return $data;
    }        
    public function getFindVentaD(){
        $data = Obj::run()->generarVentaModel->getFindVentaD();
        
        return $data;
    }    
    
    public static function getCodigo(){ 
        $data = Obj::run()->generarVentaModel->getGenerarCodigo();        
        return $data;
    }       
        
    public static function getTipoDocumento(){ 
        $data = Obj::run()->generarVentaModel->getTipoDocumento();        
        return $data;
    }    
    
     public function getFindCotizacion(){
        $idCotizacion =Obj::run()->generarVentaModel->_idCotizacion;
        Obj::run()->generarCotizacionController->setIdCotizacion($idCotizacion);
        $data = Obj::run()->generarCotizacionController->getFindCotizacion();
        
        return $data;
    }        
    public function getFindCotizacionD(){
        $idCotizacion =Obj::run()->generarVentaModel->_idCotizacion;
        Obj::run()->generarCotizacionController->setIdCotizacion($idCotizacion);
        $data = Obj::run()->generarCotizacionController->getFindCotizacionD();
        
        return $data;
    }    
    
}

?>
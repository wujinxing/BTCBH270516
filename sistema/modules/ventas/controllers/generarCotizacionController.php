<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 05-09-2014 23:09:58 
* Descripcion : generarCotizacionController.php
* ---------------------------------------
*/    

class generarCotizacionController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'generarCotizacion'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vproducto')); 
        $this->loadController(array('modulo' => 'usuarios', 'controller' => 'configurarUsuarios'));      
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc'));   
    }
    
    public function index(){ 
        $main = Session::getMain('VCOTI');
        if($main['permiso']){
           Obj::run()->View->render("indexGenerarCotizacion");      
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }            
    }
    
    public function getFormViewDocumentos(){ 
        $main = Session::getMain('VCOTI');
        if($main['permiso']){
            Obj::run()->View->render('formViewDocumentos');    
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }            
    }    
    
    public function getFormClonarCotizacion(){    
        $clonar   = Session::getPermiso('VCOTICL');
        if($clonar['permiso']){     
            Obj::run()->View->render('formClonarGenerarCotizacion');
        }
    }    
              
    public function getFormMigrarCotizacion(){    
        $clonar   = Session::getPermiso('VCOTIMIGR');
        if($clonar['permiso']){     
            Obj::run()->View->render('formMigrarCotizacion');
        }
    }  
    
    public function getGridGenerarCotizacion(){
       $exportarpdf   = Session::getPermiso('VCOTIEP');
       $exportarexcel = Session::getPermiso('VCOTIEX');
       $enviaremail   = Session::getPermiso('VCOTIEE');
       $clonar   = Session::getPermiso('VCOTICL');
       $generar   = Session::getPermiso('VCOTIGN');
       $anular = Session::getPermiso("VCOTIAN");      
       $migrar  = Session::getPermiso("VCOTIMIGR");
       
       $sEcho          =   $this->post('sEcho');        
       $rResult = Obj::run()->generarCotizacionModel->getGridGenerarCotizacion();
        
        $num = Obj::run()->generarCotizacionModel->_iDisplayStart;        
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
                $encryptReg = Aes::en($aRow['id_cotizacion']);
                
                switch($aRow['estado']){
                    case 'E':
                        $estado = '<span class=\"label label-default\">'.LABEL_PENDIENTE.'</span>';
                        break;       
                    case 'S':
                        $estado = '<span class=\"label label-warning\">'.LABEL_ENVIADO.'</span>';
                        break;                           
                    case 'A':
                        $estado = '<span class=\"label label-danger\">'.LABEL_ANULADO.'</span>';
                        break;        
                    case 'P':
                        $estado = '<span class=\"label label-info\">'.LABEL_ATENDIDO.'</span>';
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
                
                $nombreEmail = str_replace('&#039;',"´",$cliente);
                
                $nombre = '<a href=\"javascript:;\" onclick=\"persona.getDatosPersonales(\''.$idPersona.'\',\''.LABEL_DATOSCLIENTE.'\');\">'. Functions::textoLargo($cliente,43).'</a>';
                if(Session::get('sys_defaultRol') !== APP_COD_VENDEDOR){
                    $nombre .= '<br /><span class=\"badge bg-color-blue font10 \"  >Creado por: '.$aRow['vendedor'].'</span>';
                }
                $codigo = $aRow['id_cotizacion'].'';
                if (!empty($aRow['codigo_venta']) ){
                    $codigo .= '<br /><span class=\"badge bg-color-redLight font10 \"  >Orden: '.$aRow['codigo_venta'].'</span>';
                }
                
                //codigo_venta
                /*datos de manera manual*/
                $sOutput .= '["'.$codigo.'","'.$nombre.'","'.  Functions::cambiaf_a_normal($aRow['fecha']).'","'.$aRow['moneda'].' '.number_format($aRow['monto_total'],2).'","'.$estado.'", ';
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $sOutput .= '"<div class=\"btn-group\">';       

                                     
        
                if($exportarpdf['permiso'] == 1){
                    $sOutput .= '<button type=\"button\" class=\"'.$exportarpdf['theme'].'\" title=\"'.$exportarpdf['accion'].'\" onclick=\"vGenerarCotizacion.getFormViewDocumentos(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$exportarpdf['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }                    
                
                if($exportarexcel['permiso'] == 1){
                    $sOutput .= '<button type=\"button\" class=\"'.$exportarexcel['theme'].'\" title=\"'.$exportarexcel['accion'].'\" onclick=\"vGenerarCotizacion.postExcel(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$exportarexcel['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }
                
                if($enviaremail['permiso'] ){
                    if( ($aRow['estado'] == 'E' || $aRow['estado'] == 'S') && $aRow['email'] !== 'X' ){
                        $sOutput .= '<button type=\"button\" class=\"'.$enviaremail['theme'].'\" title=\"'.$enviaremail['accion'].'\" onclick=\"vGenerarCotizacion.getMensaje(this,\'' . $encryptReg . '\',\'' . $nombreEmail . '\',\'' . $aRow['email'] . '\',\'' . $aRow['vendedor'] . '\')\">';
                        $sOutput .= '    <i class=\"'.$enviaremail['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }else{
                        $sOutput .= '<button type=\"button\" class=\"'.$enviaremail['theme'].'\" title=\"'.$enviaremail['accion'].'\" disabled>';
                        $sOutput .= '    <i class=\"'.$enviaremail['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }
                }
                
                if($clonar['permiso'] == 1){
                    $sOutput .= '<button type=\"button\" class=\"'.$clonar['theme'].'\" title=\"'.$clonar['accion'].'\" onclick=\"vGenerarCotizacion.getClonarCotizacion(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$clonar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }    
                
                if($migrar['permiso'] == 1){
                    $sOutput .= '<button type=\"button\" class=\"'.$migrar['theme'].'\" title=\"'.$migrar['accion'].'\" onclick=\"vGenerarCotizacion.getFormMigrarCotizacion(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$migrar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                } 
                
                
                if($generar['permiso']){
                    if($aRow['estado'] == 'E' || $aRow['estado'] == 'S'){          
                        if(Obj::run()->generarCotizacionModel->_all == 'C' || Obj::run()->generarCotizacionModel->_all == 'N'):
                            if ($aRow['caja'] > 0){
                               $sOutput .= '<button type=\"button\" class=\"'.$generar['theme'].'\" title=\"'.$generar['accion'].' Venta\" onclick=\"generarVenta.getFormProcesarGenerarVenta(this,\''.$encryptReg.'\')\">';
                               $sOutput .= '    <i class=\"'.$generar['icono'].'\"></i>';
                               $sOutput .= '</button>';      
                            }else{
                               $sOutput .= '<button type=\"button\" class=\"'.$generar['theme'].'\" title=\"'.$generar['accion'].' Venta\" disabled >';
                               $sOutput .= '    <i class=\"'.$generar['icono'].'\"></i>';
                               $sOutput .= '</button>';      
                            }   
                        else:
                            $sOutput .= '<button type=\"button\" class=\"'.$generar['theme'].'\" title=\"'.$generar['accion'].' Venta\" onclick=\"generarVenta.getFormProcesarGenerarVenta(this,\''.$encryptReg.'\')\">';
                            $sOutput .= '    <i class=\"'.$generar['icono'].'\"></i>';
                            $sOutput .= '</button>';  
                        endif;                                                                                                             
                    }else{
                        $sOutput .= '<button type=\"button\" class=\"'.$generar['theme'].'\" title=\"'.$generar['accion'].'\" disabled >';
                        $sOutput .= '    <i class=\"'.$generar['icono'].'\"></i>';
                        $sOutput .= '</button>';
                    }
                }
                if($anular['permiso']){
                    if($aRow['estado'] == 'E' || $aRow['estado'] == 'S'){
                        $sOutput .= '<button type=\"button\" class=\"'.$anular['theme'].'\" title=\"'.$anular['accion'].'\" onclick=\"vGenerarCotizacion.postAnularVGenerarCotizacion(this,\''.$encryptReg.'\')\">';
                        $sOutput .= '    <i class=\"'.$anular['icono'].'\"></i>';
                        $sOutput .= '</button>';
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
        
    /*carga formulario (newGenerarCotizacion.phtml) para nuevo registro: GenerarCotizacion*/
    public function getFormNewVGenerarCotizacion(){
        $nuevo   = Session::getPermiso('VCOTINEW');
        if($nuevo['permiso']){     
            Obj::run()->View->render("formNewGenerarCotizacion");
        }
    }
    
    /*Formulario para procesar Cotizacion y convertir en Venta */
    public function getFormEditVGenerarCotizacion(){
        $generar   = Session::getPermiso('VCOTIGN');
        if($generar['permiso']){     
            Obj::run()->View->render("formEditGenerarCotizacion");
        }
    }
    
    /* Busqueda de Productos */
    public function getFormBuscarProductos(){
        $buscar = Session::getPermiso('VCOTIBS');
        if($buscar['permiso']){     
            Obj::run()->View->buscar = Session::getPermiso('VCOTIBS');        
            Obj::run()->View->nuevo = Session::getPermiso('VCOTINEW');
            Obj::run()->View->ventana = VCOTI;
            Obj::run()->View->onclickAdd = 'vGenerarCotizacionScript.addProducto();';  
            Obj::run()->View->render("formBuscarProductos");
         }else{
            echo 'Ud. no tiene permiso para ingresar a esta ventana.';
        }    
    }        

    public function postPDF($n=''){
        
        $exportarpdf   = Session::getPermiso('VCOTIEP');
        if($exportarpdf['permiso']){     
            
            $key= Functions::nombreAleatorio(0);
            $c = 'cotizacion_sevend_nro_'.Obj::run()->generarCotizacionModel->_idCotizacion.$key.'.pdf';
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
            
            $html = $this->getHtmlGenerarCotizacion($mpdf);         

            $mpdf->Output($ar,'F');

            if($n != 'N'){
                $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
                echo json_encode($data);
            }else{
                return $c;
            }
        }
    }
            
    public function postExcel(){
        $exportarexcel = Session::getPermiso('VCOTIEX');
        if($exportarexcel['permiso']){     
            
            $key= Functions::nombreAleatorio(0);
            $c = 'venta_'.Obj::run()->generarCotizacionModel->_idCotizacion.$key.'.xls';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;
            
            $html = $this->getHtmlGenerarCotizacion("EXCEL");

            $f=fopen($ar,'wb');
            if(!$f){$data = array('result'=>2);}
            fwrite($f,  utf8_decode($html));
            fclose($f);

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }
    }
    
    private function getHtmlGenerarCotizacion($mpdf){
        $dataC = Obj::run()->generarCotizacionModel->getFindCotizacion();
        $dataD = Obj::run()->generarCotizacionModel->getFindCotizacionD();               
        $mon = $dataC['moneda'].' ';
        
        switch ($dataC['estado']){
            case 'E': $estado = LABEL_PENDIENTE; break;
            case 'S': $estado = LABEL_ENVIADO; break;
            case 'A': $estado = LABEL_ANULADO; break;
            case 'P': $estado = LABEL_ATENDIDO; break;
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
                     
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('COTIVENTA');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        $html = str_replace('{{CODIGO}}',$dataC['id_cotizacion'].'&nbsp;', $html); 
        $html = str_replace('{{FECHA}}',$dataC['fecha'], $html);
        $html = str_replace('{{CLIENTE}}',$cliente, $html);
        $html = str_replace('{{ESTADO}}',$estado, $html);
        
        $html = str_replace('{{DETALLE_COTIZACION}}',$htmlDet, $html);
        $obs = htmlspecialchars_decode(str_replace('\\','',$dataC['observacion'] ),ENT_QUOTES);
        $html = str_replace('{{OBSERVACION}}',$obs, $html);        
        
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
    
    public function postPDFCarta($n=''){
        $exportarpdf   = Session::getPermiso('VCOTIEP');
        if($exportarpdf['permiso']){   
            $key= Functions::nombreAleatorio(0);
            $c = 'carta_intencion_sevend_nro_'.Obj::run()->generarCotizacionModel->_idCotizacion.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;      

            $mpdf = new mPDF('c');
            //$mpdf->AddPage(); -> Agrega otra página.
            $dataC = Obj::run()->generarCotizacionModel->getFindCotizacion();
            $dataPie = Obj::run()->plantillaDocController->getPlantillaDocumento('CARTAPIE');       
            $htmlPie = str_replace('\\','',$dataPie['cuerpo'] );     
            $htmlPie =   htmlspecialchars_decode($htmlPie,ENT_QUOTES);

            $mpdf->SetHTMLHeader('<br/><br/><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" />','',TRUE);
            $mpdf->SetHTMLFooter($htmlPie);

            $mpdf->SetHTMLFooter($htmlPie.'<br/><table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>                                
                                    <td width="33%"><span style="font-weight: bold;"></span></td>
                                    <td width="33%" align="center" style="font-weight: bold;">{PAGENO}/{nbpg}</td>
                                    <td width="33%" style="text-align: right; ">'.LB_EMPRESA.'</td>                                
                                 </tr></table>');        

            if((!empty($dataC['empresa']) || $dataC['empresa'] !== '' ) && (!empty($dataC['cliente']) || $dataC['cliente'] !== '' )){
                $cliente = $dataC['cliente'].'<br/>'.$dataC['empresa'];                   
            }else{
                 if(!empty($dataC['empresa']) || $dataC['empresa'] !== '' ){
                      $cliente = $dataC['empresa'];
                 }elseif (!empty($dataC['cliente']) || $dataC['cliente'] !== '' ){
                     $cliente = $dataC['cliente'];
                 }                   
            }    
            $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('CARTAPRE01');                              
            $html = str_replace('\\','',$dataHtml['cuerpo'] );        
            $html =   htmlspecialchars_decode($html,ENT_QUOTES);
            $html = str_replace('{{FECHA_CARTA}}',Functions::formato_fecha($dataC['fechaRpt'],"%d de %B del %Y"), $html);
            $html = str_replace('{{CLIENTE}}',$cliente, $html);
            $html = str_replace('{{VENDEDOR}}',Session::get('sys_nombreUsuario'), $html);
            $html = str_replace('{{TELEFONO_VENDEDOR}}',Session::get('sys_fono'), $html);        

            $mpdf->WriteHTML($html); 
            $mpdf->AddPage();
            $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('CARTAPRE02');   
            $html = str_replace('\\','',$dataHtml['cuerpo'] );    
            $html =   htmlspecialchars_decode($html,ENT_QUOTES);        
            $mpdf->WriteHTML($html);              
            $mpdf->AddPage();
            $html = '<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><img src="'.ROOT.'public'.DS.'img'.DS.'sevend_grande.jpg" width="770" height="403" />';
            $mpdf->WriteHTML($html);

            $mpdf->Output($ar,'F');

            if($n != 'N'){
                $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
                echo json_encode($data);
            }else{
                return $c;
            }
        }
    }    
            
    public function getViewMensaje(){ 
        $enviaremail   = Session::getPermiso('VCOTIEE');
        if($enviaremail['permiso']){   
            $idd = Formulario::getParam('_idCotizacion');
            $nombres = Formulario::getParam('_nombres');
            $email = Formulario::getParam('_mail');
            $vendedor = Formulario::getParam('_vendedor');

            $msj = Obj::run()->mensajesPlantillaController->getPlantillaMensaje('COTIZAMSJ', 1);                              
            $body = str_replace('\\','',$msj['cuerpo'] );
            $body = htmlspecialchars_decode($body,ENT_QUOTES);                
            $body = str_replace('{{CODIGO}}',Aes::de($idd), $body);
            $body = str_replace('{{NOMBRES}}',$nombres, $body);
            $body = str_replace('{{VENDEDOR}}',$vendedor, $body);
            $body = str_replace('{{ANIO}}', date('Y'), $body); 

            $exportarpdf = Session::getPermiso('VCOTIEP');
            if($exportarpdf["permiso"]):
                $btn .= '<button type="button" class="'.$exportarpdf['theme'].' margin-top-5 padding-5" title="'.VCOTI_8.'" onclick="vGenerarCotizacion.postPDFCarta(this,\''.$idd.'\')">';
                $btn .= '    <i class="'.$exportarpdf['icono'].'"></i> '. VCOTI_8;
                $btn .= '</button>';
                $btn .= ' ';
                $btn .= '<button type="button" class="'.$exportarpdf['theme'].' margin-top-5 padding-5" title="'.VCOTI_7.'" onclick="vGenerarCotizacion.postPDF(this,\''.$idd.'\')">';
                $btn .= '    <i class="'.$exportarpdf['icono'].'"></i> '. VCOTI_7;
                $btn .= '</button>';
            endif;

            Obj::run()->View->adjunto = $btn ;        
            Obj::run()->View->mensaje = $body;        
            Obj::run()->View->email = $email;                
            Obj::run()->View->asunto = $msj['asunto'];
            Obj::run()->View->grabar = Session::getPermiso('VCOTIEE');
            Obj::run()->View->onclick = 'vGenerarCotizacion.postEmail("'.$idd.'","'.$nombres.'","'.$email.'")';         
            Obj::run()->View->render('formViewMensaje');      
        }
    }    
        
    public function postEmail(){                                         
        $enviaremail   = Session::getPermiso('VCOTIEE');
        if($enviaremail['permiso']){   
            //Generar Archivos:
            $carta = $this->postPDF('N');                
            $cotizacion = $this->postPDFCarta('N'); 

            $idd = Aes::de(Formulario::getParam('_idCotizacion'));
            $nombres = Formulario::getParam('_nombres');
            $email = Formulario::getParam('_mail');
                        
            $body= htmlspecialchars_decode( Formulario::getParam(T4.'txt_mensaje'),ENT_QUOTES);
            $asunto = Formulario::getParam(T4.'txt_asunto');

            $data = Obj::run()->parametroController->getParametros('EMAIL');   
            $data1 = Obj::run()->parametroController->getParametros('EMPR');       

            $emailEmpresa = $data['valor'];
            $empresa = $data1['valor'];                

            $mail = new PHPMailer(); // defaults to using php "mail()"
            //$mail->IsSMTP();    
            $mail->SetFrom($emailEmpresa, $empresa);
            $mail->AddAddress($email, $nombres);
            $mail->AddBCC($emailEmpresa, $empresa);
            $mail->Subject = $asunto;

            $mail->AddAttachment('public/files/'.Session::get('sys_idUsuario').'/'.$carta);      // attachment
            $mail->AddAttachment('public/files/'.Session::get('sys_idUsuario').'/'.$cotizacion);      // attachment
            $mail->MsgHTML($body);        
            $mail->CharSet = 'UTF-8';

            //Actualizar Estado Enviado.
            $data = Obj::run()->generarCotizacionModel->enviarCotizacion();

            /* validar si dominio de correo existe */
            if($data['result'] == '1'){
                if ($mail->Send()){
                    $data = array('result' => 1,'coti'=>$cotizacion,'carta'=>$carta);
                } else {
                    $data = array('result' => 2);
                }
            }
            echo json_encode($data);             
        }
    }    
    
    /*envia datos para grabar registro: GenerarCotizacion*/
    public function postNewGenerarCotizacion(){
        $grabar = Session::getPermiso("VCOTIGR");
        if($grabar['permiso']){
             $data = Obj::run()->generarCotizacionModel->newGenerarCotizacion();      
            echo json_encode($data);
        }
    }
    
    /*Procesar Cotizacion para Venta */
    public function postEditGenerarCotizacion(){
        $generar   = Session::getPermiso('VCOTIGN');
        if($generar['permiso']){
            $data = Obj::run()->generarCotizacionModel->editGenerarCotizacion(); 
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: GenerarCotizacion*/
    public function postAnularGenerarCotizacion(){      
        $anular = Session::getPermiso("VCOTIAN");
        if($anular['permiso']){
            $data = Obj::run()->generarCotizacionModel->anularGenerarCotizacion();
            echo json_encode($data);
        }        
    }
    
    /*envia datos para migrar Cotizacion*/
    public function postMigrarCotizacion(){
        $grabar = Session::getPermiso("VCOTIMIGR");
        if($grabar['permiso']){
             $data = Obj::run()->generarCotizacionModel->migrarCotizacion();      
            echo json_encode($data);
        }
    }
        
    
    public function getFindCotizacion(){
        $data = Obj::run()->generarCotizacionModel->getFindCotizacion();
        
        return $data;
    }        
    public function getFindCotizacionD(){
        $data = Obj::run()->generarCotizacionModel->getFindCotizacionD();
        
        return $data;
    }    
    
    public function setIdCotizacion($idCot){
       Obj::run()->generarCotizacionModel->_idCotizacion = $idCot;
    }
    
    public static function getEmpleadosAll(){
        $data = Obj::run()->generarCotizacionModel->getEmpleadosAll();
        
        return $data;
    }  
}
?>
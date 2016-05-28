<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 07-04-2016 17:04:08 
* Descripcion : reporteVendedorController.php
* ---------------------------------------
*/    

class reporteVendedorController extends Controller{

    public function __construct() {        
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'reporteVendedor'));        
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc'));  
    }
    
    public function index(){ 
        Obj::run()->View->render("indexReporteVendedor");
    }
    
    public function getGridReporteVendedor(){
                
        $pdf   = Session::getPermiso('VRPT4EP');   
        $excel   = Session::getPermiso('VRPT4EX');   
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->reporteVendedorModel->getReporteVendedor();
        
        $num = Obj::run()->reporteVendedorModel->_iDisplayStart;
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
                 
                 $axion = '"<div class=\"btn-group\">';
                 
                if($pdf['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$pdf['theme'].'\" title=\"'.$pdf['accion'].'\" onclick=\"reporteVendedor.postPDF(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$pdf['icono'].'\"></i>';
                    $axion .= '</button>';
                }    
                if($excel['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$excel['theme'].'\" title=\"'.$excel['accion'].'\" onclick=\"reporteVendedor.postExcel(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$excel['icono'].'\"></i>';
                    $axion .= '</button>';
                }  
                
                $axion .= ' </div>" ';
                               
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['id_persona'].'","'.$aRow['nombrecompleto'].'","'.$aRow['telefono'].'","'.$aRow['cotizacion'].'",'.$axion.' ';

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
            $key= Functions::nombreAleatorio(0);
            $c = 'reporte_vendedor_'.Obj::run()->reporteVendedorModel->_idVendedor.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

            $mpdf = new mPDF('c');
            $dataPie = Obj::run()->plantillaDocController->getPlantillaDocumento('CARTAPIE');       
            $htmlPie = str_replace('\\','',$dataPie['cuerpo'] );     
            $htmlPie =   htmlspecialchars_decode($htmlPie,ENT_QUOTES);                        
            //{DATE j-m-Y}
            $mpdf->SetHTMLHeader('<br/><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" />','',TRUE);
            $mpdf->SetHTMLFooter($htmlPie.'<br/><table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>
                                    <td width="33%"><span style="font-weight: bold;"></span></td>
                                    <td width="33%" align="center" style="font-weight: bold;">{PAGENO}/{nbpg}</td>
                                    <td width="33%" style="text-align: right; ">'.LB_EMPRESA.'</td>
                                 </tr></table>');

            $html = $this->getHtmlReporte($mpdf);         

            $mpdf->Output($ar,'F');

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }
        
    }
    
    public function postExcel(){
        $exportarexcel   = Session::getPermiso('VRPT4EX');
        if($exportarexcel['permiso']){        
            $key= Functions::nombreAleatorio(0);
            $c = 'reporte_vendedor_'.Obj::run()->reporteVendedorModel->_idVendedor.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

            $html = $this->getHtmlReporte("EXCEL");

            $f=fopen($ar,'wb');
            if(!$f){$data = array('result'=>2);}
            fwrite($f,  utf8_decode($html));
            fclose($f);

            $data = array('result'=>1,'archivo'=>Session::get('sys_idUsuario').'/'.$c);
            echo json_encode($data);
        }
    }    
        
    private function getHtmlReporte($mpdf){
        $dataC = Obj::run()->reporteVendedorModel->getListaCotizaciones();        
        $f1 = Functions::cambiaf_a_normal(Obj::run()->reporteVendedorModel->_f1);
        $f2 = Functions::cambiaf_a_normal(Obj::run()->reporteVendedorModel->_f2);
        
        //Detalle:
        $htmlDet .='<table id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<tr><th style="width:15%">N° Cotización</th>';
        $htmlDet .= '<th colspan="2" style="width:40%">Empresa / Cliente</th>';
        $htmlDet .= '<th style="width:10%">Fecha</th>';        
        $htmlDet .= '<th style="width:10%">Estado</th>';  
        $htmlDet .= '<th style="width:15%">Total</th>';        
        $htmlDet .= '</tr>';        
        
        $i =1;
        $subtotal = 0;
        $impuesto = 0;
        $total = 0;
        
        foreach ($dataC as $value) {
            
            switch ($value['estado']){
                case 'E': $estado = LABEL_PENDIENTE; break;
                case 'S': $estado = LABEL_ENVIADO; break;
                case 'A': $estado = LABEL_ANULADO; break;
                case 'P': $estado = LABEL_ATENDIDO; break;
            }     
            $mon = $value['moneda'].' ';     
            $htmlDet .= '<tr>
                <td style="text-align:center">'.$value['id_cotizacion'].'</td>
                <td colspan="2" >'.$value['empresa'].'<br/><span style="font-size:10px; color:blue">Contacto: '.$value['cliente'].'</span></td>
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value['fecha']).'</td>                    
                <td style="text-align:center">'.$estado.'</td>
                <td style="text-align:right">'.$mon.number_format($value['monto_total'],2).'</td>
            </tr>';          
            $total += $value['monto_total'];
        }            
        $htmlDet .= '<tr><td colspan="4"></td><td>Total:</td><td  style="text-align:right; font-weight:bold;">'.$mon.number_format($total,2).'</td></tr>';      
        $htmlDet .='</table>';
                        
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('RPTVENDEDO');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        $html = str_replace('{{CODIGO}}',$dataC[0]['id_persona'], $html); 
        $html = str_replace('{{VENDEDOR}}',$dataC[0]['vendedor'], $html);
        $html = str_replace('{{F1}}',$f1, $html);
        $html = str_replace('{{F2}}',$f2, $html);
        
        $html = str_replace('{{COTIZACION}}',$htmlDet, $html);
                                  
        
        if ($mpdf == 'EXCEL'){
           return $html; 
        }        
        $mpdf->WriteHTML($html);               
    }               
        
    
    
}

?>
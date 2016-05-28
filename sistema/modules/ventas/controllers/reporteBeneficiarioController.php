<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-05-2016 18:05:40 
* Descripcion : reporteBeneficiarioController.php
* ---------------------------------------
*/    

class reporteBeneficiarioController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'reporteBeneficiario'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));  
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc')); 
        $this->loadController(array('modulo'=>'configuracion','controller'=>'parametro'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vegresos'));
    }
    
    public function index(){
        $main = Session::getMain("VRPT7");
        if($main["permiso"]){
            Obj::run()->View->render("indexReporteBeneficiario");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridReporteBeneficiario(){
         
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->reporteBeneficiarioModel->getReporteBeneficiario();
        
        $num = Obj::run()->reporteBeneficiarioModel->_iDisplayStart;
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
                 $descripcion = preg_replace ('[\n|\r|\n\r]','',$aRow['descripcion']);

                /*registros a mostrar*/
                $sOutput .= '["'.$aRow['id_egreso'].'","'.$aRow['sucursal'].'","'.$descripcion.'","'.Functions::cambiaf_a_normal($aRow['fecha']).'","'.$aRow['moneda'].' '.number_format($aRow['monto'],2).'","'.$estado.'" ';


                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function postPDFGeneral(){
        $exportarpdf   = Session::getPermiso('VRPT4EP');
        if($exportarpdf['permiso']){  
            $key = Functions::nombreAleatorio(0);
            $c = 'reporte_beneficiario'.$key.'.pdf';
            
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
            $c = 'reporte_beneficiario'.$key.'.xls';
            
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
        $dataC = Obj::run()->reporteBeneficiarioModel->getFindResumenUnitario();
        $f1 = Functions::cambiaf_a_normal(Obj::run()->reporteBeneficiarioModel->_fecha1);
        $f2 = Functions::cambiaf_a_normal(Obj::run()->reporteBeneficiarioModel->_fecha2);
        
        if(($dataC[0]['templeado'] == 'S' ) ){
            $beneficiario = ' '.$dataC[0]['empleado'];                   
        }elseif(($dataC[0]['tproveedor'] == 'S' ) ){                  
            $beneficiario = ' '.$dataC[0]['proveedor'];                             
        }else{
            $beneficiario = LB_EMPRESA;
        }
        $ciudad = $dataC[0]['ciudad'];
        $sucursal= $dataC[0]['sucursal'];
                
        $htmlDet ='<style>.fila{background: #EFEFEF;}</style>'; 
        $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<thead>';
        $htmlDet .= '<tr><th style="width:5%">N°</th>';
        $htmlDet .= '<th style="width:8%">ID</th>';                                                                 
        $htmlDet .= '<th style="width:10%">Fecha</th>';      
        $htmlDet .= '<th style="width:40%">Concepto</th>';
        $htmlDet .= '<th style="width:20%">Monto</th>';                                           
        $htmlDet .= '</tr></thead>';         
        $importe = 0;
        $i = 1;
        foreach ($dataC as $value) {      
            $mon = $value['moneda'].' ';
            $importe += $value['monto'];                                                   
            $htmlDet .= '<tr>
                <td style="text-align:center">'.($i).'</td>
                <td style="text-align:center">'.$value['id_egreso'].'&nbsp;</td>                
                <td style="text-align:center">'.  Functions::cambiaf_a_normal($value['fecha']).'</td>                
                <td style="text-align:left">'.$value['descripcion'].'</td>                
                <td style="text-align:right">'.$mon.number_format($value['monto'],2).'</td>
            </tr>';                                                                      
            $i++;
        }        
        $htmlDet .= '<tr><td colspan="4" style="text-align:right;" >Total:</td>';        
        $htmlDet .= '<td style="text-align:right; font-weight:bold;">'.$mon.number_format($importe,2).'</td>';                
        $htmlDet .= '</tr>';                  
        $htmlDet .='</table>';               
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('BENEFI01');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        $html = str_replace('{{BENEFICIARIO}}',$beneficiario, $html);
        $html = str_replace('{{CIUDAD}}',$ciudad, $html);
        $html = str_replace('{{SUCURSAL}}',$sucursal, $html);
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
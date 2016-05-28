<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 20-11-2014 17:11:15 
* Descripcion : reporteGraficoMesController.php
* ---------------------------------------
*/    

class reporteGraficoMesController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'reporteGraficoMes'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc'));  
    }
    
    public function index(){ 
        $main = Session::getMain("VRPT3");
        if($main["permiso"]){
            Obj::run()->View->render("indexReporteGraficoMes");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                        
    }
    
    public function getGrafico() {
        $data = Obj::run()->reporteGraficoMesModel->getGrafico();
        echo json_encode($data);
        
    }
    
    public static function getAnioAll(){
        $data = Obj::run()->reporteGraficoMesModel->getAnioAll();
        return $data;
    } 
    public function postPDF(){
        $exportarpdf   = Session::getPermiso('VRPT3EP');
        if($exportarpdf['permiso']){  
            
            $key= Functions::nombreAleatorio(0);
            $c = 'venta_'.Obj::run()->reporteGraficoMesModel->_periodo.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;

            $mpdf = new mPDF('c');                                  
            $mpdf->SetHTMLHeader('<br/><img src="'.ROOT.'public'.DS.'img'.DS.'logotipo.png" width="137" height="68" />','',TRUE);
            $mpdf->SetHTMLFooter('<br/><table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 10pt; color: #000000; font-weight: bold;"><tr>
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
        $exportarexcel = Session::getPermiso('VRPT3EX');   
        if($exportarexcel['permiso']){     
            $key= Functions::nombreAleatorio(0);
            $c = 'venta_'.Obj::run()->reporteGraficoMesModel->_periodo.$key.'.xls';
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
        $dataC = Obj::run()->reporteGraficoMesModel->getGrafico();
        $periodo = Obj::run()->reporteGraficoMesModel->_periodo;
        $nombreMes = Functions::nombreMes(Obj::run()->reporteGraficoMesModel->_mes);
        $sucursal = $dataC[0]['sucursal_descripcion'].' ('.$dataC[0]['sucursal'].')';
        
        $htmlDet .= '<table repeat_header="1" id="td2" style="border-collapse:collapse" border="1">';
        $htmlDet .= '<thead><tr><th style="width:10%">N°</th>';
        $htmlDet .= '<th style="width:50%">Fecha</th>';                       
        $htmlDet .= '<th style="width:40%">Saldo</th>';
        $htmlDet .= '</tr></thead>';    
        $i=1;
        $importe = 0;
        foreach ($dataC as $value) {                
            $importe += $value['monto'];
            $mon = $value['moneda'].' ';
            $fecha = $value['fecha'];
            $htmlDet .= '<tr>
                <td style="text-align:center">'.($i++).'</td>
                <td style="text-align:center">'.$fecha.'&nbsp;</td>
                <td style="text-align:right">'.$mon.number_format($value['monto'],2).'</td>
            </tr>           
            ';                            
        }              
        $htmlDet .='</table>';               
                          
        $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('GRAFICO02');                              
        $html = str_replace('\\','',$dataHtml['cuerpo'] );        
        $html =   htmlspecialchars_decode($html,ENT_QUOTES);
        
        $html = str_replace('{{PERIODO}}',$periodo, $html);  
        $html = str_replace('{{SUCURSAL}}',$sucursal, $html);  
        $html = str_replace('{{MES}}', $nombreMes, $html);  
        $html = str_replace('{{DETALLE_RESUMEN}}',$htmlDet, $html);
        
        if ($mpdf == 'EXCEL'){
           return $html;         
        }else{                               
            $mpdf->WriteHTML($html);      
        }      
    }
}

?>
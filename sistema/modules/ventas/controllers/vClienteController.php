<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-11-2014 17:11:18 
* Descripcion : vClienteController.php
* ---------------------------------------
*/    

class vClienteController extends Controller{
    
    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'vCliente'));
        $this->loadController(array('modulo'=>'usuarios','controller'=>'persona')); 
        $this->loadController(array('modulo' => 'usuarios', 'controller' => 'configurarUsuarios'));
        $this->loadController(array('modulo'=>'configuracion','controller'=>'plantillaDoc'));           
    }
    
    public function index(){ 
        $main = Session::getMain('VRECL');
        if($main['permiso']){
            Obj::run()->View->render("indexVcliente");        
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }            
    }
    
    public function getGridVcliente(){
        $editar   = Session::getPermiso('VRECLED');
        $eliminar = Session::getPermiso('VRECLDE');
         
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vClienteModel->getVcliente();
        
        $num = Obj::run()->vClienteModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow  ){
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_persona']);
                
                /*campo que maneja los estados, para el ejemplo aqui es ACTIVO, coloca tu campo*/
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"vCliente.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"vCliente.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-danger\">'.LABEL_DESACT.'</span>';
                    }
                }
                                                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"vCliente.getFormEditVcliente(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    $uso = $aRow['tVentas'];
                    if( $uso === 0):
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"vCliente.postDeleteVcliente(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';    
                    else:
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    endif;                                         
                }
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/                
                if((!empty($aRow['empresacliente']) || $aRow['empresacliente'] !== '' ) && (!empty($aRow['nombrecompleto']) || $aRow['nombrecompleto'] !== '' )){
                    $cliente = $aRow['empresacliente'].' | '.$aRow['nombrecompleto'];                   
                }else{
                     if(!empty($aRow['empresacliente']) || $aRow['empresacliente'] !== '' ){
                          $cliente = $aRow['empresacliente'];
                     }elseif (!empty($aRow['nombrecompleto']) || $aRow['nombrecompleto'] !== '' ){
                         $cliente = $aRow['nombrecompleto'];
                     }                   
                }          
                 $cliente = Functions::textoLargo($cliente,45);
                 $cliente .= '<br /><span class=\"badge bg-color-orange font10 \"  >ID: '.$aRow['id_persona'].'</span>';
                
                $sOutput .= '["'.($num++).'","'. $cliente .'","'.$aRow['telefono'].'","'.$aRow['ciudad'].'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function getFormBuscarCliente(){ 
        $tab =  Obj::run()->vClienteModel->_tab;        
        $buscar = Session::getPermiso($tab.'BS'); 
        if($buscar['permiso']){   
            Obj::run()->View->buscar = Session::getPermiso($tab.'BS');        
            Obj::run()->View->nuevo = Session::getPermiso($tab.'NEW');
            Obj::run()->View->ventana = $tab;
            Obj::run()->View->render('formBuscarCliente');
        }
    }              
    
    public function getBuscarCliente(){
        $tab =  Obj::run()->vClienteModel->_tab;
        $funcion =  Obj::run()->vClienteModel->_funcionExterna;
        $buscar = Session::getPermiso($tab.'BS');
                
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vClienteModel->getBuscarCliente();
        
        $num = Obj::run()->vClienteModel->_iDisplayStart;
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
                                                
                if((!empty($aRow['empresacliente']) || $aRow['empresacliente'] !== '' ) && (!empty($aRow['nombrecompleto']) || $aRow['nombrecompleto'] !== '' )){
                    $cliente = $aRow['empresacliente'].' | '.$aRow['nombrecompleto'];                   
                }else{
                     if(!empty($aRow['empresacliente']) || $aRow['empresacliente'] !== '' ){
                          $cliente = $aRow['empresacliente'];
                     }elseif (!empty($aRow['nombrecompleto']) || $aRow['nombrecompleto'] !== '' ){
                         $cliente = $aRow['nombrecompleto'];
                     }                   
                }                
                                  
                $cliente = str_replace('&#039;',"´",$cliente); 
                
                $nombre = '<a href=\"javascript:;\" onclick=\"simpleScript.setInput({'.$tab.'txt_idpersona:\''.$encryptReg.'\', '
                        . ''.$tab.'txt_cliente:\''.addslashes($cliente).'\'},\'#'.$tab.'formBuscarCliente\'); '.$funcion.' \" >'
                        . ''.($cliente).'</a>';                
                
                 $axion = '"<div class=\"btn-group\">';
                 
                if($buscar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$buscar['theme'].' padding-5\" title=\"'.LABEL_SELECCIONAR.'\" onclick=\"simpleScript.setInput({'.$tab.'txt_idpersona:\''.$encryptReg.'\', '
                        . ''.$tab.'txt_cliente:\''.addslashes($cliente).'\'},\'#'.$tab.'formBuscarCliente\'); '.$funcion.' \">';
                    $axion .= '    <i class=\"'.$buscar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                $axion .= ' </div>" ';
               
                /*datos de manera manual*/
                $sOutput .= '["'.($num++).'","'.$nombre.'" , '.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }           
        
    /*carga formulario (newVcliente.phtml) para nuevo registro: Vcliente*/
    public function getFormNewVcliente(){
        $nuevo   = Session::getPermiso('VRECLNEW');
        if($nuevo['permiso']){     
            Obj::run()->View->render("formNewVcliente");
        }
    }
    
    /*carga formulario (editVcliente.phtml) para editar registro: Vcliente*/
    public function getFormEditVcliente(){
        $editar   = Session::getPermiso('VRECLED');
        if($editar['permiso']){        
            Obj::run()->View->render("formEditVcliente");
        }
    }
    
    
    public function postPDFCarta($n=''){
        $exportarpdf   = Session::getPermiso('VRECLEP');  
        if($exportarpdf['permiso']){  
            $key= Functions::nombreAleatorio(0);
            $c = 'carta_intencion_sevend_'.Obj::run()->vClienteModel->_idPersona.$key.'.pdf';
            $ar = ROOT.'public'.DS.'files'.DS.Session::get('sys_idUsuario').DS.$c;
            
            $mpdf = new mPDF('c');
            //$mpdf->AddPage(); -> Agrega otra página.
            $dataC = Obj::run()->vClienteModel->findVcliente();
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
            
            if((!empty($dataC['empresacliente']) || $dataC['empresacliente'] !== '' ) && (!empty($dataC['nombrecompleto']) || $dataC['nombrecompleto'] !== '' )){
                $cliente = $dataC['nombrecompleto'].'<br/>'.$dataC['empresacliente'];                   
            }else{
                 if(!empty($dataC['empresacliente']) || $dataC['empresacliente'] !== '' ){
                      $cliente = $dataC['empresacliente'];
                 }elseif (!empty($dataC['nombrecompleto']) || $dataC['nombrecompleto'] !== '' ){
                     $cliente = $dataC['nombrecompleto'];
                 }                   
            }    
            $dataHtml = Obj::run()->plantillaDocController->getPlantillaDocumento('CARTAPRE01');                              
            $html = str_replace('\\','',$dataHtml['cuerpo'] );        
            $html =   htmlspecialchars_decode($html,ENT_QUOTES);
            $html = str_replace('{{FECHA_CARTA}}',Functions::formato_fecha(date('Y-m-d'),"%d de %B del %Y"), $html);
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
        $enviaremail   = Session::getPermiso('VRECLEE');
        if($enviaremail['permiso']){ 
            $idd = Formulario::getParam('_idPersona');
            $nombres = Formulario::getParam('_nombres');
            $email = Formulario::getParam('_mail');        
            $msj = Obj::run()->mensajesPlantillaController->getPlantillaMensaje('CARTAMSJ', 1);                              
            $body = str_replace('\\','',$msj['cuerpo'] );
            $body = htmlspecialchars_decode($body,ENT_QUOTES);                
            $body = str_replace('{{NOMBRES}}',$nombres, $body);
            $body = str_replace('{{ANIO}}', date('Y'), $body); 

            $exportarpdf = Session::getPermiso('VRECLEP');
            if($exportarpdf["permiso"]):
                $btn .= '<button type="button" class="'.$exportarpdf['theme'].' margin-top-5 padding-5" title="'.VCOTI_8.'" onclick="vCliente.postPDFCarta(this,\''.$idd.'\')">';
                $btn .= '    <i class="'.$exportarpdf['icono'].'"></i> '. VCOTI_8;
                $btn .= '</button>';
            endif;

            Obj::run()->View->adjunto = $btn ;
            Obj::run()->View->mensaje = $body;  
            Obj::run()->View->email = $email;                
            Obj::run()->View->asunto = $msj['asunto'];
            Obj::run()->View->grabar = Session::getPermiso('VRECLEE');
            Obj::run()->View->onclick = 'vCliente.postEmail("'.$idd.'","'.$nombres.'","'.$email.'")';         
            Obj::run()->View->render('formViewMensaje');      
        }
    }    
        
    public function postEmail(){                                         
        $enviaremail   = Session::getPermiso('VRECLEE');
        if($enviaremail['permiso']){ 
            //Generar Archivos:
            $carta = $this->postPDFCarta('N'); 

            $idd = Aes::de(Formulario::getParam('_idPersona'));
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
            $mail->MsgHTML($body);        
            $mail->CharSet = 'UTF-8';

            /* validar si dominio de correo existe */
            if ($mail->Send()) {
                $data = array('result' => 1,'carta'=>$carta);
            } else {
                $data = array('result' => 2);
            }
            echo json_encode($data);      
        }
    }    
    
    /*busca data para editar registro: Vcliente*/
    public static function findVcliente(){
        $editar   = Session::getPermiso('VRECLED');
        if($editar['permiso']){
            $data = Obj::run()->vClienteModel->findVcliente();            
            return $data;
        }
    }
    
    /*envia datos para grabar registro: Vcliente*/
    public function postNewVcliente(){
        $grabar = Session::getPermiso("VRECLGR");
        if($grabar['permiso']){
            $data = Obj::run()->vClienteModel->newVcliente();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: Vcliente*/
    public function postEditVcliente(){
        $editar = Session::getPermiso("VRECLACT");
        if($editar['permiso']){
            $data = Obj::run()->vClienteModel->editVcliente();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: Vcliente*/
    public function postDeleteVcliente(){
        $eliminar = Session::getPermiso("VRECLDE");
        if($eliminar['permiso']){
            $data = Obj::run()->vClienteModel->deleteVcliente();        
            echo json_encode($data);
        }
    }
    
    public function postDesactivar(){
        $editar   = Session::getPermiso('VRECLED');
        if($editar['permiso']){
            $data = Obj::run()->vClienteModel->postDesactivar();        
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar   = Session::getPermiso('VRECLED');
        if($editar['permiso']){
            $data = Obj::run()->vClienteModel->postActivar();        
            echo json_encode($data);
        }
    }      
}

?>
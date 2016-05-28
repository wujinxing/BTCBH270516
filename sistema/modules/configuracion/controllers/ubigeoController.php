<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 17-12-2014 00:12:57 
* Descripcion : ubigeoController.php
* ---------------------------------------
*/    

class ubigeoController extends Controller{

    public function __construct() {
         $this->loadModel(array('modulo' => 'configuracion', 'modelo' => 'ubigeo'));
    }
    
    public function index(){ 
        $main = Session::getMain("UBIG");
        if($main["permiso"]){
            Obj::run()->View->render("indexUbigeo");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                  
    }
    
    public function getGridPais(){
        $editar   = Session::getPermiso('UBIGED');
        $eliminar = Session::getPermiso('UBIGDE');
        $consultar = Session::getPermiso('UBIGCC');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->ubigeoModel->getGridPais();
        
        $num = Obj::run()->ubigeoModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){
                
               /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_pais']);
                $pais = $aRow['descripcion_original'];       
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"pais.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-warning btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"pais.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-warning\">'.LABEL_DESACT.'</span>';
                    }
                }     
                                      
                /* http://www.mapanet.eu/ */
                switch ($aRow['id_continente']){
                    case '01': $continente = 'África'; break;
                    case '02': $continente = 'Sur América'; break;
                    case '03': $continente = 'Europa Oeste'; break;
                    case '04': $continente = 'Asia'; break;
                    case '05': $continente = 'Oceanía'; break;
                    case '06': $continente = 'Antárctica'; break;
                    case '07': $continente = 'Europa Este'; break;
                    case '08': $continente = 'Norte América'; break;
                    case '09': $continente = 'Centro América'; break;
                    case '10': $continente = 'Caribe'; break;
                    case '11': $continente = 'Medio Oriente'; break;                                        
                }                
                /*registros a mostrar*/
                $icono = '<span class=\"'.$aRow['css'].'\" style=\"margin-top:-5px;\"></span>';
                
                  /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';                                
                if($consultar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$consultar['theme'].'\" title=\"'.$consultar['accion'].'\" onclick=\"ubigeo.getGridDepartamento(\''.$encryptReg.'\',\''.$pais.'\')\">';
                    $axion .= '    <i class=\"'.$consultar['icono'].'\"></i>';
                    $axion .= '</button>';
                }                
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"pais.getFormEditPais(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }                
                if($eliminar['permiso']){
                    if ($aRow['uso'] == 0){
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"pais.postDeletePais(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }else{
                         $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }
                }
                
                $axion .= ' </div>" ';
                                
                $sOutput .= '["'.($num++).'","'.$icono.'","'.$aRow['descripcion'].'","'.$continente.'","'.$aRow['alias_isoa2'].'","'.$aRow['alias_isoa3'].'","'.$aRow['moneda'].'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function getGridDepartamento(){
        $editar   = Session::getPermiso('UBIGED');
        $eliminar = Session::getPermiso('UBIGDE');
        $consultar = Session::getPermiso('UBIGCC');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->ubigeoModel->getGridDepartamento();
        
        $num = Obj::run()->ubigeoModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){
                               
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_departamento']);
                $departamento = $aRow['departamento'];
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"departamento.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-warning btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"departamento.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-warning\">'.LABEL_DESACT.'</span>';
                    }
                }     
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                
                
                if($consultar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$consultar['theme'].'\" title=\"'.$consultar['accion'].'\" onclick=\"ubigeo.getGridUbigeo(\''.$encryptReg.'\',\''.$departamento.'\')\">';
                    $axion .= '    <i class=\"'.$consultar['icono'].'\"></i>';
                    $axion .= '</button>';
                }                
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"departamento.getFormEditDepartamento(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    if ($aRow['uso'] == 0){
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"departamento.postDeleteDepartamento(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }
                }
                
                $axion .= ' </div>" ';
                              
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['id_departamento'].'","'.$departamento.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function getGridUbigeo(){
        $editar   = Session::getPermiso('UBIGED');
        $eliminar = Session::getPermiso('UBIGDE');        
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->ubigeoModel->getGridUbigeo();
        
        $num = Obj::run()->ubigeoModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){
                
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_ubigeo']);
                $distrito = $aRow['distrito'];
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"ubigeo.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-warning btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"ubigeo.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-warning\">'.LABEL_DESACT.'</span>';
                    }
                }     
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                                                          
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"ubigeo.getFormEditUbigeo(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    if ($aRow['uso'] == 0){
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"ubigeo.postDeleteUbigeo(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }else{
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    }
                }
                
                $axion .= ' </div>" ';
                              
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['id_ubigeo'].'","'.$distrito.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }   
    
    /*carga formulario (newUbigeo.phtml) para nuevo registro: Ubigeo*/
    public function getFormNewUbigeo(){
        $nuevo   = Session::getPermiso("UBIGNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewUbigeo");
        }
    }
    
    /*carga formulario (editUbigeo.phtml) para editar registro: Ubigeo*/
    public function getFormEditUbigeo(){
        $editar   = Session::getPermiso("UBIGED");
        if($editar["permiso"]){     
           Obj::run()->View->render("formEditUbigeo");
        }       
    }
    
    /*busca data para editar registro: Ubigeo*/
    public static function findUbigeo(){
        $data = Obj::run()->ubigeoModel->findUbigeo();
            
        return $data;
    }
    
    /*envia datos para grabar registro: Ubigeo*/
    public function postNewUbigeo(){
        $grabar = Session::getPermiso("UBIGGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->ubigeoModel->newUbigeo();
            echo json_encode($data);
        }            
    }
    
    /*envia datos para editar registro: Ubigeo*/
    public function postEditUbigeo(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->ubigeoModel->editUbigeo();
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: Ubigeo*/
    public function postDeleteUbigeo(){
        $eliminar = Session::getPermiso("UBIGDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->ubigeoModel->deleteUbigeo();
            echo json_encode($data);
        }                        
    }      
    
    public static function listarDepartamento(){
        $data = Obj::run()->ubigeoModel->listarDepartamento();
        return $data;
    } 
    
    public function postDesactivar(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->ubigeoModel->postDesactivar();
            echo json_encode($data);
        }
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("UBIGACT");
        if($editar["permiso"]){      
            $data = Obj::run()->ubigeoModel->postActivar();
            echo json_encode($data);
        }
    }        
        
    /* Para Combos del Sistema */
    public static function getPais(){ 
        $data = Obj::run()->ubigeoModel->getPais();    
        return $data;
    }
    
    public static function getDepartamentos(){ 
        $data = Obj::run()->ubigeoModel->getDepartamentos(''); 
        echo json_encode($data);
    }
    
    public static function getDepartamentosEst($p){
        $data = Obj::run()->ubigeoModel->getDepartamentos($p);        
        return $data;
    }        
    
    public function getUbigeo(){
        $data = Obj::run()->ubigeoModel->getUbigeo('');        
        echo json_encode($data);
    }
    
    public static function getUbigeoEst($pro=''){
        $data = Obj::run()->ubigeoModel->getUbigeo($pro);        
        return $data;
    }    
    
    /*Combo para reportes y consultas */
    public static function getCiudad(){
        $data = Obj::run()->ubigeoModel->getCiudad();        
        return $data;
    }    
    
    /* Fin Combos del Sistema */
    
}

?>
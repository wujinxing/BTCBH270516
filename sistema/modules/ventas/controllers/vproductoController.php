<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 07-11-2014 00:11:47 
* Descripcion : vproductoController.php
* ---------------------------------------
*/    

class vproductoController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'vproducto'));
    }
    
    public function index(){ 
        Obj::run()->View->render("indexVproducto");
    }
    
    public function getGridVproducto(){
        $editar   = Session::getPermiso('VPRODED');
        $eliminar = Session::getPermiso("VPRODDE");
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vproductoModel->getVproducto();
        
        $num = Obj::run()->vproductoModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_producto']);
                
                /*campo que maneja los estados, para el ejemplo aqui es ACTIVO, coloca tu campo*/
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"vproducto.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"vproducto.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
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
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"vproducto.getFormEditVproducto(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                     if( $aRow['id_producto'] !== '201600000001' && $aRow['uso'] == 0 ):
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"vproducto.postDeleteVproducto(this,\''.$encryptReg.'\')\">';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    else:
                        $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" disabled>';
                        $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                        $axion .= '</button>';
                    endif;
                }
              
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $sOutput .= '["'.$num++.'","'.$aRow['descripcion'].'","'.$aRow['unidadMedida'].'","'.number_format($aRow['precio'],2).'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function getFormBuscarProductos(){ 
        $tab =  Obj::run()->vproductoModel->_tab;        
        $buscar = Session::getPermiso($tab.'BS'); 
        if($buscar['permiso']){   
            Obj::run()->View->buscar = Session::getPermiso($tab.'BS');                    
            Obj::run()->View->ventana = $tab;
            Obj::run()->View->onclickAdd = 'generarVentaScript.addProducto();';  
            Obj::run()->View->render('formBuscarProducto_');
        }
    }              
    
    public function getBuscarProductos(){
        $tab =  Obj::run()->vproductoModel->_tab;
        $funcion =  Obj::run()->vproductoModel->_funcionExterna;
        $buscar = Session::getPermiso($tab.'BS');
                
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->vproductoModel->getBuscarProducto();
        
        $num = Obj::run()->vproductoModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_catalogo']);
                $prod = AesCtr::en($aRow['id_catalogo']).'~'.$aRow['descripcion'].'~'.'1'.'~'.'UND'.'~'.'N';
                
                $chk = '<label class=\"checkbox\">';
                $chk .='<input type=\"checkbox\" id=\"'.++$key.$tab.'chk_prod\" name=\"'.$tab.'chk_prod[]\" value=\"'.$prod.'\"><i></i>';
                $chk .='</label>';
                
                $nombre = addslashes($aRow['descripcion']);
                
                /*datos de manera manual*/
                $sOutput .= '["'.$chk.'","'.$nombre.'" , "'.$aRow['laboratorio'].'" ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }    
    
    
    public static function getUnidadMedida(){ 
        $data = Obj::run()->vproductoModel->getUnidadMedida();
        
        return $data;
    }    
    
    public function getFindProductos(){
        $data = Obj::run()->vproductoModel->getFindProductos();
        
        return $data;
    }
    
    public function findServicioOne(){
        $data = Obj::run()->vproductoModel->findServicioOne();
        
        return $data;
    }
    
    
    public static function getMoneda(){ 
        $data = Obj::run()->vproductoModel->getMoneda();
        
        return $data;
    }   
    
    /*carga formulario (newVproducto.phtml) para nuevo registro: Vproducto*/
    public function getFormNewVproducto(){
        Obj::run()->View->render("formNewVproducto");
    }
    
    /*carga formulario (editVproducto.phtml) para editar registro: Vproducto*/
    public function getFormEditVproducto(){
        Obj::run()->View->render("formEditVproducto");
    }
    
    /*busca data para editar registro: Vproducto*/
    public static function findVproducto(){
        $data = Obj::run()->vproductoModel->findVproducto();
            
        return $data;
    }
    
    /*envia datos para grabar registro: Vproducto*/
    public function postNewVproducto(){
        $data = Obj::run()->vproductoModel->newVproducto();
        
        echo json_encode($data);
    }
    
    /*envia datos para editar registro: Vproducto*/
    public function postEditVproducto(){
        $data = Obj::run()->vproductoModel->editVproducto();
        
        echo json_encode($data);
    }
    
    /*envia datos para eliminar registro: Vproducto*/
    public function postDeleteVproducto(){
        $data = Obj::run()->vproductoModel->deleteVproducto();
        
        echo json_encode($data);
    }
    
    /*envia datos para eliminar registros: Vproducto*/
    public function postDeleteVproductoAll(){
        $data = Obj::run()->vproductoModel->deleteVproductoAll();
        
        echo json_encode($data);
    }
    public function postDesactivar(){
        $data = Obj::run()->vproductoModel->postDesactivar();
        
        echo json_encode($data);
    }
    
    public function postActivar(){
        $data = Obj::run()->vproductoModel->postActivar();
        
        echo json_encode($data);
    }        
    
}

?>
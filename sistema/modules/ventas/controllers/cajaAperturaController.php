<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 06-12-2014 21:12:27 
* Descripcion : cajaAperturaController.php
* ---------------------------------------
*/    

class cajaAperturaController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'ventas','modelo'=>'cajaApertura'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));           
    }
    
    public function index(){ 
        $main = Session::getMain('CAJAA');
        if($main['permiso']){
            Obj::run()->View->render("indexCajaApertura");        
        }else{
            echo 'Ud. no tiene permiso para ingresar a esta pestaña.';
        }          
    }
    
    public function getGridCajaApertura(){
        $editar   = Session::getPermiso('CAJAAED');        
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->cajaAperturaModel->getCajaApertura();
        
        $num = Obj::run()->cajaAperturaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_caja']);
                
                /*campo que maneja los estados, para el ejemplo aqui es ACTIVO, coloca tu campo*/
                if($aRow['estado'] == 'A'){
                    $estado = '<span class=\"label label-success\">'.LABEL_APERTURA.'</span>';
                }else{
                    $estado = '<span class=\"label label-danger\">'.LABEL_CIERRE.'</span>';
                }                                                       
                        
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso'] && $aRow['estado'] == 'A'  ){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"cajaApertura.getFormEditCajaApertura(this,\''.$encryptReg.'\',\''.$aRow['descripcion_moneda'].'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }else{
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" disabled >';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
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

    /*carga formulario (formNewCajaApertura.phtml) para nuevo registro: Caja Apertura*/
    public function getFormNewCajaApertura(){
        $nuevo   = Session::getPermiso('CAJAANEW');
        if($nuevo['permiso']){     
            Obj::run()->View->render("formNewCajaApertura");
        }
    }
    
    /*carga formulario (formEditCajaApertura.phtml) para editar registro: Caja Apertura*/
    public function getFormEditCajaApertura(){
        $editar   = Session::getPermiso('CAJAAED');
        if($editar['permiso']){        
        Obj::run()->View->render("formEditCajaApertura");           
        }
    }    
    
    /*busca data para editar registro: CajaApertura*/
    public static function findCajaApertura(){
        $data = Obj::run()->cajaAperturaModel->findCajaApertura();
            
        return $data;
    }
    
    public function getValidarCaja(){
        $data = Obj::run()->cajaAperturaModel->getValidarCaja();
        return $data;
    }  
        
    public static function getUltimoSaldo($idSucursal){
        $data = Obj::run()->cajaAperturaModel->getUltimoSaldo($idSucursal);
        return $data;
    }
    
    // Cuando se ejecuta desde onchange:
    public static function getUltimoSaldoCombo(){
        $data = Obj::run()->cajaAperturaModel->getUltimoSaldo('');
        echo json_encode($data);
    }
    
    public static function getDataCajaAll($idSucursal){
        $data = Obj::run()->cajaAperturaModel->getDataCajaAll($idSucursal);
            
        return $data;
    }  
    
    // Cuando se ejecuta desde onchange:
    public static function getDataCajaAllCombo(){
        $data = Obj::run()->cajaAperturaModel->getDataCajaAll('');            
        echo json_encode($data);
    }        
    
    public function postNewCajaApertura(){
        $grabar = Session::getPermiso("CAJAAGR");
        if($grabar['permiso']){
            $data = Obj::run()->cajaAperturaModel->postNewApertura();    
            echo json_encode($data);
        }
    }       
            
    /*envia datos para editar registro: CajaApertura*/
    public function postEditCajaApertura(){
        $editar = Session::getPermiso("CAJAAACT");
        if($editar['permiso']){
            $data = Obj::run()->cajaAperturaModel->editCajaApertura();
            echo json_encode($data);
        }
    }
    
}

?>
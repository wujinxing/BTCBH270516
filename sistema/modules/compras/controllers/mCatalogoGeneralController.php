<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 02-06-2016 19:06:04 
* Descripcion : mCatalogoGeneralController.php
* ---------------------------------------
*/    

class mCatalogoGeneralController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'compras','modelo'=>'mCatalogoGeneral'));
        $this->loadController(array('modulo'=>'compras','controller'=>'mClasificacion'));    
        $this->loadController(array('modulo'=>'compras','controller'=>'mFamilia'));    
        $this->loadController(array('modulo'=>'compras','controller'=>'mGenericos'));    
        $this->loadController(array('modulo'=>'compras','controller'=>'mLaboratorios'));    
        $this->loadController(array('modulo'=>'compras','controller'=>'mPresentacion'));    
    }
    
    public function index(){
        $main = Session::getMain("CATGR");
        if($main["permiso"]){
            Obj::run()->View->render("indexMCatalogoGeneral");    
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }                
    }
    
    public function getGridMCatalogoGeneral(){
        $editar   = Session::getPermiso('CATGRED');
        $eliminar = Session::getPermiso('CATGRDE');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->mCatalogoGeneralModel->getMCatalogoGeneral();
        
        $num = Obj::run()->mCatalogoGeneralModel->_iDisplayStart;
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
                
                /*campo que maneja los estados, para el ejemplo aqui es ACTIVO, coloca tu campo*/                              
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_catalogo']);
                if($aRow['estado'] == 'A'){                  
                    $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                }elseif($aRow['estado'] == 'B'){                   
                    $estado = '<span class=\"label label-danger\">'.LABEL_BAJA.'</span>';
                }                   
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"mCatalogoGeneral.getFormEditMCatalogoGeneral(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($eliminar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$eliminar['theme'].'\" title=\"'.$eliminar['accion'].'\" onclick=\"mCatalogoGeneral.postDeleteMCatalogoGeneral(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$eliminar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                $axion .= ' </div>" ';
                
                /*registros a mostrar*/
                $c1 = addslashes($aRow['nombre']).'<br><span  class=\"badge bg-color-orange font10 \">'.$aRow['id_catalogo'].'</span>';    
                $c2 = addslashes($aRow['concentracion']);
                $c3 = $aRow['fraccion'];
                $c4 = addslashes($aRow['laboratorio']);
                $c6 = addslashes($aRow['presentacion']);
                
                if ($aRow['receta_medica'] == 'S'):
                    $c5 = '<span  class=\"badge bg-color-greenLight font10 \">'.LABEL_S.'</span>';    
                else:
                    $c5 = '<span  class=\"badge bg-color-blueLight font10 \">'.LABEL_N.'</span>';
                endif;
                
                $sOutput .= '["'.($num++).'","'.$c1.'","'.$c2.'","'.$c6.'","'.$c4.'","'.$c3.'","'.$c5.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newMCatalogoGeneral.phtml) para nuevo registro: MCatalogoGeneral*/
    public function getFormNewMCatalogoGeneral(){
        $nuevo   = Session::getPermiso("CATGRNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewMCatalogoGeneral");
        }
    }
    
    /*carga formulario (editMCatalogoGeneral.phtml) para editar registro: MCatalogoGeneral*/
    public function getFormEditMCatalogoGeneral(){
        $editar   = Session::getPermiso("CATGRED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditMCatalogoGeneral");
        }
    }
    
    /*busca data para editar registro: MCatalogoGeneral*/
    public static function findMCatalogoGeneral(){
        $data = Obj::run()->mCatalogoGeneralModel->findMCatalogoGeneral();            
        return $data;
    }
    
    /*envia datos para grabar registro: MCatalogoGeneral*/
    public function postNewMCatalogoGeneral(){
        $grabar = Session::getPermiso("CATGRGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->mCatalogoGeneralModel->newMCatalogoGeneral();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para editar registro: MCatalogoGeneral*/
    public function postEditMCatalogoGeneral(){
        $editar = Session::getPermiso("CATGRACT");
        if($editar["permiso"]){      
            $data = Obj::run()->mCatalogoGeneralModel->editMCatalogoGeneral();        
            echo json_encode($data);
        }
    }
    
    /*envia datos para eliminar registro: MCatalogoGeneral*/
    public function postDeleteMCatalogoGeneral(){
        $eliminar = Session::getPermiso("CATGRDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->mCatalogoGeneralModel->deleteMCatalogoGeneral();        
            echo json_encode($data);
        }
    }
        
}

?>
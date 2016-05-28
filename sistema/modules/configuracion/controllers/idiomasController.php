<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 23-12-2014 14:12:17 
* Descripcion : idiomasController.php
* ---------------------------------------
*/    

class idiomasController extends Controller{

    public function __construct() {
        $this->loadModel(array('modulo'=>'configuracion','modelo'=>'idiomas'));
    }
    
    public function index(){ 
        $main = Session::getMain("IDIOM");
        if($main["permiso"]){
            Obj::run()->View->render("indexIdiomas");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }        
    }
    
    public function getGridIdiomas(){
        $editar   = Session::getPermiso('IDIOMED');  
        $file   = Session::getPermiso('IDIOMAJ');  
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->idiomasModel->getIdiomas();
        
        $num = Obj::run()->idiomasModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_idioma']);
                $sigla = Aes::en($aRow['sigla']);
                
                if($aRow['estado'] == 'A'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"'.BTN_DESACT.'\" onclick=\"idiomas.postDesactivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-check\"></i> '.LABEL_ACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-success\">'.LABEL_ACT.'</span>';
                    }
                }elseif($aRow['estado'] == 'I'){
                    if($editar['permiso']){
                        $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"'.BTN_ACT.'\" onclick=\"idiomas.postActivar(this,\''.$encryptReg.'\')\"><i class=\"fa fa-ban\"></i> '.LABEL_DESACT.'</button>';
                    }else{
                        $estado = '<span class=\"label label-danger\">'.LABEL_DESACT.'</span>';
                    }
                }    
                
                $chk = '<input id=\"c_'.(++$key).'\" type=\"checkbox\" name=\"'.IDIOM.'chk_delete[]\" value=\"'.$encryptReg.'\">';
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($editar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"idiomas.getFormEditIdiomas(this,\''.$encryptReg.'\')\">';
                    $axion .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                if($file['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$file['theme'].'\" title=\"'.$file['accion'].'\" onclick=\"idiomas.getFormFileIdiomas(this,\''.$encryptReg.'\',\''.$sigla.'\')\">';
                    $axion .= '    <i class=\"'.$file['icono'].'\"></i>';
                    $axion .= '</button>';
                }
                
                
                $axion .= ' </div>" ';
                
                $icono = '<span class=\"'.$aRow['icono'].'\"></span>';
                
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$aRow['nombre'].'","'.$aRow['sigla'].'","'.$icono.'","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    /*carga formulario (newIdiomas.phtml) para nuevo registro: Idiomas*/
    public function getFormNewIdiomas(){
        $nuevo   = Session::getPermiso("IDIOMNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewIdiomas");
        }
        
    }
    
    /*carga formulario (editIdiomas.phtml) para editar registro: Idiomas*/
    public function getFormEditIdiomas(){
        $editar   = Session::getPermiso("IDIOMED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditIdiomas");
        }
        
    }
    
    /*carga formulario (editIdiomas.phtml) para editar registro: Idiomas*/
    public function getFormFileIdiomas(){
        $file   = Session::getPermiso('IDIOMAJ'); 
        if($file["permiso"]){     
            Obj::run()->View->render("formFileIdiomas");
        }
        
    }    
    
    /*busca data para editar registro: Idiomas*/
    public static function findIdiomas(){
        $data = Obj::run()->idiomasModel->findIdiomas();
            
        return $data;
    }
    
    /*envia datos para grabar registro: Idiomas*/
    public function postNewIdiomas(){
        $grabar = Session::getPermiso("IDIOMGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->idiomasModel->newIdiomas();
            echo json_encode($data);
        }                        
    }
    
    /*envia datos para editar registro: Idiomas*/
    public function postEditIdiomas(){
        $editar = Session::getPermiso("IDIOMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->idiomasModel->editIdiomas();
            echo json_encode($data);
        }        
    }
    
    /*envia datos para editar Archivo: Idiomas*/
    public function postFileIdiomas(){
        $file   = Session::getPermiso('IDIOMAJ'); 
        if($file["permiso"]){  
            /* Back-End PHP */
            $rutaArchivo1 = ROOT . 'lang' . DS .'lang_' .Obj::run()->idiomasModel->_sigla . '.php';                        
            $contenido1 = htmlspecialchars_decode(Formulario::getParam(IDIOM.'txt_archivophp'),ENT_QUOTES);         
            // Grabar        
            $archivo1 = fopen($rutaArchivo1, "w");
            $fputs1 = fputs($archivo1, $contenido1);        

            /* Back-End JS */
            $rutaArchivo2 = ROOT . 'lang' . DS .'js' .DS . 'lang_' .Obj::run()->idiomasModel->_sigla . '.js';                
            $contenido2 = htmlspecialchars_decode(Formulario::getParam(IDIOM.'txt_archivojs'),ENT_QUOTES);                              
            // Grabar
            $archivo2 = fopen($rutaArchivo2, "w");
            $fputs2 = fputs($archivo2, $contenido2);        

            /* Front-End PHP */
            $rutaArchivo3 = '..'. DS . 'lang' . DS .'lang_' .Obj::run()->idiomasModel->_sigla . '.php';                        
            $contenido3 = htmlspecialchars_decode(Formulario::getParam(IDIOM.'txt_archivophp2'),ENT_QUOTES);         
            // Grabar
            $archivo3 = fopen($rutaArchivo3, "w");
            $fputs3 = fputs($archivo3, $contenido3);        

            /* Front-End JS */
            $rutaArchivo4 = '..'. DS . 'lang' . DS .'js' .DS . 'lang_' .Obj::run()->idiomasModel->_sigla . '.js';                
            $contenido4 = htmlspecialchars_decode(Formulario::getParam(IDIOM.'txt_archivojs2'),ENT_QUOTES);                              
            // Grabar
            $archivo4 = fopen($rutaArchivo4, "w");
            $fputs4 = fputs($archivo4, $contenido4);                

            echo json_encode(array('result' => 1));
        }
    }
    
    /*envia datos para eliminar registro: Idiomas*/
    public function postDeleteIdiomas(){
        $eliminar = Session::getPermiso("IDIOMDE");
        if($eliminar["permiso"]){          
            $data = Obj::run()->idiomasModel->deleteIdiomas();
            echo json_encode($data);
        }        
    }       
    
    public function postDesactivar(){
        $editar = Session::getPermiso("IDIOMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->idiomasModel->postDesactivar();
            echo json_encode($data);
        }        
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("IDIOMACT");
        if($editar["permiso"]){      
            $data = Obj::run()->idiomasModel->postActivar();
            echo json_encode($data);
        }                
    }     
    
    public static function listarIdiomas(){
        $data = Obj::run()->idiomasModel->listarIdiomas();
        return $data;
    }     
    
}
?>
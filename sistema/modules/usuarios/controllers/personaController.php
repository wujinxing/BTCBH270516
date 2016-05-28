<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 16-09-2014 03:09:14 
* Descripcion : personaController.php
* ---------------------------------------
*/    

class personaController extends Controller{

    public function __construct() {      
        $this->loadModel(array('modulo' => 'usuarios', 'modelo' => 'persona'));        
        $this->loadController(array('modulo' => 'index', 'controller' => 'login'));
        $this->loadController(array('modulo' => 'usuarios', 'controller' => 'configurarUsuarios'));        
        $this->loadController(array('modulo'=>'configuracion','controller'=>'idiomas')); 
        $this->loadController(array('modulo'=>'configuracion','controller'=>'ubigeo'));
        $this->loadController(array('modulo'=>'ventas','controller'=>'vSucursal'));
    }
    
    public function index(){ 
        $main = Session::getMain("REPER");
        if($main["permiso"]){
            Obj::run()->View->render("indexPersona");
        }else{
            echo "Ud. no tiene permiso para ingresar a esta pestaña.";
        }        
    }
    
    public function getGridPersona(){
        $editar = Session::getPermiso('REPERED');     
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->personaModel->getGridPersona();
        $num = Obj::run()->personaModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_persona']);
                 
                if($aRow['estado'] == 'A'){
                    $estado = '<button type=\"button\" class=\"btn btn-success btn-xs\" title=\"Desactivar\" onclick=\"persona.postDesactivarPersona(this,\''.$encryptReg.'\')\">Activo</button>';
                }elseif($aRow['estado'] == 'I'){
                    $estado = '<button type=\"button\" class=\"btn btn-danger btn-xs\" title=\"Activar\" onclick=\"persona.postActivarPersona(this,\''.$encryptReg.'\')\">Inactivo</button>';
                }
                                                                
                $ruta = 'public/img/fotos/'.$aRow['foto'];
               
                if( file_exists($ruta) && $aRow['foto'] != ''){
                    $foto = '<img border=\"0\" src=\"'.$ruta.'\" width=\"30px\" height=\"30px\" onclick=\"index.getFormViewFoto(\''.AesCtr::en($ruta).'\');\" style=\"cursor:pointer\">';
                }else{
                     $foto = '<img border=\"0\" src=\"'.BASE_URL.'public/img/sin_foto.jpg\" width=\"70px\" height=\"40px\">';
                }
                
                 if((!empty($aRow['empresacliente']) || $aRow['empresacliente'] !== '' ) && (!empty($aRow['nombrecompleto']) || $aRow['nombrecompleto'] !== '' )){
                    $cliente = $aRow['empresacliente'].' | '.$aRow['nombrecompleto'];                   
                }else{
                     if(!empty($aRow['empresacliente']) || $aRow['empresacliente'] !== '' ){
                          $cliente = $aRow['empresacliente'];
                     }elseif (!empty($aRow['nombrecompleto']) || $aRow['nombrecompleto'] !== '' ){
                         $cliente = $aRow['nombrecompleto'];
                     }                   
                }            
                
                /*datos de manera manual*/
                $clienteTemplate = '<a href=\"javascript:;\" onclick=\"persona.getDatosPersonales(\''.$encryptReg.'\',\''.LABEL_A163.'\');\">'.$cliente.'</a>';
                $sOutput .= '["'.($num++).'","'.$foto.'","'.$clienteTemplate.'","'.$aRow['email'].'","'.$aRow['telefono'].'","'.$aRow['ciudad'].'","'.$estado.'", ';
                

                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $sOutput .= '"<div class=\"btn-group\">';
                
                if($editar['permiso']){
                    $sOutput .= '<button type=\"button\" class=\"'.$editar['theme'].'\" title=\"'.$editar['accion'].'\" onclick=\"persona.getFormEditPersona(this,\''.$encryptReg.'\')\">';
                    $sOutput .= '    <i class=\"'.$editar['icono'].'\"></i>';
                    $sOutput .= '</button>';
                }       

                $sOutput .= ' </div>" ';

                $sOutput = substr_replace( $sOutput, "", -1 );
                $sOutput .= '],';
            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;
    }
    
    /*carga formulario (newPersona.phtml) para nuevo registro: Persona*/
    public function getFormNewPersona(){
        $nuevo   = Session::getPermiso("REPERNEW");
        if($nuevo["permiso"]){    
            Obj::run()->View->render("formNewPersona");
        }
    }
    
   /*carga formulario (editPersona.phtml) para editar registro: Persona*/
    public function getFormEditPersona(){
        $editar   = Session::getPermiso("REPERED");
        if($editar["permiso"]){     
            Obj::run()->View->render("formEditPersona");
        }
    }
    
    public function getFormDatosPersonales(){
        Obj::run()->View->render("formDatosPersonales");
    }
    
    /*envia datos para grabar registro: Persona*/
    public function postNewPersona(){
        $grabar = Session::getPermiso("REPERGR");
        if($grabar["permiso"]){    
            $data = Obj::run()->personaModel->mantenimientoPersona(); 
            echo json_encode($data);
        }                
    }
        
    /*envia datos para editar registro: Persona*/
    public function postEditPersona(){
        $editar = Session::getPermiso("REPERACT");
        if($editar["permiso"]){      
            $data = Obj::run()->personaModel->mantenimientoPersona();    
            echo json_encode($data);
        }               
    }
    
    /*envia datos para eliminar registro: Persona*/
    public function postDeletePersona(){
        $eliminar = Session::getPermiso("REPERDE");
        if($eliminar["permiso"]){      
            $data = Obj::run()->personaModel->deletePersona(); 
            echo json_encode($data);
        }        
    }
    
    public static function findPersona(){
        $data = Obj::run()->personaModel->findPersona();
        
        return $data;
    }  
    
    public function getDatosPersonales(){
        $data = Obj::run()->personaModel->findPersona();        
        return $data;
    }            
       
    public function postDesactivar(){
        $editar = Session::getPermiso("REPERACT");
        if($editar["permiso"]){      
            $data = Obj::run()->personaModel->postDesactivar();
            echo json_encode($data);
        }              
    }
    
    public function postActivar(){
        $editar = Session::getPermiso("REPERACT");
        if($editar["permiso"]){      
            $data = Obj::run()->personaModel->postActivar();
            echo json_encode($data);
        }                                      
    }  
    
    /* Actualizar Datos de Facebook en cuenta de Usuario */
    public function postFacebookPersonaUpdate($idFB, $email, $link, $fn , $img){
        $data = Obj::run()->personaModel->postFacebookPersonaUpdate($idFB, $email, $link, $fn);           
        if ($data['result'] == 1){                       
            if ($data['foto'] == 'X'):
                $targetPath = ROOT . 'public' . DS .'img' .DS . 'fotos' . DS;            
                if(!file_exists($targetPath)):
                    mkdir($targetPath);                
                endif;                                             
                $usuario =  Obj::run()->personaModel->_usuario;
                $file = $usuario . '_' . Functions::nombreAleatorio() . '.jpg';
                $targetFile = $targetPath. DS. $file;                 
                //Crear Archivo:                            
                $imagen = file_get_contents($img);
                file_put_contents($targetFile, $imagen);
                $dataImg = Obj::run()->personaModel->postFacebookFotografia($file);    
            endif;                                  
        }                        
        return $data;                
    }            
    
}

?>
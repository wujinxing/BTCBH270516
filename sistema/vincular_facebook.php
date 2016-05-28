<?php
session_start();

require 'libs/facebook/vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

define('DS',DIRECTORY_SEPARATOR);
define('ROOT',  realpath(dirname(__FILE__)) . DS);   

require_once (ROOT . 'app' . DS . 'Config.php');
$url_success = \BASE_URL;

$configVinculo = array(        
	'app_id' => FB_LOGIN_APP_ID,
	'app_secret' => FB_LOGIN_APP_SECRET,
	'scopes' => array('scope' => 'email, public_profile')
);

FacebookSession::setDefaultApplication($configVinculo['app_id'], $configVinculo['app_secret']);
$vincularFB = new FacebookRedirectLoginHelper($url_success);
try {
	$session = $vincularFB->getSessionFromRedirect();
      
	if ($session):
		$_SESSION['facebook_sincronizar'] = $session->getToken();
		header('Location: '.$url_success);
	endif;
	if (isset($_SESSION['facebook_sincronizar'])):
            Session::init();
            $session = new FacebookSession($_SESSION['facebook_sincronizar']);
            /* Datos personales */
            $request = new FacebookRequest($session, 'GET', '/me?fields=id,first_name,last_name,email,gender,birthday,link');
            $response = $request->execute();
            $facebook_user = $response->getGraphObject(GraphUser::className());               
            // ID de Facebook
            $idd = $facebook_user->getId();                                                                                                                                   
            /* Imagen type=square& */
            $picture_array = new FacebookRequest( $session, 'GET', '/me/picture?type=large&redirect=false' );
            $resp = $picture_array->execute();                         	            
            $array_facebook_img = $resp->getGraphObject()->asArray();              
            // Extraer Datos:            
            $facebook_img = $array_facebook_img['url'];
            $facebook_email = $facebook_user->getProperty('email');
            $facebook_link = $facebook_user->getProperty('link');            
            //Fecha Nacimiento:
            $fechanac = new DateTime($facebook_user->getProperty('birthday'));                               
            $facebook_fechaNac = $fechanac->format('Y-m-d');              
            
            /*registro de clases*/                
            Registry::anonimous('Request');
            Registry::anonimous('Database');
            Registry::anonimous('Autoload');
            Registry::anonimous('View');
            Obj::run()->Request->_modulo = 'usuarios';
            Obj::run()->Request->_controlador = 'persona';
            Obj::run()->Request->_metodo = 'postFacebookPersonaUpdate';
            Obj::run()->Request->_argumentos = array($idd,$facebook_email,$facebook_link,$facebook_fechaNac,$facebook_img);                
            //Validar si Usuario existe en BD 
            $retorno = Bootstrap::run(Obj::run()->Request);                              
                                                                                                                                                              
           Session::destroy('facebook_sincronizar');                      
           if(isset($retorno['result'])&& $retorno['result'] == 2 ){    // Ya existe PIC en otra Cuenta                    
                Session::set('sys_pic','X');  
                header('location:' . BASE_URL.'index/index/msjFacebook/1');
            } if(isset($retorno['result'])&& $retorno['result'] == 3 ){  // El usuario ya está sincronizado
                header('location:' . BASE_URL.'index/index/msjFacebook/2'); 
            }else{                                 
                Session::set('sys_pic', $idd);
                header('location:' . $url_success.'?sincr=ok');              
            }                         
           
	endif;
} catch(FacebookRequestException $ex) {
 
} catch(\Exception $ex) {
 
}
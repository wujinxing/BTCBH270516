<?php
session_start();

require 'libs/facebook/vendor/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

if(isset($_GET['token'])){
    define('DS',DIRECTORY_SEPARATOR);
    define('ROOT',  realpath(dirname(__FILE__)) . DS);   
}

require_once (ROOT . 'app' . DS . 'Config.php');
$url_success = BASE_URL;

$config = array(        
	'app_id' => FB_LOGIN_APP_ID,
	'app_secret' => FB_LOGIN_APP_SECRET,
	'scopes' => array('scope' => 'email, public_profile')
);

FacebookSession::setDefaultApplication($config['app_id'], $config['app_secret']);
$helper = new FacebookRedirectLoginHelper($url_success);

try {
	$session = $helper->getSessionFromRedirect();
      
	if ($session):
		$_SESSION['facebook_acceso'] = $session->getToken();
		header('Location: '.$url_success);
	endif;
	if (isset($_SESSION['facebook_acceso'])):
            Session::init();
            $session = new FacebookSession($_SESSION['facebook_acceso']);
            /* Datos personales */
            $request = new FacebookRequest($session, 'GET', '/me');
            $response = $request->execute();
            $facebook_user = $response->getGraphObject(GraphUser::className());   
            $idd = $facebook_user->getId();
            try{
                /*registro de clases*/                
                Registry::anonimous('Request');
                Registry::anonimous('Database');   
                Registry::anonimous('Autoload');      
                Registry::anonimous('View');
                Obj::run()->Request->_modulo = 'index';
                Obj::run()->Request->_controlador = 'login';                                
                Obj::run()->Request->_metodo = 'getFacebook';
                Obj::run()->Request->_argumentos = array($idd);                
                $retorno = Bootstrap::run(Obj::run()->Request);       
            }  
            catch (Exception $e){
                echo $e->getMessage();
            }                                                
                                                          
            /*Validar si Usuario existe en BD */       
           // $retorno = Obj::run()->loginController->getFacebook($idd);                            
            if(isset($retorno['result'])&& $retorno['result'] == 2 ){
                //Si su cuenta esta Bloquedada:
               Session::destroy('facebook_acceso');
               header('location:' . BASE_URL.'index/index/msjFacebook/4');                
            }else{            
                if (isset($retorno['id_usuario'])):       
                    if(isset($_GET['token'])){
                        header('Location: '.  \BASE_URL);     
                    }else{
                        Obj::run()->View->render('index',false, true);                                                      
                    }
                else:
                   //No existe registrado el ID de Facebook en la BD
                   Session::destroy('facebook_acceso');
                   header('location:' . BASE_URL.'index/index/msjFacebook/3');                    
                endif;
            }
            exit;
            
	endif;
} catch(FacebookRequestException $ex) {
 
} catch(\Exception $ex) {
 
}
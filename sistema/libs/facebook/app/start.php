<?php

session_start();

require 'clases/facebook/config/facebook.php';
require 'clases/facebook/vendor/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

FacebookSession::setDefaultApplication($config['app_id'], $config['app_secret']);
$helper = new FacebookRedirectLoginHelper('http://localhost:8080/pn/registro-gratis.html');

try {
	$session = $helper->getSessionFromRedirect();

	if ($session):
		$_SESSION['facebook'] = $session->getToken();
		header('Location: index.php');
	endif;

	if (isset($_SESSION['facebook'])):
		$session = new FacebookSession($_SESSION['facebook']);

                /* Datos personales */
		$request = new FacebookRequest($session, 'GET', '/me');
		$response = $request->execute();
		$facebook_user = $response->getGraphObject(GraphUser::className());
                
                $id = $facebook_user->getId();
                $name = $facebook_user->getName();
                $image = 'https://graph.facebook.com/'.$id.'/picture?width=64&height=64';            
                
                /* Imagen type=square& */
                $picture_array = new FacebookRequest( $session, 'GET', '/me/picture?type=large&redirect=false' );
                $resp = $picture_array->execute();
                $facebook_img = $resp->getGraphObject()->asArray();             
                
                /* Datos */
                $request = new FacebookRequest($session, 'GET', '/me?fields=id,first_name,last_name,email,gender,birthday,link');
		$response = $request->execute();		
                $graph = $response->getGraphObject(GraphUser::classname());
                // Extraer Datos:
                $firstname = $graph->getProperty('first_name');
                $lastname = $graph->getProperty('last_name');             
                $email = $graph->getProperty('email');
                $genero = $graph->getProperty('gender');
                $fechanac = $graph->getProperty('birthday');
                $link = $graph->getProperty('link');
	endif;
} catch(FacebookRequestException $ex) {
  // When Facebook returns an error
} catch(\Exception $ex) {
  // When validation fails or other local issues
}
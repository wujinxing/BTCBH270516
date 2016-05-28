<?php
/*
 * --------------------------------------
 * Config.php
 * --------------------------------------
 */
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/botica/sistema/');#accede a las vistas del usuario
define('INDEX_URL','http://'.$_SERVER['HTTP_HOST'].'/botica/');

//define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/admin/');#accede a las vistas del usuario
//define('INDEX_URL','http://'.$_SERVER['HTTP_HOST'].'/');
$version = '1.001';

define('DEFAULT_CONTROLLER','index');
define('DEFAULT_LAYOUT','stardadmin');
define('APP_KEY','adABKCDLZEFXGHIJ');
define('APP_PASS_KEY','99}dF7EZbnbXOkojf&dzvxd5q#guPbPK1spU75Jm|N79Ii7PK');
define('APP_EXPORT_FILES',ROOT . 'public' . DS . 'files' . DS);
define('APP_VERSION',$version);

define('DB_ENTORNO','D');  /*D=DESARROLLO, P=PRODUCCION*/
define('DB_MOTOR','mysql');

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','sistema_botica');

define('DB_PORT','3306');
define('DB_CHARSET','utf8');

define('REG_X_PAGINA',10);
define('ITEM_PAGINADOR',5);
define('IMG_CALIDAD',75);

define('FB_LOGIN_APP_ID','1037023236336140');
define('FB_LOGIN_APP_SECRET','5bb7fcfb9ab8ab28a2079a16c7c330b6');
define('KEY_APP_GOOGLE','AIzaSyBrTZ8B2VNylHHIBWAKGcuQeZWvIdVt92s');

define('APP_COD_SADM','0001');
define('APP_COD_ADMIN','0002');
define('APP_COD_VENDEDOR','0003');
define('APP_COD_CAJERO','0004');

define('VERSION_CSS', $version );
define('VERSION_JS', $version );
define('VERSION_COOKIE', $version );

/*__autoload es obsoleta*/
function autoloadCore($class){
    if(file_exists(ROOT . 'app' . DS . $class.'.php')){
        require_once (ROOT . 'app' . DS . $class.'.php');
    }
}

function autoloadLibs($class){
    if(file_exists(ROOT . 'libs' . DS . $class . DS . $class.'.php')){
        require_once (ROOT . 'libs' . DS . $class . DS . $class.'.php');
    }
}

/*se registra la funcion autoload*/
spl_autoload_register('autoloadCore'); 
spl_autoload_register('autoloadLibs');

?>
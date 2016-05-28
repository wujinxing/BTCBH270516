<?php
/*
 * Documento   : Model
 * Creado      : 03-ene-2014, 17:05:26 Modificado: 11-11-2015
 * Autor       : RDCC
 * Descripcion : ** Act: Ahora graba los errores
 */
class Model{
    
    protected $_db, $_dbError;
    
    public function __construct() {
        $this->_db = Obj::run()->Database;
        $this->_dbError = Obj::run()->Database;
    }
    
    public function execute($query,$arrayValues){
        $statement = $this->_db->prepare($query);
        $statement->execute($arrayValues);

        $bug = $statement->errorInfo();

        if($bug[0] == '00000'){// ok
            $result = true;
        }else{//error
            if(DB_ENTORNO == 'D'){
                $result = array('error'=>'ERROR:: '.$bug[2]);
            }elseif(DB_ENTORNO == 'P'){
                $result = array('error'=>'ERROR:: '.$this->messageError($bug[1]));
            }
            $this->insertError($bug, $query, $arrayValues);
        }
         
        return $result;
    }
    public function queryOne($query,$arrayValues){
        $statement = $this->_db->prepare($query);
        $statement->execute($arrayValues);

        $bug = $statement->errorInfo();

        if($bug[0] == '00000'){// ok
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }else{//error
            if(DB_ENTORNO == 'D'){
                $result = array('error'=>'ERROR:: '.$bug[2]);
            }elseif(DB_ENTORNO == 'P'){
                $result = array('error'=>'ERROR:: '.$this->messageError($bug[1]));
            }
            $this->insertError($bug, $query, $arrayValues);
        }
         
        return $result;
    }
    
    public function queryAll($query,$arrayValues){
        $statement = $this->_db->prepare($query);
        $statement->execute($arrayValues);

        $bug = $statement->errorInfo();

        if($bug[0] == '00000'){// ok
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        }else{//error
            if(DB_ENTORNO == 'D'){
                $result = array('error'=>'ERROR:: '.$bug[2]);
            }elseif(DB_ENTORNO == 'P'){
                $result = array('error'=>'ERROR:: '.$this->messageError($bug[1]));
            }
            $this->insertError($bug, $query, $arrayValues);
        }
         
        return $result;
    }
    
    private function messageError($code) {
        $msg = '';
        switch ($code) {
            case 1305:
                $msg = 'Procedimiento almacenado no existe.';
                break;
            case 1318:
                $msg = 'Numero de argumentos en el procedimiento incorrectos.';
                break;
            case 1061:
                $msg = 'Nombre de clave duplicado.';
                break;
            case 547:
                $msg = 'No se puede eliminar el registro porque se necesitan en otras tablas.';
                break;
            case 1451:
                $msg = 'No se pudo eliminar el registro debido a que está siendo utilizada en otras operaciones.';
                break;
            case 1452:
                $msg = 'Algunas claves primarias no existen en las tablas maestras. No se pudo realizar la relaci&oacute;n.';
                break;
            case 1062:
                $msg = 'Registro duplicado. Esta intentando registrar un registro que ya existe.';
                break;
            case 1146:
                $msg = 'La tabla no existe.';
                break;
            case 1054:
                $msg = 'La columna es desconocida.';
                break;
            case 1064:
                $msg = 'Sintaxis incorrecta.';
                break;
            case 1136:
                $msg = 'Numero de columnas no corresponde al numero de campos.';
                break;
            case 1362:
                $msg = 'Error de clave unica.';
                break;
            case 1022:
                $msg = 'Ya existe un registro con este nombre.';
                break;
            default:
                $msg = 'Codigo de error: ' . $code . ': Por favor comun&iacute;que de este problema a la Oficina de Sistemas.';
        }
        return $msg;
    }
    
    /*retorna parametros*/
    protected function post($parametro){
        if(isset($_POST[$parametro]) && !empty($_POST[$parametro])){
            if(is_array($_POST[$parametro])){
                return $_POST[$parametro];
            }else{
                return trim($_POST[$parametro]);
            }
        }else{
            return false;
        }
    }   
    
   protected function getLibrary($libreria){
        $rutaLibreria = ROOT . 'libs' . DS . $libreria . '.php';
        
        if(is_readable($rutaLibreria)){
            require_once ($rutaLibreria);
        }else{
            throw new Exception('Error de Libreria: <b>'.$rutaLibreria.'</b> no encontrada.');
        }
    } 
    
   public function insertError($error, $q, $a){
        $query = 'insert into pub_error_mysql (error, descripcion, query, parametros, ip, `fecha`) '
               . 'values(:error, :deserror, :query, :parametros, :ip, :fecha);';
        $arrayValues = array(
           ':error' => trim($error[1]).' - '.trim($this->messageError($error[1])),
           ':deserror' => trim($error[2]),
           ':query' => $q,
           ':parametros'=>implode(",", $a),
           ':ip'=>$_SERVER["REMOTE_ADDR"],
           ':fecha' => date('Y-m-d h:i:s')
       );
       $st = $this->_dbError->prepare($query);
       $st->execute($arrayValues);
   }
    
}
?>
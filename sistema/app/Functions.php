<?php
/*
 * Documento   : Functions
 * Creado      : 07-jul-2014
 * Autor       : ..... .....
 * Descripcion : 
 */
class Functions{
    
    public static function widgetOpen($obj){
        if(is_array($obj)){
            $id     = (isset($obj['id']))?$obj['id']:'';
            $title  = (isset($obj['title']))?$obj['title']:'';
            $width  = (isset($obj['width']))?' width:'.$obj['width'].'; ':'';
            $height = (isset($obj['height']))?' height:'.$obj['height'].';':'';
            $padding= (isset($obj['padding']))? '':'no-padding';
            $actions= (isset($obj['actions']))? $obj['actions']:'';
            $overflow= (isset($obj['overflow']))? 'overflow:'.$obj['overflow'].';':'overflow:auto;';            
        }else{
            $id     = $obj;
            $title  = $obj;
            $width  = '';
            $height = '';
            $padding= 'no-padding';
            $actions= '';
            $overflow = '';
        }
        $bottom = '';
        $border = '';
        if(empty($padding)){
            $bottom = 'margin-bottom:15px;';
            $border = 'border: #dddddd solid 1px;';
        }
        $toolButton = '';
        if(!empty($actions) && is_array($actions)){
            $toolButton = '
            <div class="widget-toolbar" role="menu">
                <div class="btn-group">
                    <button class="btn dropdown-toggle btn-xs btn-warning" data-toggle="dropdown">
                            '.ACTIONS.' <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">';
            foreach ($actions as $btn) {
                $t='';
                $idd = '';
                if(isset($btn['sitemap'])) $sm= 'data-sitemap="'.$btn['sitemap'].'"';    
                
                if(isset($btn['permiso'])){
                    $permiso = Session::getPermiso($btn['permiso']);  
                }
                
                if($permiso['permiso']){       
                    if(isset($btn['title'])) $t= $btn['title'];
                    if(isset($btn['label'])){
                        $label = $btn['label']; 
                    }else{
                        $label = $permiso['accion'];
                    }         
                    
                    if(isset($btn['id'])) $idd = 'id = "'.$btn['id'].'" ';
                    $toolButton .= '
                    <li>
                        <a '.$idd.' href="javascript:void(0);" '
                            . 'onclick="'.$btn['click'].'" '
                            . 'title="'.$t.'" '.$sm.' ><i class="'.$permiso['icono'].'"></i> '.$label.'</a>
                    </li>';
                }else{
                    if(isset($btn['title'])) $t= $btn['title'];
                    if(isset($btn['id'])) $idd = 'id = "'.$btn['id'].'" ';
                    $toolButton .= '
                    <li>
                        <a '.$idd.' href="javascript:void(0);" '
                            . 'onclick="'.$btn['click'].'" '
                            . 'title="'.$t.'" '.$sm.' >'.$btn['label'].'</a>
                    </li>';
                }
            }
            $toolButton .='  
                    </ul>
                </div>
            </div>';
        }
        
        $html = '
        <div id="widget_'.$id.'" class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" data-widget-editbutton="false" style="'.$width.'" role="widget">
            <header role="heading">
                <div class="jarviswidget-ctrls" role="menu">
                     <!-- <a style="display: block;" data-original-title="Collapse" href="#" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-minus"></i></a> 
                    <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-resize-full"></i></a>
                    <div style="top: 33px; left: 1311px; display: block;" class="tooltip fade bottom in">
                        <div class="tooltip-arrow"></div>
                        <div class="tooltip-inner">Fullscreen</div>
                    </div> -->
                    <!--<a style="display: block;" data-original-title="Eliminar" href="javascript:void(0);" class="button-icon jarviswidget-delete-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-times"></i></a>-->
                </div>
               <!-- <div class="widget-toolbar" role="menu">
                    <a data-toggle="dropdown" class="dropdown-toggle color-box selector" href="javascript:void(0);"></a>
                        <ul class="dropdown-menu arrow-box-up-right color-select pull-right">
                            <li><span class="bg-color-green" data-widget-setstyle="jarviswidget-color-green" rel="tooltip" data-placement="left" data-original-title="Green Grass"></span></li>
                            <li><span class="bg-color-greenDark" data-widget-setstyle="jarviswidget-color-greenDark" rel="tooltip" data-placement="top" data-original-title="Dark Green"></span></li>
                            <li><span class="bg-color-greenLight" data-widget-setstyle="jarviswidget-color-greenLight" rel="tooltip" data-placement="top" data-original-title="Light Green"></span></li>
                            <li><span class="bg-color-purple" data-widget-setstyle="jarviswidget-color-purple" rel="tooltip" data-placement="top" data-original-title="Purple"></span></li><li><span class="bg-color-magenta" data-widget-setstyle="jarviswidget-color-magenta" rel="tooltip" data-placement="top" data-original-title="Magenta"></span></li>
                            <li><span class="bg-color-pink" data-widget-setstyle="jarviswidget-color-pink" rel="tooltip" data-placement="right" data-original-title="Pink"></span></li>
                            <li><span class="bg-color-pinkDark" data-widget-setstyle="jarviswidget-color-pinkDark" rel="tooltip" data-placement="left" data-original-title="Fade Pink"></span></li><li><span class="bg-color-blueLight" data-widget-setstyle="jarviswidget-color-blueLight" rel="tooltip" data-placement="top" data-original-title="Light Blue"></span></li><li><span class="bg-color-teal" data-widget-setstyle="jarviswidget-color-teal" rel="tooltip" data-placement="top" data-original-title="Teal"></span></li><li><span class="bg-color-blue" data-widget-setstyle="jarviswidget-color-blue" rel="tooltip" data-placement="top" data-original-title="Ocean Blue"></span></li><li><span class="bg-color-blueDark" data-widget-setstyle="jarviswidget-color-blueDark" rel="tooltip" data-placement="top" data-original-title="Night Sky"></span></li><li><span class="bg-color-darken" data-widget-setstyle="jarviswidget-color-darken" rel="tooltip" data-placement="right" data-original-title="Night"></span></li><li><span class="bg-color-yellow" data-widget-setstyle="jarviswidget-color-yellow" rel="tooltip" data-placement="left" data-original-title="Day Light"></span></li><li><span class="bg-color-orange" data-widget-setstyle="jarviswidget-color-orange" rel="tooltip" data-placement="bottom" data-original-title="Orange"></span></li><li><span class="bg-color-orangeDark" data-widget-setstyle="jarviswidget-color-orangeDark" rel="tooltip" data-placement="bottom" data-original-title="Dark Orange"></span></li><li><span class="bg-color-red" data-widget-setstyle="jarviswidget-color-red" rel="tooltip" data-placement="bottom" data-original-title="Red Rose"></span></li><li><span class="bg-color-redLight" data-widget-setstyle="jarviswidget-color-redLight" rel="tooltip" data-placement="bottom" data-original-title="Light Red"></span></li><li><span class="bg-color-white" data-widget-setstyle="jarviswidget-color-white" rel="tooltip" data-placement="right" data-original-title="Purity"></span></li><li><a href="javascript:void(0);" class="jarviswidget-remove-colors" data-widget-setstyle="" rel="tooltip" data-placement="bottom" data-original-title="Reset widget color to default">Remove</a></li></ul>
                </div> -->
                <span class="widget-icon"><i class="fa fa-table"></i></span>
                <h2>'.$title.'<span class="badge "></span></h2>
                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                '.$toolButton.'
            </header>
            <div role="content">
                <!-- widget edit box -->
                <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                </div>
                <!-- end widget edit box -->
                <!-- widget content -->
                <div class="widget-body '.$padding.'" style="'.$height.$overflow.$bottom.$border.'">';
        return $html;
    }
    
    
    public static function listaOpciones($obj){
        if(is_array($obj)){
            $actions= (isset($obj['actions']))? $obj['actions']:'';
        }else{
            $actions= '';
        }

        $toolButton = '';
        if(!empty($actions) && is_array($actions)){
            $toolButton = '<div class=\"widget-toolbar\" role=\"menu\" >';
            $toolButton .= '<div class=\"btn-group\">';
                $toolButton .= '<button class=\"btn dropdown-toggle btn-xs btn-warning\" data-toggle=\"dropdown\">';
                $toolButton .= ''.ACTIONS.' <i class=\"fa fa-caret-down\"></i>';
                $toolButton .= '</button>';
                $toolButton .= '<ul class=\"dropdown-menu pull-right\">';
            foreach ($actions as $btn) {
                $t='';
                if(isset($btn['permiso'])){
                    $permiso = Session::getPermiso($btn['permiso']);  
                }
                
                if($permiso['permiso']){                         
                    if(isset($btn['label'])){
                        $label = $btn['label']; 
                    }else{
                        $label = $permiso['accion'];
                    }         
                    if(isset($btn['title'])) $label= $btn['title'];
                    if ($btn['flag']):                        
                        $toolButton .= '<li>';
                        $toolButton .= '<a href=\"javascript:void(0);\"  ';
                        $toolButton .= 'onclick=\"'.$btn['click'].'\" ';
                        $toolButton .= 'title=\"'.$label.'\" ><i class=\"'.$permiso['icono'].'\"></i> '.$label.'</a>';
                        $toolButton .= '</li>';
                    else:
                        $toolButton .= '<li>';
                        $toolButton .= '<a href=\"javascript:void(0);\"  ';
                        $toolButton .= ' disabled style=\"color:#CCC;\" ';
                        $toolButton .= 'title=\"'.$label.'\" ><i class=\"'.$permiso['icono'].'\"></i> '.$label.'</a>';
                        $toolButton .= '</li>';
                    endif;
                    
                }
            }
                    $toolButton .='</ul>';
                $toolButton .='</div>';
            $toolButton .='</div>';
        }               
        return $toolButton;
    }
    
    
    public static function widgetClose(){
        $html = '</div>
            </div>
        </div>';
        return $html;
    }
    
    public static function siteMap(){
        if (Formulario::getParam('_siteMap') !== false && Formulario::getParam('_siteMap') !== 'undefined'){  
            $html = '<div class="col col-12 sitemap_contenido" ><span class="sitemap" ><i class="fa fa-home"></i> \\ '.Formulario::getParam('_siteMap').'</span></div>';
        }   
        return $html;
    }   
    
    public static function help($c){
        $html = '<img src="'.BASE_URL.'public/img/h1.png" class="xhelp pointer" title="'.$c.'" onmousemove="$(\'.tooltip\').css({opacity: 1,background: \'transparent\'});$(\'.tooltip-inner\').css({\'margin-left\':\'-3px\'});" style="margin-top:7px;">';
        return $html;
    }
    
    
    /*
     * echo Functions::selectHtml(array(
                                        'data'=>$data,
                                        'atributes'=>array(
                                            'id'=>T6.'lst_tipoconcento',
                                            'name'=>T6.'lst_tipoconcento'
                                        ),
                                        'etiqueta'=>'descripcion',
                                        'value'=>'id_tipo',
                                        'defaultEtiqueta'=>'',
                                        'txtAll'=>true,
                                        'txtSelect'=>true
                                    ))
     */
     public static function selectHtml($obj) {
        $data = isset($obj['data'])?$obj['data']:array();        
        $attr = isset($obj['atributes'])?$obj['atributes']:array();
        $all  = isset($obj['txtAll'])?$obj['txtAll']:false;
        $sel  = isset($obj['txtSelect'])?$obj['txtSelect']:false;
        $etiq = isset($obj['etiqueta'])?$obj['etiqueta']:'';
        $group = isset($obj['group'])?$obj['group']:'';
        $valo = isset($obj['value'])?$obj['value']:'';
        $etid = isset($obj['defaultEtiqueta'])?$obj['defaultEtiqueta']:'';
        $multi  = isset($obj['multiple'])?$obj['multiple']:false;
        $data_id = isset($obj['data_id'])?$obj['data_id']:array();
        $encript = isset($obj['encript'])?$obj['encript']:false;
        $enabled = isset($obj['enabled'])?$obj['enabled']:false;        
        $enabled = ($enabled == true)?'disabled':'';
        $multi = ($multi== true)? 'multiple':'';
        
        $groupOld = '';
        
        $html = '<select ';
        foreach ($attr as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }
        $html .= ' '.$enabled.' '.$multi.' >';
        
        if (count($data) > 0) {
            if ($sel){
                $html .= '<option value="">'.LABEL_SELECCIONAR.'</option>';
            }
            if ($all){
                if ($encript == true) $all = Aes::en('ALL'); else $all = 'ALL';
                $html .= '<option value="'.$all.'">'.LABEL_TODOS.'</option>';
            }

            foreach ($data as $item) {
                
                /*las etiquetas*/
                if(is_array($etiq)){
                    $desc = '';
                    foreach ($etiq as $val) {
                        $desc .= $item[$val].'-';
                    }
                    $desc = substr_replace($desc, "", -1);
                }else{
                    $desc = $item[$etiq];
                }
                
                /*los valores*/
                if(is_array($valo)){
                    $key = '';
                    foreach ($valo as $vall) {
                        $key .= $item[$vall].'-';
                    }
                    $key = substr_replace($key, "", -1);
                }else{
                    $key = $item[$valo];
                }
                
                $selected = "";
                
                if(is_array($etid)){
                    foreach ($etid as $et) {
                       if ($key == $et) {
                            $selected = '  selected="selected"';                            
                       }
                    }
                }else{
                    if ($key == $etid) {
                        $selected = '  selected="selected"';
                    }
                }                              
                
                /*los data-'id' */
                $dataAtrr = "";
                if(is_array($data_id)){
                     foreach ($data_id as $key2 => $value) {                         
                        $dataAtrr .= ' data-'.$key2 .' = "'.$item[$value].'" ';                            
                    }                                       
                }
                
                /*Encriptar ID en caso se requiera */
                if ($encript == true){
                    $key = Aes::en($key);
                }
                
                if($group == ''){
                     $html .= '<option title="' . $desc . '" value="' . $key . '" '.$dataAtrr.' ' . '" ' . $selected . '>' . $desc . '</option>';
                }else{
                                       
                    $grupo = $item[$group];                                           
                    if ($groupOld !== $grupo){
                        $html .='<optgroup label="'.$grupo.'" >';                
                    }                    
                    $html .= '<option title="' . $desc . '" value="' . $key . '" '.$dataAtrr.' ' . '" ' . $selected . '>' . $desc . '</option>';                                                                                            
                    $groupOld = $grupo;                    
                    if ($groupOld !== $grupo){
                        $html .='</optgroup>';
                    }
                }                                
            }

            $html .= '</select>';
        }
        else{
            $html .= '<option value=""> - Sin datos - </option></select>';
        }
        return $html;
    }

    public static function createCell($obj){
        $t = '';
        for($i=0;$i<$obj['row'];$i++){
           $t.= '<tr>'; 
           for($j=0;$j<$obj['cols'];$j++){
               $t.='<td>&nbsp;</td>';
           }
           $t.= '</tr>';
        }
        return $t;
    }
    
    public static function cambiaf_a_mysql($fecha){
        if(!empty($fecha)){
            $mifecha = explode("/",$fecha);
            $lafecha=$mifecha[2]."-".$mifecha[1]."-".$mifecha[0];
            return $lafecha; 
        }
    } 	
    
    public static function cambiaf_a_normal($fecha){
        if(!empty($fecha)){
            $mifecha = explode("-",$fecha);
            $lafecha=$mifecha[2]."/".$mifecha[1]."/".$mifecha[0];
            return $lafecha; 
        }
    } 
    
    public static function nombreMes($mes){
        setlocale(LC_TIME, 'spanish');
        $nombre=strftime("%B",mktime(0, 0, 0, $mes, 1, 2000));
        return $nombre;
    } 
    
    public static function deleteComa($val){
        return str_replace(',', '', $val);
    }
    
    public static function convertirTiempo($valor){
        //Entrada: 0.5, 0.6, 1, 1.5, 3.5
        
        if ($valor == 0){
            return 'Indefinido';
        }
        
        $dias = 12;
        if ($valor < 1 ){
           $mes = round($dias * $valor);
           $resultado = number_format($mes,0).' meses';              
        }else{
            $decimal = explode(".",$valor);
            $nmes = $decimal[0];
            $ndia = '0.'.$decimal[1];                        
            $xdia = round($dias * $ndia);
            
            if($xdia <= 0){        
               if($valor == 1):
                  $resultado = number_format($valor,0).' año';            
                 else:
                  $resultado = number_format($valor,0).' años';                                
               endif;               
            }else{
                if($valor < 2):
                    $resultado = number_format($nmes,0).' año y '.$xdia.' meses';                        
                else:
                    $resultado = number_format($nmes,0).' años y '.$xdia.' meses';                        
                endif;
                
            }
            
             
        }                    
        return $resultado;
    }
    
    public static function nombreAleatorio($cant=10){
        $str = "1234567890";
        $cad = "";
        for($i=1;$i<=$cant;$i++){                
            $aux = substr($str,rand(0,strlen($str)),1); 
            if ($aux == '') $aux = '0';
            $cad .= $aux;           
        }
        return $cad.'_'.time();
   }          
   
   /*
	Ejemplo de la operacion:
	$var = data_youtube('http://www.youtube.com/watch?v=7OPADWu3pYo&list=UU7H0-heDAtEJL2tJ99t7UPA&index=13&feature=plcp')
	Obtenemos el codigo: 7OPADWu3pYo
	
	$thumb = data_youtube('7OPADWu3pYo', 'thumb'); //obtiene la imagen preview del video (la pequenia)
	$title = data_youtube('7OPADWu3pYo', 'title'); //obtiene el titulo del video
	$embed = data_youtube('7OPADWu3pYo', 'embed'); //obtiene el codigo para insertar el video
	
    */
  public static function data_youtube($url,$return='',$width='',$height='',$rel=0, $autoplay=0){
        $urls = parse_url($url);	
        
        $key = KEY_APP_GOOGLE;
        
        //url is http://youtu.be/xxxx
        if(isset($urls['host']) and $urls['host'] == 'youtu.be'){
            $id = ltrim($urls['path'],'/');
        }
        //url is http://www.youtube.com/embed/xxxx
        else if(strpos($urls['path'],'embed') == 1){
            $id = end(explode('/',$urls['path']));
        }        
        //url is xxxx only
        else if(strpos($url,'/')===false){
            $id = $url;
        }	
        //http://www.youtube.com/watch?feature=player_embedded&v=m-t4pcO99gI
        //url is http://www.youtube.com/watch?v=xxxx
        else{
            parse_str($urls['query']);
            $id = $v;
        }
        
        //return embed iframe
        if($return == 'embed'){
            return '<iframe class="embed-responsive-item" width="'.($width?$width:560).'" height="'.($height?$height:349).'" src="http://www.youtube.com/embed/'.$id.'?rel='.$rel.'&autoplay='.$autoplay.'" frameborder="0" allowfullscreen></iframe>';
        }        
        //return normal thumb
        else if($return == 'thumb'){
            return 'http://img.youtube.com/vi/'.$id.'/default.jpg';
        }
        //Imagen 480x360 (Grande)
        else if($return == '0'){
            return 'http://img.youtube.com/vi/'.$id.'/0.jpg';
        }        
        //Imagen 2
        else if($return == '1'){
            return 'http://img.youtube.com/vi/'.$id.'/1.jpg';
        }		
        //Imagen 3
        else if($return == '2'){
            return 'http://img.youtube.com/vi/'.$id.'/2.jpg';
        }				
        //return hqthumb
        else if($return == 'hqthumb'){
            return 'http://img.youtube.com/vi/'.$id.'/hqdefault.jpg';
        }				      
        //return Titulo
        else if ($return == 'title') {
            $videoTitle = @file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=".$key."&fields=items(id,snippet(title),statistics)&part=snippet,statistics");
            if ($videoTitle) {
                $json = json_decode($videoTitle, true);                
                return $json['items'][0]['snippet']['title'];
            }else{
                return false;
            }
        }
        //return Descripcion
        else if ($return == 'description') {
            $videoDes = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=".$key."&fields=items(id,snippet(description),statistics)&part=snippet,statistics");
            if ($videoDes) {
                $json = json_decode($videoDes, true);
                return $json['items'][0]['snippet']['description'];
            }else{
                return false;
            }				
        }		
        // else return id
        else{
            return $id;
        }
    }
    
    public static function data_vimeo($url,$return='',$width='',$height='',$autoplay=0){
                
        if ( $url === '' ) return false;
        if ( Functions::isValidURL($url) ){
           sscanf(parse_url($url, PHP_URL_PATH), '/%d', $id);           
        }else{
            $id = $url;
        }               
        
        
        //Url donde esta nuestro JSON
        $req = 'https://vimeo.com/api/oembed.json?url=https://vimeo.com/'.$id;
        //Iniciamos cURL junto con la URL
        $cVimeo = curl_init($req);
        //Agregamos opciones necesarias para leer
        curl_setopt($cVimeo,CURLOPT_RETURNTRANSFER, TRUE);
        // Capturamos la URL
        $gVimeo = curl_exec($cVimeo);
        //Descodificamos para leer
        $getVimeo = json_decode($gVimeo,true);

        //Asociamos los campos del JSON a variables        
        //$descripcion = $getVimeo['description'];
                        
        //return embed iframe
        if($return == 'embed'){
            return '<iframe class="embed-responsive-item" src="//player.vimeo.com/video/'.$id.'?autoplay='.$autoplay.'" width="'.($width?$width:560).'" height="'.($height?$height:349).'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }      			                
        //return title
        else if ($return == 'title') {     
            //$hash = unserialize(@file_get_contents("http://vimeo.com/api/v2/video/$id.php"));            
            //return $hash[0]["title"];       
            return $getVimeo['title'];
        }     
         //return normal thumb
        else if($return == 'thumb'){
            //$hash = unserialize(@file_get_contents("http://vimeo.com/api/v2/video/$id.php"));            
            //return $hash[0]["thumbnail_small"];
            return  $getVimeo['thumbnail_url'];
        }
        // else return id
        else{
            return $id;
        }
    }
    
    public static function isValidURL($url = ''){
        return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i', $url);
    }
    
    public static function comp_numb($input){
        $input = number_format($input);
        $input_count = substr_count($input, ',');
        $arr = array(1=>'K','M','B','T');
        
        if(isset($arr[(int)$input_count]))      
           return substr($input,0,(-1*$input_count)*4).$arr[(int)$input_count];
        else 
            return $input;
    }
    
    public static function eliminarArchivo($file){        
        if(file_exists($file)):
            unlink($file); 
        endif;
    } 
    
    public static function validarTipoImg($extension){
        $allowedExts = array(".gif", ".jpeg",".jpg", ".png",".JPG",".JPEG",".GIF",".PNG" );
        if( !in_array($extension, $allowedExts) ){         
            return false;
        }     
        return true;
    }
    
    public static function textoLargo($cadena,$caracter=100){
        $cantidad = strlen($cadena);
        $decri=substr(strip_tags($cadena),0,$caracter);	
        $nuevacantidad = strlen($decri);		
        if($nuevacantidad < $cantidad ) $decri = $decri.'...';
        return $decri;                
    }	
    
    public static function formato_fecha($datetime, $formato="%A, %d de %B del %Y"){	
        date_default_timezone_set('America/Lima');
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        if(is_null($datetime)){
                return "";	
        }		
        $fecha = strftime($formato,strtotime($datetime));
        $fecha = utf8_encode(ucfirst($fecha));				
        return $fecha;	
    }        
    
    public static function ucname($string) {       
        $string =ucwords(mb_strtolower($string, 'UTF-8'));
        foreach (array('-', '\'') as $delimiter) {
          if (strpos($string, $delimiter)!==false) {
            $string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
          }
        }
        return $string;
    }    
    
    public static function htmlRender($str){
      $patron = array (
            // Vocales
            '/&agrave;/' => 'a', '/&egrave;/' => 'e', '/&igrave;/' => 'i', '/&ograve;/' => 'o', '/&ugrave;/' => 'u',
            '/&aacute;/' => 'a', '/&eacute;/' => 'e', '/&iacute;/' => 'i', '/&oacute;/' => 'o', '/&uacute;/' => 'u', 
            '/&acirc;/' => 'a', '/&ecirc;/' => 'e', '/&icirc;/' => 'i', '/&ocirc;/' => 'o', '/&ucirc;/' => 'u', 
            '/&atilde;/' => 'a', '/&etilde;/' => 'e', '/&itilde;/' => 'i', '/&otilde;/' => 'o', '/&utilde;/' => 'u',
            '/&auml;/' => 'a', '/&euml;/' => 'e', '/&iuml;/' => 'i', '/&ouml;/' => 'o', '/&uuml;/' => 'u', 
            '/&auml;/' => 'a', '/&euml;/' => 'e', '/&iuml;/' => 'i', '/&ouml;/' => 'o', '/&uuml;/' => 'u',				
            // Otras letras
            '/&aring;/' => 'a', '/&ntilde;/' => 'n',
            //caracteres especiales	
            '/&amp;/' => ' & ','/&AMP;/' => ' & '                                
        );	
      
        $html = preg_replace(array_keys($patron),array_values($patron),$str);	
        return $html;
    } 
    
     function returnMacAddress() {
          // This code is under the GNU Public Licence
       // Written by michael_stankiewicz {don't spam} at yahoo {no spam} dot com
       // Tested only on linux, please report bugs

       // WARNING: the commands 'which' and 'arp' should be executable
      // by the apache user; on most linux boxes the default configuration
      // should work fine

       // Get the arp executable path
        $location = `which arp`;
       // Execute the arp command and store the output in $arpTable
       $arpTable = `arp -a`;
       // Split the output so every line is an entry of the $arpSplitted array
       $arpSplitted = explode("\\n",$arpTable);
       // Get the remote ip address (the ip address of the client, the browser)
       $remoteIp = getenv('REMOTE_ADDR');
       // Cicle the array to find the match with the remote ip address
       foreach ($arpSplitted as $value) {
         // Split every arp line, this is done in case the format of the arp
         // command output is a bit different than expected
          $valueSplitted = explode(" ",$value);
          foreach ($valueSplitted as $spLine) {
           if (preg_match("/$remoteIp/",$spLine)) {
                $ipFound = true;
          }
        // The ip address has been found, now rescan all the string
        // to get the mac address
        if (isset($ipFound)) {
               // Rescan all the string, in case the mac address, in the string
               // returned by arp, comes before the ip address
               // (you know, Murphy's laws)
           reset($valueSplitted);
           foreach ($valueSplitted as $spLine) {
                 if (preg_match("/[0-9a-f][0-9a-f][:-]".
                     "[0-9a-f][0-9a-f][:-]".
                     "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                  "[0-9a-f][0-9a-f]/i",$spLine)) {
                     return $spLine;
                  }
              }
         }
        $ipFound = false;
       }
       }
       return false;
     }

}
?>
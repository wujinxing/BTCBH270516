<?php
##############################################
# Shiege Iseng Resize Class
# 11 March 2003
# shiegege_at_yahoo.com
# View Demo :
#   http://shiege.com/scripts/thumbnail/
/*############################################
Sample :
$thumb=new thumbnail("./shiegege.jpg");			// generate image_file, set filename to resize
$thumb->size_width(100);				// set width for thumbnail, or
$thumb->size_height(300);				// set height for thumbnail, or
$thumb->size_auto(200);					// set the biggest width or height for thumbnail
$thumb->jpeg_quality(75);				// [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
$thumb->show();						// show your thumbnail
$thumb->save("./huhu.jpg");				// save your thumbnail to file
----------------------------------------------
Note :
- GD must Enabled
- Autodetect file extension (.jpg/jpeg, .png, .gif, .wbmp)
  but some server can't generate .gif / .wbmp file types
- If your GD not support 'ImageCreateTrueColor' function,
  change one line from 'ImageCreateTrueColor' to 'ImageCreate'
  (the position in 'show' and 'save' function)
*/############################################


class thumbnail
{
	var $img=array();

	function thumbnail($imgfile)
	{
		//detect image format
		$this->img["format"]= $this->extension($imgfile);
		$this->img["format"]=strtoupper($this->img["format"]);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
                    //JPEG
                    $this->img["format"]="JPEG";
                    $this->img["src"] = imagecreatefromjpeg ($imgfile);
		} elseif ($this->img["format"]=="PNG") {
                    //PNG
                    $this->img["format"]="PNG";
                    $this->img["src"] = imagecreatefrompng ($imgfile);
		} elseif ($this->img["format"]=="GIF") {
                    //GIF
                    $this->img["format"]="GIF";
                    $this->img["src"] = imagecreatefromgif ($imgfile);
		} elseif ($this->img["format"]=="WBMP") {
                    //WBMP
                    $this->img["format"]="WBMP";
                    $this->img["src"] = imagecreatefromwbmp($imgfile);
		} else {
                    //DEFAULT
                    //echo "No soporta Archivo: ".$this->img["format"];
                    //exit();
		}
		$this->img["lebar"] = imagesx($this->img["src"]);
		$this->img["tinggi"] = imagesy($this->img["src"]);
		//default quality jpeg
		$this->img["quality"]=75;
	}

	function size_height($size=100)
	{
            //height
            $this->img["tinggi_thumb"]=$size;
            $this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	}

	function size_width($size=100){
            //width
            $this->img["lebar_thumb"]=$size;
            $this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	}

	function size_auto($size=100){
            //size
            /*Validar que si el size es mayor que la imagen no debe de ejecutar esto */
            if ($this->img["lebar"] > $size ){                                                    
                if ($this->img["lebar"]>=$this->img["tinggi"]) {
                    $this->img["lebar_thumb"]=$size;
                    $this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
                } else {
                    $this->img["tinggi_thumb"]=$size;
                    $this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
                }
            }
            //print_r($this->img);
	}

	function jpeg_quality($quality=75){
            //jpeg quality
            $this->img["quality"]=$quality;
	}

	function show()
	{
		//show thumb
		@Header("Content-Type: image/".$this->img["format"]);

		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		$this->img["des"] = @imagecreatetruecolor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
    		imagecopyresized ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imagejpeg($this->img["des"],"",$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagepng($this->img["des"]);
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imagegif($this->img["des"]);
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			imagewbmp($this->img["des"]);
		}
	}

	function save($save=""){

		//save thumb
		if (empty($save)) $save=strtolower("./thumb.".$this->img["format"]);
		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		$this->img["des"] = imagecreatetruecolor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
		/*antes: @imagecopyresized */
                imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imagejpeg($this->img["des"],$save,$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagepng($this->img["des"],$save);

		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imagegif($this->img["des"],$save);
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			imagewbmp($this->img["des"],$save);
		}
	}
        
        function saveCrop($save="", $x = 0, $y = 0, $w = 0, $h = 0){

            //save thumb
            if (empty($save)) $save=strtolower("./thumb.".$this->img["format"]);
            /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
            $this->img["des"] = imagecreatetruecolor($w, $h);

            imagecopyresampled ($this->img["des"], $this->img["src"],0 , 0, $x, $y, $w, $h, $w, $h);
            if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
                //JPEG
                imagejpeg($this->img["des"],$save,$this->img["quality"]);
            } elseif ($this->img["format"]=="PNG") {
                //PNG
                $color  =  imagecolorallocate ($this->img["des"],255,255,255);                  
                imagefill($this->img["des"], 0, 0, $color);
                imagepng($this->img["des"],$save);

            } elseif ($this->img["format"]=="GIF") {
                //GIF
                imagegif($this->img["des"],$save);
            } elseif ($this->img["format"]=="WBMP") {
                //WBMP
                imagewbmp($this->img["des"],$save);
            }
	}
                       
        function extension($filename){
             return substr(strrchr($filename, '.'), 1);
        }
}
?>
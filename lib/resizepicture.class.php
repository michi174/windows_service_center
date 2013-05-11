<?php


class resizePicture
{
	
	public $dst_height	= null;
	public $dst_width	= null;
	
	/*public function __constructor($dst_height, $dst_width)
	{
		self::$dst_height	= (!empty($dst_height)) ? $dst_height : 0;
		self::$dst_width	= (!empty($dst_width)) ? $dst_width : 0;
	}*/
			
	public function resizePicture($file, $dst_height, $dst_width)
	{
		$thumb_dir 		= "/resized";
		$thumbfile 		= $thumb_dir.substr($file,strrpos($file,'/') + 1);
		
		
		if( !$file || !file_exists( $file ) ) die("Datei ".$file." nicht vorhanden");
	
		$imagedata = getimagesize($file);
		$width = $imagedata[0];
		$height = $imagedata[1];
		
		
		if(!empty($dst_height))
		{
			$thumbheight	= $dst_height;
			$thumbwidth		= floor($width * $dst_height / $height);
		}		
		elseif(!empty($dst_width))
		{
			$thumbwidth		= $dst_width;
			$thumbheight	= floor($height * $dst_width / $width);
		}
		else
		{
			$error 		= TRUE;
			$error_msg	= "Es wurde keine Zielgröße definiert.";
		}
			
		if(!file_exists( $thumbfile ))
		{
	
			if($imagedata[2] == 1)
			{
				$image = imagecreatefromgif($file);
				$thumb = imagecreatetruecolor($thumbwidth, $thumbheight);
				imagecopyresized($thumb, $image, 0, 0, 0, 0, $thumbwidth, $thumbheight, $width, $height);
				imagegif($thumb, $thumbfile);
			}
			elseif($imagedata[2] == 2)
			{
				$image = imagecreatefromjpeg($file);
				$thumb = imagecreatetruecolor($thumbwidth, $thumbheight);
				imagecopyresized($thumb, $image, 0, 0, 0, 0, $thumbwidth, $thumbheight, $width, $height);
				imagejpeg($thumb, $thumbfile);
			}
			elseif($imagedata[2] == 3)
			{
				$image = imagecreatefrompng($file);
				$thumb = imagecreatetruecolor($thumbwidth, $thumbheight);
				imagecopyresized($thumb, $image, 0, 0, 0, 0, $thumbwidth, $thumbheight, $width, $height);
				imagejpeg($thumb, $thumbfile);
			}
		}
		$image = fopen ( $thumbfile, "rb");
		fpassthru ($image);
		fclose ($image);
	}
}
?>
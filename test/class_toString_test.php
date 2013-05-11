<?php
$file		= "../template/win8_style/systemnotification_button.tpl";
$handler	= fopen($file, "rb");

if(file_exists($file))
{
	while (!feof($handler))
	{
		$text	= fgetss($handler,1024, "<div>");
		echo $text;
	}
	
	
}



?>
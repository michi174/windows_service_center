<?php
use wsc\autoload\Autoload;

function autoload($className)
{
	$path = wsc\config\Config::getInstance()->get("class_dir")."/".str_replace("\\", "/", $className).".php";
	if(file_exists($path))
	{
		require_once($path);
	}
	else
	{
		//Nur zum debuggen
		echo "	<br /><br />WSC Autoload Fehler: Die Datei <br /><strong>" . $path . " </strong><br /> 
				konnte nicht eingebunden werden, da die Datei nicht gefunden wurde.<br /><br />";
	}
}

Autoload::register("autoload");
?>
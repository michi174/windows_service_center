<?php
use wsc\autoload\Autoload;

function my_autoload($className)
{
	$path = wsc\config\Config::getInstance()->get("project_classes")."/".str_replace("_", "/", $className).".class.php";
	if(file_exists($path))
	{
		require_once($path);
	}
	else
	{
		die("<br /><br />WSC Autoload Fehler: Die Datei <br /><strong>" . $path . " </strong><br /> konnte nicht eingebunden werden, da die Datei nicht gefunden wurde.");
	}
}

Autoload::register("my_autoload");
?>
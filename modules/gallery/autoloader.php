<?php

namespace gallery;

use wsc\config\Config;
/**
 *
 * @author Michi
 *        
 */
class autoloader 
{
	/**
	 * Der Autoloader
	 * 
	 * @param string $class		Name der aufzurufenden Klasse
	 */
	public static function autoload($class)
	{
		$path = Config::getInstance()->get("project_dir")."/".Config::getInstance()->get("module_dir")."/".str_replace("\\", "/", $class).".php";
		
		if(file_exists($path))
		{
			require_once($path);
		}
		else
		{
			//Nur zum debuggen
			echo "	<br /><br />Modul Autoload Fehler: Die Datei <br /><strong>" . $path . " </strong><br />
					konnte nicht eingebunden werden, da die Datei nicht gefunden wurde.<br /><br />";
		}
	}
	
	/**
	 * Registriert den Autoloader
	 */
	public static function register()
	{
	    try {
	        spl_autoload_register(array('gallery\autoloader', "autoload"));
	    }
		catch (\Exception $e)
		{
		    echo $e->getMessage()."<br><br>";
		    echo nl2br($e->getTraceAsString());
		    die();
		}
	}
}

?>
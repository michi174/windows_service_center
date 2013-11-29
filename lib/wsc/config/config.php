<?php

namespace wsc\config;

/**
 * Config (2013 - 05 - 31)
 * 
 * Konfiguriert websitespezifische Einstellungen.
 *
 * @name Config
 * @version 1.0
 * @author Michi
 * @copyright 2013 - Michael Strasser. Alle Rechte vorbehalten.
 *        
 */
final class config 
{
	private static $object	= NULL;
	
	public $environment			= "development";
	public $template_dir		= "win8_style";
	public $default_timezone	= "Europe/Vienna";
	public $DATABASE_NAME		= "wsc";
	public $DATABASE_HOST		= "localhost";
	public $DATABASE_USER		= "root";
	public $DATABASE_PASSWORD	= "";
	
	
	/**
	 * Singleton Methode damit die Config nicht versehentlich überschrieben werden kann.
	 * 
	 * @return object $config
	 */
	public static function getInstance()
	{
		if(self::$object instanceof Config)
		{
			return self::$object;
		}
		else
		{
			$object			= new Config();
			self::$object	= $object;
				
			return $object;
		}
	}
}

?>
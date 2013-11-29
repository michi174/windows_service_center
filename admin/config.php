<?php

setlocale (LC_ALL, 'deu');

define("DEVELOPMENT_MODE", true);
	
/*if(DEVELOPMENT_MODE === true)
{
	ini_set("error_reporting", E_ALL);
	ini_set("display_errors", 1);
}
else
{
	ini_set("error_reporting", E_ALL^E_NOTICE);
	ini_set("display_errors", 0);
	ini_set("log_errors", 1);
	ini_set("error_log", "I:/Stuff/Website/Zend Server/ZendServer/logs/php_error.log");
}*/

define("DATABASE", "wsc");				//Name der Datenbank
define("DATABASE_USER", "Michael");		//Benutzername
define("DATABASE_PASSWORD", "425262");	//Passwort
define("DATABASE_HOST", "localhost");	//Host

define("DEFAULT_TIME_ZONE", date_default_timezone_set("Europe/Vienna")); //Zeitzone
define("MAX_TIPS_PER_PAGE", 10); //Maximale Anzahl von Tips pro Seite
define("DEFAULT_LINK", "site" ); //z.B.: index.php?DEFAULT_LINK=news&action=new
define("DEFAULT_ACTION", "action");
define("PLUGIN_DIR", "plugins/"); //Verzeichnis für Plug-ins
define("DOCUMENT_ROOT", $_SERVER["DOCUMENT_ROOT"] . "/windows_service_center/");


$dbconn	= mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
mysql_select_db(DATABASE);
?>
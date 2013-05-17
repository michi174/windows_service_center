<?php
/**
 * PluginManager (2013 - 03 - 02)
 *
 * Klasse um Plug-ins zu verwalten.
 *
 * @author 		michi_000
 * @name 		Login
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */
class PluginManager
{
	/**
	 * @var (string) - Pluginverzeichnis
	 * @since 1.0
	 */
	protected $dir_path		= PLUGIN_DIR;
	
	
	/**
	 * @var (array) Verzeichnisse - Vorhandene Verezichnisse im Pluginvereichnis
	 * @since 1.0
	 */
	protected $directories	= array();
	
	
	/**
	 * @var (array) Plug-ins - Vorhandene Plug-ins in der Datenbank
	 * @since 1.0
	 */
	protected $plugins		= array();
	
	
	/**
	 * Sucht bereits vorhandene Plug-ins.
	 *
	 * @since 1.1
	 */
	protected function getRegisteredPlugins()
	{
		$db		= new Database();
		
		$sql	= "SELECT plugin_name FROM areas";
		$res	= $db->query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $db->error);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			while(($row = $res->fetch_assoc()) == true)
			{
				$this->plugins[]	= $row['plugin_name'];
			}
		}
	}
	
	
	/**
	 * Sucht neue Plug-ins im Verzeichnis und registriert diese in der Datenbank.
	 *
	 * @since 1.1
	 */
	public function checkPlugins()
	{
		$this->getRegisteredPlugins();
		
		$open_dir		= opendir($this->dir_path);
		
		while(($files = readdir($open_dir)) == true)
		{
			$filetype	= filetype($this->dir_path . $files);
			
			if($filetype == "dir" && $files != "." && $files != "..")
			{
				$this->directories[]	= $files;
			}
		}
	
		//Wenn Plug-ins vorhanden sind.
		if(count($this->directories) > 0)
		{
			foreach($this->directories as $directory)
			{
				
				//Wenn das Plug-in noch nicht in der Datenbank registiert wurde.
				if(!in_array($directory, $this->plugins))
				{
					$this->addPlugin($directory);
				}
			}
		}
	}
	
	
	/**
	 * Fügt Plug-ins in die Datenbank ein.
	 *
	 * @since 1.1
	 */
	public function addPlugin($dir)
	{
		$plugin_name	= NULL; //Diese Variablen werden von der unten eingebundenen "install"-Datei Werte zugewiesen.
		$display_name	= NULL;
		$menu_item		= NULL;
		$style_name		= NULL;
		$default_action	= NULL;
		$sort			= NULL; //Ende "install" Zuweisung
		
		$install	= require_once($this->dir_path . $dir . "/install.php");

		$this->sortPlugins($sort);
			
		$sql	= "	INSERT
						areas
								(plugin_name,
								display_name,
								menu_item,
								default_action,
								style_name,
								sort)
						VALUES
								('". $plugin_name ."',
								'". $display_name ."',
								'". $menu_item ."',
								'". $default_action ."',
								'". $style_name ."',
								'". $sort ."')";
		
		$res	= mysql_query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . mysql_error() . "<br />");
	}
	
	
	/**
	 * Sortiert Plug-ins in der Datenbank.
	 *
	 * @since 1.1
	 */
	protected function sortPlugins($sort)
	{
		$sql	= "SELECT id, sort FROM areas WHERE sort >= '" . $sort . "' ORDER BY sort ASC";
		$res	= mysql_query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . mysql_error() . "<br />");
		$num	= mysql_num_rows($res);
		
		if($num > 0)
		{
			while(($row = mysql_fetch_assoc($res)) == true)
			{
				if($row['sort'] == $sort)
				{
					$sql	= "UPDATE areas SET sort = sort+1 WHERE id = " . $row['id'];
					mysql_query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . mysql_error() . "<br />");
				}
			}
		}
	}
	
	
	/**
	 * Gibt registrierte Plug-ins auf dem Bildschirm aus bzw. liefert ein Array mit den registrierten Plug-ins zurück.
	 *
	 * @param (bool) print
	 * @return (array) plugins oder (bool) false
	 * @since 1.1
	 */
	public static function getPlugins($print)
	{
		$plugins	= array();
		$db			= new Database();		
		
		$sql		= "SELECT * FROM areas WHERE menu_item = 1 ORDER BY sort ASC";
		$res		= $db->query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $db->error);
		$num		= $res->num_rows;
		
		
		if($num > 0)
		{
			while(($row = $res->fetch_assoc()) == true)
			{
				if($print == true)
				{
					if(!empty($row['default_action']))
					{
						$action	= "&action=" . $row['default_action'];
					}
					else
					{
						$action	= NULL;
					}
					
					echo "<li class=\"". $row['style_name'] ."\"><a href=\"?". DEFAULT_LINK ."=". $row['plugin_name'] . $action . "&id=" . $row['id'] . "\">". $row['display_name'] ."</a></li>";
				}
				else
				{
					$plugins[]	= $row['plugin_name'];
				}
			}
			
			return $plugins;
		}
		else
		{
			return false;
		}
	}
}
?>
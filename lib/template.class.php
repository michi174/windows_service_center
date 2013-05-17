<?php

/**
 * Template (2013 - 03 - 14)
 *
 * Template Klasse, die HTML Templates einliest und Templatevariablen durch Werte ersetzt.
 *
 * @author 		michi_000
 * @name 		Template
 * @version		1.0 Alpha
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */

class Template
{	
	/**
	 * Linker Delimeter
	 * 
	 * @var (string) $delimeter_left	
	 * @since 1.0
	 */
	protected $delimeter_left		= "{";
	
	
	/**
	 * Rechter Delimeter
	 * 
	 * @var (string) $delimeter_right
	 * @since 1.0
	 */
	protected $delimeter_right	= "}";
	
	
	/**
	 * Templateverzeichnis
	 * 
	 * @var (string) $template_dir
	 * @since 1.0
	 */
	protected $template_dir		= NULL;
	
	
	/**
	 * Templatename
	 * 
	 * @var (string) $templates		
	 * @since 1.0
	 */
	protected $templates			= NULL;


	/**
	 * Inhalt der Templatedatei bzw. fertige HTML Ausgabe.
	 *
	 * @var string $output
	 * @since 1.0
	 */
	protected $output				= NULL;
	
	
	/**
	 * Templatevariablen inklusive Werte
	 * 
	 * @var (array) $tpl_vars
	 * @since 1.0
	 */
	protected $tpl_vars			= array();


	/**
	 * Variablen, die durch Funktionen ersetzt werden müssen
	 *
	 * @var (array) $assigned_functions
	 * @since 1.0
	 */
	protected $assigned_functions		= array();

	
	
	/**
	 * SQL-Befehl für Datarows
	 * 
	 * @var (array) $datarow_ressource
	 * @since 1.0
	 */
	protected $datarow_ressource	= array();
	
	
	/**
	 * Datarow Templatevariablen inklusive Werte
	 * 
	 * @var (array) $datarow_vars
	 * @since 1.0
	 */
	protected $datarow_vars		= array();

	
	
	/**
	 * Subrows für Datarows
	 * 
	 * @var (array) $subrows
	 * @since 1.0
	 */
	protected $subrows		= array();
	
	
	/**
	 * Subrow Variablen samt Werte
	 * 
	 * @var (array) $subrow_vars
	 * @since 1.0
	 */
	protected $subrow_vars		= array();
	
	
	/**
	 * @var (object) $db		Datenbank Objekt
	 * @since 1.0
	 */
	protected $db		= NULL;
	
	
	/**
	 * @var (object) $notify	Benachrichtigungs Objekt
	 * @since 1.0
	 */
	protected $notify		= NULL;
	
	
	/**
	 * @var (object) $bboce		bbCode Objekt
	 * @since 1.0
	 */
	protected $bbcode		= NULL;
	
	
	
	public function __construct($db = NULL)
	{
		if(is_object($db))
		{
			$this->db = $db;
		}
		$this->notify	= new SystemNotification("warning");
		$this->bbcode	= new BBCode();
	}
	
	
	/**
	 * Template-Datei wird eingelesen.
	 *
	 * @return boolean
	 * @since 1.0
	 */
	protected function openTemplate()
	{
		$file	= $this->template_dir.$this->templates;
	
		if(file_exists($file))
		{
			$this->output	= file_get_contents($file);
				
			return true;
		}
		else
		{
			$this->notify->addMessage("Das Template &rsquo;" . $file . "&rsquo; wurde nicht gefunden!","error");
			return false;
		}
	}
	
	
	/**
	 * Kompiliert ein Template
	 *
	 * @return boolean
	 * @since 1.0
	 */
	protected function compileTemplate()
	{
		$this->replaceFunctions();
		$this->replaceVariables();
	}
	
	
	/**
	 * Templatevariablen werden ersetzt.
	 *
	 * @since 1.0
	 */
	protected function replaceVariables()
	{
		foreach($this->tpl_vars as $tpl_var => $value)
		{
			if(!is_array($value))
			{
				$tpl_var		= $this->delimeter_left.$tpl_var.$this->delimeter_right;
				$this->output	= str_replace($tpl_var, $value, $this->output);
			}
		}
	}
	
	
	
	/**
	 * Funktionen werden kompiliert.
	 *
	 * @since 1.0
	 */
	protected function replaceFunctions()
	{
		$this->replaceForeach();
		$this->multiDatarow();
		
		$this->output	= $this->parseArrayKeys($this->output);
	}
	
	
	protected function parseIf($section)
	{
		
	}
	
	/**
	 * Template Arraykeys werden ersetzt.
	 * 
	 * @param (string)	$section		Teil der auf Arraykeys abgesucht werden muss.
	 * @return (string)	$section		Fertig kompilierter Abschnitt.
	 *
	 * @since 1.0
	 */
	protected function parseArrayKeys($section)
	{
		$pattern	= '#\{([\w]*)\:([\w]*)\}#ismU'; #Muster: {arrayname:arraykey}
		$array_keys	= array();
	
		while(preg_match($pattern, $section, $array_keys) == true)
		{
			$array_keys_array	= $array_keys[1];
			$array_keys_key		= $array_keys[2];
				
			if(array_key_exists($array_keys_array, $this->tpl_vars))
			{
				if(array_key_exists($array_keys_key, $this->tpl_vars[$array_keys_array]))
				{
					$array_keys_search	= "{" . $array_keys_array . ":" . $array_keys_key . "}";
					$array_keys_replace	= $this->tpl_vars["$array_keys_array"]["$array_keys_key"];
						
					$section	= str_replace($array_keys_search, $array_keys_replace, $section);
				}
				else
				{
					$this->notify->addMessage("<strong>Warnung:</strong> Undefinierter Arraykey &rsquo;" . $array_keys_key . "&rsquo; in Array &rsquo;" . $array_keys_array . "&rsquo;");
					break;
				}
			}
			else
			{
				$this->notify->addMessage("<strong>Fehler:</strong> " . $array_keys_array . " ist kein Array.", "error");
				break;
			}
		}
		return $section;
	}
	
	
	
	/**
	 * Foreach Schleifen behandeln.
	 *
	 * @since 1.0
	 */
	
	protected function replaceForeach()
	{
		$pattern	= '#\{foreach[\s]{1}(.+)[\s]{1}key=(.+)[\s]{1}value=(.*)\}(.*)\{\/foreach\}#ismU';
		$foreach	= array();
	
		while(preg_match($pattern, $this->output, $foreach) == true)
		{
			$foreach_name	= $foreach[1];
			$foreach_key	= $foreach[2];
			$foreach_value	= $foreach[3];
			$foreach_repeat	= $foreach[4];
			
			if(array_key_exists($foreach_name, $this->tpl_vars) && is_array($this->tpl_vars[$foreach_name]))
			{
				$foreach_array				= $this->tpl_vars[$foreach_name];
				$foreach_compiled_dataset	= array();
				
				if($foreach_value != "_NULL")
				{
					foreach($foreach_array as $key	=> $value)
					{
						$replace_value	= (is_array($value)) ? "" : $value;
						
						$foreach_var_search						= array("{".$foreach_key."}", "{".$foreach_value."}");
						$foreach_var_replace					= array($key, $replace_value);
						$foreach_compiled_dataset[]	= str_replace($foreach_var_search , $foreach_var_replace, $foreach_repeat);
						
						$array_key	= max(array_keys($foreach_compiled_dataset));

						if(is_array($value))
						{
							$this->assign($foreach_value, $value);
							$foreach_compiled_dataset[$array_key]	= $this->parseArrayKeys($foreach_compiled_dataset[$array_key]);
						}
					}
				}
				else
				{
					foreach($foreach_array as $key)
					{
						if(is_array($key))
						{
							$this->notify->addMessage("<strong>Hinweis:</strong> Es werden keine mehrdimensionalen Arrays im Template unterst&uuml;tzt.<br>", "information");
							$value	= "(array)";
						}else 
						{
							$foreach_var_search			= array("{".$foreach_key."}");
							$foreach_var_replace		= array($key);
	
							$foreach_compiled_dataset[]	= str_replace($foreach_var_search , $foreach_var_replace, $foreach_repeat);
						}
					}
				}
				
				$replace_dataset		= implode("", $foreach_compiled_dataset);
				$foreach_pattern_all	= '#\{foreach '.$foreach_name.' key='.$foreach_key.' value='.$foreach_value.'\}.*\{\/foreach\}#ismU';
				$this->output			= preg_replace($foreach_pattern_all, $replace_dataset, $this->output);
			}
			else
			{
				$this->notify->addMessage("<strong>Fehler:</strong> Das Array &rsquo;" . $foreach_name ."&rsquo; wurde nicht definiert.", "error");
				break;
			}
		}
		//var_dump($this->tpl_vars['lastname']);
	}
	
	
	/**
	 * Datarows werden durch DB-Ergebnisse ersetzt.
	 *
	 * @since 1.0
	 */
	protected function multiDatarow()
	{
		$datarow_matches	= array();
		$subrow_matches		= array();	
		
		$datarow_pattern	= '#\{datarow[\s]{1}([\w]+)\}(.*)\{\/datarow\}#ismU';
		$subrow_pattern		= '#\{subrow[\s]{1}([\w]+)\}(.*)\{\/subrow\}#ismU';
		
		
		//Datarows herausfiltern
		preg_match_all($datarow_pattern, $this->output, $datarow_matches);
		$datarow_names		= $datarow_matches[1];
		$datarow_inhalte	= $datarow_matches[2];
		foreach($datarow_names as $datarow_key => $datarow_name)
		{
			if(array_key_exists($datarow_name, $this->datarow_vars))
			{
				$datarow_db_ressource	= $this->db->query($this->datarow_ressource[$datarow_name]) or die($this->db->error);
				$datarow_db_num_rows		= $datarow_db_ressource->num_rows;
				
				if($datarow_db_num_rows > 0)
				{
					$datarow_compiled_dataset_array_key	= 0;
					while(($datarow_dataset	= $datarow_db_ressource->fetch_assoc()) == true)
					{
						//Datarow Variablen durch Werte ersetzen.
						$search_datarow_var		= array();
						$replace_datarow_var	= array();
						$datarow_var_array_key	= 0;
						foreach($this->datarow_vars[$datarow_name] as $datarow_var => $datarow_var_inhalt)
						{
							$search_datarow_var[$datarow_name][]	= "{" . $datarow_name . "." . $datarow_var . "}";
							$replace_datarow_var[$datarow_name][]	= $datarow_dataset[$datarow_var_inhalt];
							
							//Funktionen auf die Werte der Variablen anwenden.
							$replace_datarow_var[$datarow_name][$datarow_var_array_key]	= $this->replaceVarFunction($search_datarow_var[$datarow_name][$datarow_var_array_key], $replace_datarow_var[$datarow_name][$datarow_var_array_key]);
							
							$datarow_var_array_key	+= 1;
						}
						$datarow_compiled_dataset[$datarow_name][]	= str_replace($search_datarow_var[$datarow_name], $replace_datarow_var[$datarow_name], $datarow_inhalte[$datarow_key]);
							
						//Subrows herausfiltern
						preg_match_all($subrow_pattern, $datarow_compiled_dataset[$datarow_name][$datarow_compiled_dataset_array_key], $subrow_matches[$datarow_name]);
						$subrow_names[$datarow_name]	= $subrow_matches[$datarow_name][1];
						$subrow_inhalte[$datarow_name]	= $subrow_matches[$datarow_name][2];
						foreach($subrow_names[$datarow_name] as $subrow_key => $subrow_name)
						{
							//Wenn die Subrow im Hauptprogramm deklariert wurde
							if(array_key_exists($subrow_name, $this->subrows[$datarow_name]))
							{	
								//SQL-Befehl für Subrow kompilieren.
								foreach($this->datarow_vars[$datarow_name] as $datarow_var => $datarow_var_inhalt)
								{		
									$subrow_pre_sql			= $this->subrows[$datarow_name][$subrow_name]['sql'];
									$subrow_compiled_sql	= str_replace("{".$datarow_name.".".$datarow_var."}", $datarow_dataset[$datarow_var_inhalt], $subrow_pre_sql);
								}
								//Datensätze der Subrow auslesen
								$subrow_db_ressource	= $this->db->query($subrow_compiled_sql) or die($this->db->error);
								$subrow_db_num_rows		= $subrow_db_ressource->num_rows;
								
								if($subrow_db_num_rows > 0)
								{
									while(($subrow_dataset	= $subrow_db_ressource->fetch_assoc()) == true)
									{
										//Jetzt müssen die Subrow Template Variablen durch die Werte ersetzt werden...
										$search_subrow_var	= array();
										$replace_subrow_var	= array();
		
										$subrow_var_array_key	= 0;
										foreach($this->subrow_vars[$datarow_name][$subrow_name] as $subrow_var => $subrow_var_value)
										{
											$search_subrow_var[$datarow_name][$subrow_name][]	= "{" . $subrow_name ."." . $subrow_var . "}";
											$replace_subrow_var[$datarow_name][$subrow_name][]	= $subrow_dataset[$subrow_var_value];
											
											//Funktionen auf die Werte der Variablen anwenden.
											$replace_subrow_var[$datarow_name][$subrow_name][$subrow_var_array_key]	= $this->replaceVarFunction($search_subrow_var[$datarow_name][$subrow_name][$subrow_var_array_key], $replace_subrow_var[$datarow_name][$subrow_name][$subrow_var_array_key]);
											$subrow_var_array_key	+= 1;
										}
										$subrow_compiled_dataset[$datarow_name][$subrow_name][]	= str_replace($search_subrow_var[$datarow_name][$subrow_name], $replace_subrow_var[$datarow_name][$subrow_name], $subrow_inhalte[$datarow_name][$subrow_key]);
									}
									$subrow_replace_data		= implode("", $subrow_compiled_dataset[$datarow_name][$subrow_name]);
								}
								else
								{
									$subrow_replace_data		= "keine Eintr&auml;ge gefunden.";
								}
								//Subrows werden ersetzt
								$subrow_pattern_all			= '#\{subrow[\s]{1}'.$subrow_name.'\}.*\{\/subrow\}#ismU';
								$datarow_compiled_dataset[$datarow_name][$datarow_compiled_dataset_array_key] = preg_replace($subrow_pattern_all, $subrow_replace_data, $datarow_compiled_dataset[$datarow_name][$datarow_compiled_dataset_array_key]);
								$subrow_compiled_dataset	= NULL;
								
							}
							else
							{
								$this->notify->addMessage("Subrow &rsquo;".$subrow_name."&rsquo; wurde nicht definiert.");
							}
						}
						$datarow_compiled_dataset_array_key++;
					}			
					$datarow_pattern_all		= '#\{datarow[\s]{1}'.$datarow_name.'\}.*\{\/datarow\}#ismU';
					$datarow_replace_data		= implode("", $datarow_compiled_dataset[$datarow_name]);			
					$this->output				= preg_replace($datarow_pattern_all, $datarow_replace_data, $this->output);
				}
				else
				{
					$datarow_pattern_all		= '#\{datarow[\s]{1}'.$datarow_name.'\}.*\{\/datarow\}#ismU';
					$this->output				= preg_replace($datarow_pattern_all, "Es wurden keine Datens&auml;tze gefunden.", $this->output);
				}
			}
			else
			{
				$this->notify->addMessage("Datarow &rsquo;" . $datarow_name . "&rsquo; wurde nicht definiert.", "information");
			}
		}
	}
	
	
	/**
	 * Wendet eine Funktionen auf eine Templatevariable an.
	 * 
	 * @param (string) $var		Variable, auf die die Funktion angewandt wird.
	 * @param (string) $value	Inhalt der Variable.
	 *
	 * @since 1.0
	 */
	protected function replaceVarFunction($var, $value)
	{
		if(array_key_exists($var, $this->assigned_functions))
		{
			$function		= preg_replace('#\{var\}#ismU', $value, $this->assigned_functions[$var]);
			$compiled_code	= "FUNCTION COMPILING FAILED on ".__FILE__.":".__LINE__;
			
			eval("\$compiled_code = $function;");
		}
		else
		{
			$compiled_code	= $value;
		}
		return $compiled_code;
	}


	/**
	 * Deklariert Templatevariablen
	 *
	 * @param (mixed) $tpl_vars		Variablen in der Template-Datei
	 * @param (mixed) $replace		Werte, durch die die Templatevariablen ersetzt werden
	 * @return boolean
	 * @since 1.0
	 */
	public function assign($tpl_vars, $replace = NULL)
	{
		if(!empty($tpl_vars))
		{
			$this->tpl_vars[$tpl_vars]	= $replace;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Datarows und Datarowvariablen werden definiert bzw. deklariert.
	 *
	 * @param (string) 	$name		Name der Datarow im Template
	 * @param (array) 	$sql		SQL-Query
	 * @param (array)	$vars		Zu ersetzende Datarowvariablen (key) und Werte, durch die Datarowvariablen ersetzt werden (value) 
	 * 
	 * @since 1.0
	 */	
	public function assignDatarow($name, $sql, $vars)
	{
		foreach($vars as $var => $value)
		{
			$this->datarow_vars[$name][$var] 	= $value;
		}
				
		$this->datarow_ressource[$name]		= $sql;
	}
	
	
	
	/**
	 * Subrow und Subrowvariablen werden definiert bzw. deklariert.
	 *
	 * @param (string) 	$name		Name der Subrow im Template
	 * @param (string) 	$parent		Name der Datarow (Parent) im Template
	 * @param (array) 	$sql		SQL-Query
	 * @param (array)	$vars		Zu ersetzende Datarowvariablen (key) und Werte, durch die Datarowvariablen ersetzt werden (value)
	 *
	 * @since 1.0
	 */
	public function assignSubrow($name, $parent, $sql, $vars)
	{
		foreach($vars as $var => $value)
		{
			$this->subrow_vars[$parent][$name][$var] 	= $value;
		}
	
		$this->subrows[$parent][$name]['sql']	= $sql;
	}
	
	
	/**
	 * Verändert den Inhalt der Datarow Variablen mithilfe der übergeben Funktion.
	 *
	 * @param (string) $var			Zu bearbeitende Variable
	 * @param (string) $function	Funktion mit der die Variable bearbeitet wird
	 *
	 * @since 1.0
	 */
	public function assignFunction($var, $function)
	{
		$this->assigned_functions["{".$var."}"]	= $function;
	}
	
	
	/**
	 * Legt den Pfad in dem Templates gespeichert sind fest.
	 *
	 * @param (string) $dir		Verzeichnis
	 * @return boolean
	 * @since 1.0
	 */	
	public function setTemplateDir($dir)
	{
		if(!empty($dir))
		{
			if(substr($dir, -1, 1) == "/")
			{
				$this->template_dir	= $dir;
			}
			else
			{
				$this->template_dir = $dir . "/";
			}
			
			return true;
		}
		else
		{
			$this->notify->addMessage("Templateverzeichnis wurde nicht angegeben!", "error");
			return false;
		}
	}
	
	
	/**
	 * Fügt ein Template hinzu.
	 *
	 * @param (string) $template	Name der Template-Datei
	 * @return boolean
	 * @since 1.0
	 */
	public function addTemplate($template)
	{
		if(!empty($template))
		{
			$this->templates	= $template;
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Gibt ein fertiges Template aus.
	 *
	 * @since 1.0
	 */	
	public function display()
	{
		$this->openTemplate();
		$this->compileTemplate();
		$this->notify->printMessage();
		echo $this->output;
	}
}
?>
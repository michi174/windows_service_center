<?php
namespace wsc\template;
use wsc\systemnotification as system;
use wsc\bbcode as bbCode;
use wsc\database as database; 

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
	 * Variablen, die durch Funktionen ersetzt werden mï¿½ssen
	 *
	 * @var (array) $assigned_functions
	 * @since 1.0
	 */
	protected $assigned_functions		= array();

	
	
	/**
	 * SQL-Befehl fÃ¼r Datarows
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
	 * Subrows fï¿½r Datarows
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
		$this->notify	= new system\SystemNotification("warning");
		$this->bbcode	= new bbCode\BBCode();
		
		if(!is_null($db))
		{
			if($db instanceof database\Database)
			{
				$this->db = $db;
			}
			else
			{
				$this->notify->addMessage("Das Datenbankobjekt konnte nicht initialisiert werden!", "error");
			}
		}
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
		
		$this->output	= $this->parseIf($this->output);
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
		$this->output	= $this->replaceIncludes($this->output);
		$this->replaceForeach();
		$this->multiDatarow();
		$this->output	= $this->parseArrayKeys($this->output);
	}
	
	/**
	 * Ersetzt INCLUDE Tags im Template
	 *
	 * Die Methode ließt die angeforderten Templates ein. Die Funktion muss als erstes angewandt
	 * werden, da noch unkompilierte IF bzw. FOREACH Blöcke im includierten Template sein könnten,
	 * die sonst nicht mehr kompiliert werden.
	 * 
	 * Die im Template enthaltenen variablen müssen selbstverständlich im Aufrufendem Programm deklariert
	 * worden sein, da ansonsten eine Fehlermeldung ausgegeben wird.
	 *
	 * @param (string)	$section		Teil der auf Include Blocks abgesucht werden muss.
	 * @return (string)	$section		Fertig kompilierter Abschnitt.
	 *
	 * @since 1.0
	 */
	
	protected function replaceIncludes(&$section)
	{
		$matches	= array();
		$pattern	= '#\{include\=\"{1}(.+)\"\}#';
		
		while(preg_match($pattern, $section, $matches))
		{
			$include_file	= $matches[1];
			//Exisitiert die Datei?
			
			if(file_exists($include_file))
			{
				//Inhalt laden
				$file_content	= file_get_contents($include_file);
				
				if(preg_match($pattern, $file_content, $matches))
				{
					$this->replaceIncludes($file_content);
				}
				

				//Jetzt den Platzhalter ersetzen
				$search		= '{include="'.$include_file.'"}';
				$replace	= $file_content;
				
				$section	= str_replace($search, $replace, $section);
			}
			else
			{
				$this->notify->addMessage("Include Template (&quot;".$include_file."&quot;) wurde nicht gefunden!", "information");
				$section	= str_replace('{include="'.$include_file.'"}', "INCLUDE_ERROR", $section);
			}
			
		}
		return $section;
	}
	
	
	/**
	 * Ersetzt IF-ELSE Tags im Template
     * 
     * Diese Methode erwartet, dass bereits alle Templatevariablen ( '{var}' )in der Condition
     * ersetzt wurden. Dies wird benötigt, da nicht alle Variablen im selben Array gespeichert
     * sind und damit das IF-ELSE Parsing auch in den Data- bzw. Subrow Abschnitten funktioniert.
	 *
	 * @param (string)	$section		Teil der auf If-Else Blocks abgesucht werden muss.
	 * @return (string)	$section		Fertig kompilierter Abschnitt.
	 *
	 * @since 1.0
	 */
	protected function parseIf($section)
	{
		$matches	= array();
		$pattern	= '#\{if\=\"{1}(.+)\"\}(.*)(\{else\}(.*))?\{\/if\}#ismU'; #Muster: {if a > b}a ist mehr als b{else}b ist mehr als a{/if} - Else Block ist optional
		
		while(preg_match($pattern, $section, $matches) == true)
		{
			$condition		= $matches[1];
			$if_block		= $matches[2];
			
			//Wenn ein else Block vorhanden ist, wird er der Variable zugewiesen sonst null.
			$else_block		= (isset($matches[3]) && substr($matches[3],0,6) == $this->delimeter_left."else".$this->delimeter_right) ? $matches[4] : null;
			$output_block	= null;
			
			
			//ï¿½berprï¿½fen, ob noch nicht ersetzte Variablen vorhanden sind.
			$condition_pattern	= '#(\{.+\})#ismU';
			$condition_matches	= array();
			
			if(preg_match($condition_pattern, $condition, $condition_matches) == true)
			{
				$this->notify->addMessage("<strong>Syntaxfehler:</strong> Die Templatevariable &rsquo;".$condition_matches[1]."&rsquo; wurde nicht deklariert!<br /><br />Der IF-ELSE Block kann nicht richtig ausgef&uuml;hrt werden.", "error");
				$output_block	= "IF_ELSE_ERROR";
			}
			else
			{
				$compiled_if	= '
								if('.$condition.')
								{
									$output_block	= "'.addslashes($if_block).'";
								}
								else
								{
									$output_block	= "'.addslashes($else_block).'";
								}';
				eval($compiled_if);
			}
			
			//If-Else Tags im Template durch den Output Block ersetzen.
			if(!is_null($else_block))
			{
				$search_if 	= ''.$this->delimeter_left.'if="'.$condition.'"'.$this->delimeter_right.$if_block.$this->delimeter_left.'else'.$this->delimeter_right.$else_block.$this->delimeter_left.'/if'.$this->delimeter_right;
			}
			else 
			{
				$search_if	= ''.$this->delimeter_left.'if="'.$condition.'"'.$this->delimeter_right.$if_block.$this->delimeter_left.'/if'.$this->delimeter_right;
			}
			
			$section	= str_replace($search_if, $output_block, $section);
		}
		return $section;
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
			
			
			//Wenn der Template Array ein PHP Array ist
			if(array_key_exists($array_keys_array, $this->tpl_vars))
			{
				//Wenn der Template ArrayKey ein PHP ArrayKey ist
				if(array_key_exists($array_keys_key, $this->tpl_vars[$array_keys_array]))
				{
					$array_keys_search	= $this->delimeter_left . $array_keys_array . ":" . $array_keys_key . $this->delimeter_right;
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
				$this->notify->addMessage("<strong>Fehler:</strong> &rsquo;" . $array_keys_array . "&rsquo; ist kein Array.", "error");
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
				$datarow_db_num_rows	= $datarow_db_ressource->num_rows;
				
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
								//SQL-Befehl fï¿½r Subrow kompilieren.
								foreach($this->datarow_vars[$datarow_name] as $datarow_var => $datarow_var_inhalt)
								{		
									$subrow_pre_sql			= $this->subrows[$datarow_name][$subrow_name]['sql'];
									$subrow_compiled_sql	= str_replace("{".$datarow_name.".".$datarow_var."}", $datarow_dataset[$datarow_var_inhalt], $subrow_pre_sql);
								}
								//Datensï¿½tze der Subrow auslesen
								$subrow_db_ressource	= $this->db->query($subrow_compiled_sql) or die($this->db->error);
								$subrow_db_num_rows		= $subrow_db_ressource->num_rows;
								
								if($subrow_db_num_rows > 0)
								{
									while(($subrow_dataset	= $subrow_db_ressource->fetch_assoc()) == true)
									{
										//Jetzt mï¿½ssen die Subrow Template Variablen durch die Werte ersetzt werden...
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
					$this->output				= preg_replace($datarow_pattern_all, "keine Eintr&auml;ge gefunden.", $this->output);
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
			$compiled_code	= "FUNCTION_COMPILING_FAILED";
			
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
	 * Verï¿½ndert den Inhalt der Datarow Variablen mithilfe der ï¿½bergeben Funktion.
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
	 * Fï¿½gt ein Template hinzu.
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

<?php
namespace wsc\systemnotification;
use wsc\template as template;
/**
 * SystemNotifications (2013 - 03 - 01)
 *
 * Klasse um Benachrichtigungen auf dem Bildschirm auszugeben.
 *
 * @author 		michi_000
 * @name 		SystemNotifications
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */
class SystemNotification 
{
	/**
	 * @var (string) Standardtyp
	 * @since 1.0
	 */
	protected $default_type		= NULL;
	
	
	/**
	 * @var (string) Benachrichtigungstyp
	 * @since 1.0
	 */
	protected $types		= array();
	
	
	/**
	 * @var (array) Meldungen
	 * @since 1.0
	 */
	protected $message	= array();
	
	
	/**
	 * @var (string) Überschrift
	 * @since 1.0
	 */
	protected $title	= array();
	
	
	/**
	 * @var array() Schaltflächen
	 * @since 1.0
	 */
	protected $buttons	= array();

	
	/**
	 * @var array() Erlaubte Nachrichtentypen
	 * @since 1.0
	 */
	protected $allowed_types	= array("error", "warning", "information", "success");
	
	
	/**
	 * Konstruktor
	 *
	 * Standardtyp aller Fehlermeldungen wird zugewiesen. (Falls Methode setNotificationType() nicht aufgerufen wird.)
	 *
	 * @param (string) Benachrichtigungstyp
	 * @since 1.0
	 */
	public function __construct($type = "information")
	{
		$this->default_type	= $type;
	}
	
	
	/**
	 * Fügt eine Meldung hinzu.
	 *
	 * @param (string) Nachricht
	 * 
	 * @since 1.0
	 */
	public function addMessage($message, $type = NULL)
	{		
		if(is_null($type))
		{
			$type	= $this->default_type;
		}
		
		if(!in_array($type, $this->types))
		{
			if(in_array($type, $this->allowed_types))
			{
				$this->types[]	= $type;
			}
			else
			{
				var_dump($this->allowed_types);
				echo "Systemmeldungstyp nicht erlaubt! $type";
			}
		}
		
		$this->message[$type][]	= $message;
	}
	
	
	/**
	 * Fügt einen Titel hinzu.
	 *
	 * @param (string) Titel
	 *
	 * @since 1.0
	 */
	public function addTitle($title)
	{
		$this->title	= $title;
	}
	
	/**
	 * Fügt eine Schaltfläche hinzu.
	 *
	 * @param (string) Linkziel
	 * @param (string) Linkbeschreibung
	 * @param (string) Ausrichtung der Schaltfläche
	 * 
	 * @since 1.0
	 */
	public function addButton($link, $description)
	{
		if (!empty($description))
		{
			$this->buttons["" . $description . ""] = $link;
		}
		else
		{
			$this->buttons["[description_failed]"] = $link;
		}
	}
	
	
	/**
	 * Legt den Typ der Benachrichtung fest.
	 *
	 * @param (string) Typ
	 *
	 * @since 1.0
	 */
	public function setNotificationType($type)
	{
		if(in_array($type, $this->allowed_types))
		{
			$this->type	= $type;
		}		
	}
	
	
	/**
	 * Gibt den Typ der Benachrichtung zurück.
	 *
	 * @param (string) Typ
	 *
	 * @since 1.0
	 */
	public function getNotificationType()
	{
		return $this->types;
	}

	
	/**
	 * Gibt die Benachrichtung über ein Template aus.
	 *
	 * @since 1.0
	 */
	function printMessage()
	{
		foreach($this->types as $type)
		{
			$template_dir	= "template/win8_style/templates";
			$template_name	= "systemnotification.html";
			
			$notification	= new template\template();
			$notification->setTemplateDir($template_dir);
			$notification->addTemplate($template_name);
			
			$notification->assign("type", $type);
			$notification->assign("messages", $this->message[$type]);
			
			if(is_array($this->buttons))
			{
				$notification->assign("buttons", $this->buttons);
			}
			
			if(!empty($this->message[$type]))
			{
				$notification->display();
			}
		}
	}
}

?>
<?php
namespace wsc\login;

/**
 * Login (2013 - 02 - 09)
 * 
 * Klasse um einen Benutzer einzuloggen und seine Berechtigungen festzustellen.
 * 
 * @author 		michi_000
 * @name 		Login
 * @version		1.1
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 * @access		final
 */ 
final class Login 
{
	/**
	 * @var string Benutzerpasswort oder Session ID
	 * @since 1.0
	 */
	private $auth			= NULL;
	
	
	/**
	 * @var string Benutzername oder E-Mailadresse 
	 * @since 1.0
	 */
	private $account		= NULL;

	/**
	 * @var (bool) Cookie
	 * @since 1.1
	 */
	private $cookie			= NULL;
	
	

	
	/**
	 * Datenbankobjekt
	 * 
	 * @var object $db
	 * @since 1.0
	 */
	private $db		= NULL;
	
	
	/**
	 * @var array Benutzerdatenspeicher
	 * @since 1.0
	 */
	public $userdata		= array();
	
	public $usergroups;
	public $userroles;
	public $userpermissions;
	
	
	/**
	 * Konstruktor
	 *
	 * Accountname und Passwort wird den jeweiligen Eigenschaften zugewiesen.
	 *
	 * @param (string) Accountname (Benutzername oder E-Mailadresse)
	 * @param (string) Passwort
	 * @param (bool) Cookie
	 * @since 1.0
	 */
	public function __construct($database)
	{
		
	}
	
	public function recognizeUser()
	{
		
	}
	
	
	/**
	 * Erzeugt eine neue Sitzung für den Benutzer und speichert seine Daten in der Session, um nicht immer wieder 
	 * neu aus der Datenbank geladen werden zu müssen.
	 *
	 * @since 1.3
	 */
	public function newSession()
	{
		
	}
	/**
	 * Loggt den Benutzer ein, wenn alle Sicherheitschecks bestanden wurden und schreibt einen Eintrag ins Login-Protokoll
	 *
	 * @since 1.0
	 */
	public function loginUser($account, $auth, $cookie = false)
	{
		
	}
	
	private function guest()
	{

	}
	public function logoutUser()
	{
		
	}
	
	public function getUserGroups()
	{

	}
	
	public function getUserRoles()
	{

	}
	
	/**
	 * Alle Berechtiungen des Benutzers sammeln.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	public function getUserPermissions()
	{
		
	}
	
	/**
	 * Gibt die Berechtigung einer Rolle inklusive der Elternrollen zurück.
	 *
	 * @return (array) Berechtigungen
	 * @since 1.0
	 */
	private function getRolePermission($role)
	{
		
	}
	
	/**
	 * Liefert Benutzerdaten an das Hauptprogramm.
	 *
	 * @return (array) Benutzerdaten oder (bool) false
	 * @since 1.0
	 */
	public static function getUserData($user_id)
	{

	}
	
	
	private function checkForgottenData()
	{
		
	}
	
	public function sendMail()
	{
		
	}
}
?>
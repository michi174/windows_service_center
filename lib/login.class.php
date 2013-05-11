<?php
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
	 * @var string Benutzerpasswort
	 * @since 1.0
	 */
	private $password		= NULL;
	
	
	/**
	 * @var string Benutzername oder E-Mailadresse 
	 * @since 1.0
	 */
	private $account		= NULL;

	/**
	 * @var (bool) Cookie
	 * @since 1.1
	 */
	private $cookie		= NULL;
	
	
	/**
	 * @var array Benutzerdatenspeicher
	 * @since 1.0
	 */
	private $userdata		= array();
	
	/**
	 * Datenbankobjekt
	 * 
	 * @var object $db
	 * @since 1.0
	 */
	private $db		= NULL;
	
	
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
	public function __construct($database, $account, $password, $cookie = false)
	{
		$this->db		= $database;
		$this->account	= $account;
		$this->password	= $password;
		$this->cookie	= (!is_null($cookie)) ? true : false;
	}
	
	/**
	 * Es wird festgestellt, ob der Account über ein Cookie eingeloggt werden kann.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.1
	 */
	protected function checkCookie()
	{
		if(isset($_COOKIE['login']))
		{
			$sql	= "SELECT * FROM userdata WHERE session_id = '" . $_COOKIE['login'] . "'";
			$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);
			$num	= $res->num_rows;
			
			if($num == 1)
			{
				$row	= $res->fetch_assoc();
				$this->userdata = $row;
				return true;
			}
			elseif($num == 0)
			{
				Logout::logoutUser();
				throw new LoginErrorException("Login fehlgeschlagen. Cookie fehlerhaft.");
			}
			elseif($num > 1)
			{
				Logout::logoutUser();
				throw new LoginErrorException("Login fehlgeschlagen - Sicherheitsrisiko endeckt! Bitte manuell einloggen.");
			}
			
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Es wird festgestellt, ob ein Account mit den übermittelten Benutzerdaten existiert.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	private function checkUserData()
	{

		$sql	= "	SELECT
					*
				FROM
					userdata
				WHERE
					(username = '".$this->account."' OR email = '".$this->account."') && password = '".md5($this->password)."'
				LIMIT
					1";
		
		$res	= mysql_query($sql) or die(mysql_error());
		$row	= mysql_fetch_assoc($res);
		$num	= mysql_num_rows($res);
		
		if($num === 1)
		{
			$this->userdata	= $row;
			return true;
		}
		else
		{						
			throw new LoginErrorException("Anmeldeinformationen fehlerhaft! Bitte die Eingaben &uuml;berpr&uuml;fen.");
		}
		

	}

	
	/**
	 * Es wird festgestellt, ob der Account von einem Administrator gesperrt wurde.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	private function checkAccountBans()
	{
		$sql	= "	SELECT
						*
					FROM
						account_lock
					WHERE 
						userid = " . $this->userdata['id'];
		
		$res	= mysql_query($sql) or die(mysql_error());;
		$row	= mysql_fetch_assoc($res);
		$num	= mysql_num_rows($res);
				
		if($num === 0)
		{
			return true;
		}
		else
		{
			$admin			= self::getUserData($row['admin']);
			
			throw new LoginErrorException("Dieses Benutzerkonto wurde von Admin: <strong>" . $admin['username'] . "</strong> am <strong>" . date("d.m.Y", $row['date']) . "
								</strong> um <strong>" . date("H:i", $row['date']) . "</strong> gesperrt.<br /><br /><strong>Grund:</strong> " . $row['reason'], 20);
		}
	}
	
	
	/**
	 * Es wird festgestellt, ob der Account aktiviert wurde.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	private function checkAccountActivated()
	{
		$sql	= "	SELECT
						*
					FROM
						account_activation
					WHERE
						userid	= " . $this->userdata['id'];
		
		$res	= mysql_query($sql) or die(mysql_error());;
		$row	= mysql_fetch_assoc($res);
		if($row['active'] == 1)
		{
			return true;
		}
		else
		{
			throw new LoginErrorException("Dieses Benutzerkonto wurde nicht aktiviert. Bitte Maileingang &uuml;berpr&uuml;fen und Konto aktivieren.");
		}

	}

	
	/**
	 * Es wird ein Eintrag in das Login-Protokoll erstellt.
	 *
	 * @since 1.0
	 */
	private function writeLoginProtocol()
	{
		$sql	= "	INSERT login_protocol
										(
											userid, 
											ip, 
											time
										)
					VALUES
						(
							'" . $this->userdata['id'] . "',
							'" . $_SERVER['REMOTE_ADDR'] . "',
							'" . time() . "'
						)";
		$res	= mysql_query($sql) or die(mysql_error());
	}
	
	/**
	 * Erzeugt ein Login-Cookie
	 *
	 * @since 1.1
	 */
	private function setLoginCookie()
	{
		$cookie	= setcookie("login", session_id(), time()+(60*60*24*30));
		$sql	= "UPDATE userdata SET session_id = '". session_id() ."' WHERE id = ". $this->userdata['id'];
		$res	= mysql_query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . mysql_error());
	}
	
	
	/**
	 * Loggt den Benutzer bei ein, wenn alle Sicherheitschecks bestanden wurden und schreibt einen Eintrag ins Login-Protokoll
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */	
	public function loginUser()
	{
		if($this->checkCookie() === false)
		{
			$this->checkUserData();
		}
		$this->checkAccountBans();
		$this->checkAccountActivated();
		
		if(empty($this->errors))
		{
			$_SESSION['loggedIn']	= true;
			$_SESSION['userid']		= $this->userdata['id'];
			
			if($this->cookie === true)
			{
				$this->setLoginCookie();
			}
			
			$this->writeLoginProtocol();
			
			return true;
		}
		else 
		{
			throw new LoginErrorException("Unbekannter Fehler bei Benutzeranmeldung");
		}
	}
	

	/**
	 * Liefert Fehler an das Hauptprogramm.
	 *
	 * @return (array) Fehler oder (bool) false
	 * @since 1.0
	 */
	public function getLoginErrors()
	{
		if(array_key_exists(0, $this->errors))
		{
			return $this->errors;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Liefert Benutzerdaten an das Hauptprogramm.
	 *
	 * @return (array) Benutzerdaten oder (bool) false
	 * @since 1.0
	 */
	public static function getUserData($user_id)
	{
		$sql	= "	SELECT
						*
					FROM
						userdata
					WHERE
						id	= " . $user_id;
		
		$res	= mysql_query($sql) or die(mysql_error());
		$row	= mysql_fetch_assoc($res);
		$num	= mysql_num_rows($res);
		
		if($num === 1)
		{
			return $row;
		}
		else 
		{
			return false;
		}
	}
	
	
	/**
	 * Liefert Benutzerberechtigungen an das Hauptprogramm.
	 *
	 * @return (array) Berechtigungen
	 * @since 1.0
	 */
	public static function getUserPermission($user_id)
	{
		$sql	= "	SELECT
						*
					FROM
						user_permission
					WHERE
						userid = " . $user_id;
	
		$res	= mysql_query($sql) or die(mysql_error());
		$row	= mysql_fetch_array($res);
		
		return $row;
	}

	private function checkForgottenData()
	{
		
	}
	
	public function sendMail()
	{
		
	}	
}
?>
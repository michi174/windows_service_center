<?php

namespace wsc\auth;
use wsc\user\User;

/**
 *
 * Auth (2013 - 12 - 01)
 * 
 * Klasse um Benutzer zu authentifizieren.
 * 
 * @author 		Michael Strasser
 * @name 		Auth
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 *        
 */
class Auth 
{
	private $db			= NULL;
	private $auth		= NULL;
	private $account	= NULL;
	private $cookie		= NULL;
	private $userid		= NULL;
	
	public function __construct($database)
	{
		if(!is_null($database))
		{
			$this->db		= $database;
		}
		else
		{
			die("Es wurde kein gültiges Datenbankobjekt uebergeben.");
		}
		
		$this->recognizeUser();
	}
	
	public function recognizeUser()
	{
		//Ein neuer Benutzer besucht die Website
		if(!isset($_SESSION['recognizedUser']))
		{
			//Kann er über ein Cookie eingeloggt werden?
			if($this->checkCookie() == false)
			{
				//Nein, dann muss es ein Gast sein.
				$this->guest();
			}
		}
		//...wir haben den Benutzer bereits gekannt.
		else
		{
			//$this->getUser();
		}
	}
	
	public function login($account, $auth, $cookie = false)
	{
		$this->account	= $account;
		$this->auth		= $auth;
		$this->cookie	= $cookie;
		
		$this->checkUserData();
		$this->checkAccountBans();
		$this->checkAccountActivated();
		
		$_SESSION['loggedIn']	= true;
		$_SESSION['userid']		= $this->userid;
		
		if($this->cookie === true)
		{
			$this->setLoginCookie();
		}
		
		$this->newUser();
		$this->writeLoginProtocol();
	}
	
	public function logout()
	{
		session_unset();
		session_destroy();
		setcookie("login", "", time()-1);
		unset($_COOKIE['login']);
		
		$this->recognizeUser();
	}
	
	public function getUser()
	{
		return unserialize($_SESSION['user']);
	}
	
	public function isAuthenticated()
	{
		if($_SESSION['recognizedUser'] === true)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function isLoggedIn()
	{
		if($_SESSION['loggedIn'] === true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	private function checkCookie()
	{
	
		if(isset($_COOKIE['login']))
		{
			$sql	= "SELECT * FROM userdata WHERE session_id = '" . $_COOKIE['login'] . "'";
			$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . ___FILE___ . ":" . ___LINE___ . "<br /><br />" . $this->db->error);
			$num	= $res->num_rows;
				
			if($num == 1)
			{
				$row	= $res->fetch_assoc();
				$this->login($row['username'], $row['session_id']);
	
				return true;
			}
			elseif($num == 0)
			{
				$this->logout();
				//throw new Exception\LoginErrorException("Login fehlgeschlagen. Cookie fehlerhaft.");
			}
			elseif($num > 1)
			{
				$this->logout();
				//throw new Exception\LoginErrorException("Login fehlgeschlagen - Sicherheitsrisiko endeckt! Bitte manuell einloggen.");
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
		if(!empty($this->account) && !empty($this->auth))
		{
			$sql	= "
					SELECT
						*
					FROM
						userdata
					WHERE
						(username = '".$this->account."' OR email = '".$this->account."') AND (password = '". md5($this->auth)."' OR session_id = '" . $this->auth . "')
					LIMIT
						1";
				
			$res	= $this->db->query($sql) or die($this->db->error);
			$row	= $res->fetch_assoc();
			$num	= $res->num_rows;
	
			if($num == 1)
			{
				$this->userid	= $row['id'];
				return true;
			}
			else
			{
				//throw new Exception\LoginErrorException("Anmeldeinformationen fehlerhaft! Bitte die Eingaben &uuml;berpr&uuml;fen.");
	
			}
		}
		else
		{
			//throw new Exception\LoginErrorException("Anmeldung fehlgeschlagen. Keine Accountinformationen &uuml;bermittelt.");
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
						userid = " . $this->userid;
	
		$res	= mysql_query($sql) or die(mysql_error());;
		$row	= mysql_fetch_assoc($res);
		$num	= mysql_num_rows($res);
	
		if($num === 0)
		{
			return true;
		}
		else
		{
			//$admin			= self::getUserData($row['admin']);
				
			//throw new Exception\LoginErrorException("Dieses Benutzerkonto wurde von Admin: <strong>" . $admin['username'] . "</strong> am <strong>" . date("d.m.Y", $row['date']) . "
								//</strong> um <strong>" . date("H:i", $row['date']) . "</strong> gesperrt.<br /><br /><strong>Grund:</strong> " . $row['reason'], 20);
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
						userid	= " . $this->userid;
	
		$res	= mysql_query($sql) or die(mysql_error());;
		$row	= mysql_fetch_assoc($res);
	
		if($row['active'] == 1)
		{
			return true;
		}
		else
		{
			//throw new Exception\LoginErrorException("Dieses Benutzerkonto wurde nicht aktiviert. Bitte Maileingang &uuml;berpr&uuml;fen und Konto aktivieren.");
		}
	
	}
	
	/**
	 * Erzeugt ein Login-Cookie
	 *
	 * @since 1.1
	 */
	private function setLoginCookie()
	{
		$cookie	= setcookie("login", session_id(), time()+(60*60*24*30));
		$sql	= "UPDATE userdata SET session_id = '". session_id() ."' WHERE id = ". $this->userid;
		$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . ___FILE___ . ":" . ___LINE___ . "<br /><br />" . $this->db->error);
	
	}
	
	private function guest()
	{
		$sql	= "SELECT * FROM userdata WHERE username = 'guest'";
		$res	= $this->db->query($sql) or die($this->db->error);
		$row	= $res->fetch_assoc();
		$num	= $res->num_rows;
	
		if($num == 1)
		{
			$this->userid		= $row['id'];
			$this->newUser();
		}
		else
		{
			die("Es ist kein Gastkonto vorhanden.");
		}
	}
	
	private function newUser()
	{
		$_SESSION['recognizedUser'] = true;
		$_SESSION['user']	= serialize(new User($this->userid));
	
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
							'" . $this->userid . "',
							'" . $_SERVER['REMOTE_ADDR'] . "',
							'" . time() . "'
						)";
		$res	= mysql_query($sql) or die(mysql_error());
	}

}

?>
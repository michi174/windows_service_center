<?php
namespace wsc\login;
use wsc\login\exception as Exception;
use wsc\logout as Logout;
use wsc\login\exception\LoginErrorException;

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
	public function __construct($database, $account=null, $auth=null, $cookie = false)
	{
		if(!is_null($database))
		{
			$this->db		= $database;
		}
		else
		{
			throw new Exception\LoginErrorException("Es wurde kein gültiges Datenbankobjekt uebergeben.");
		}
		
		$this->recognizeUser();
	}
	
	public function recognizeUser()
	{
		if(!isset($_SESSION['loggedIn']) && !isset($_SESSION['recognizedUser']))
		{
			if($this->checkCookie() == false)
			{
				$this->guest();
			}
			$_SESSION['recognizedUser'] = true;
		}
		elseif(!isset($_SESSION['loggedIn']))
		{
			$this->guest();
		}
		else
		{
			$this->userdata	= self::getUserData($_SESSION['userid']);
		}
	}
	/**
	 * Loggt den Benutzer bei ein, wenn alle Sicherheitschecks bestanden wurden und schreibt einen Eintrag ins Login-Protokoll
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	public function loginUser($account, $auth, $cookie = false)
	{
	
	
		$this->account	= $account;
		$this->auth		= $auth;
		$this->cookie	= $cookie;
	
		$this->checkUserData();
		$this->checkAccountBans();
		$this->checkAccountActivated();
	
		$_SESSION['loggedIn']	= true;
		$_SESSION['userid']		= $this->userdata['id'];
	
		if($this->cookie === true)
		{
			$this->setLoginCookie();
		}
	
		$this->writeLoginProtocol();
	}
	
	private function guest()
	{
		$sql	= "SELECT * FROM userdata WHERE username = 'guest'";
		$res	= $this->db->query($sql) or die($this->db->error);
		$row	= $res->fetch_assoc();
		$num	= $res->num_rows;
	
		if($num == 1)
		{
			$this->userdata	= $row;
		}
		else
		{
			$this->userdata['username']		= "guest";
			$this->userdata['firstname']	= "Gast";
			$this->userdata['id']			= "0";
		}
	}
	public function logoutUser()
	{
		session_unset();
		session_destroy();
		setcookie("login", "", time()-1);
		unset($_COOKIE['login']);
		
		$this->recognizeUser();
	}
	
	/**
	 * Es wird festgestellt, ob der Account über ein Cookie eingeloggt werden kann.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.1
	 */
	private function checkCookie()
	{
		
		if(isset($_COOKIE['login']))
		{
			$sql	= "SELECT * FROM userdata WHERE session_id = '" . $_COOKIE['login'] . "'";
			$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);
			$num	= $res->num_rows;
			
			if($num == 1)
			{
				$row	= $res->fetch_assoc();
				$this->loginUser($row['username'], $row['session_id']);
				
				return true;
			}
			elseif($num == 0)
			{
				$this->logoutUser();
				throw new Exception\LoginErrorException("Login fehlgeschlagen. Cookie fehlerhaft.");
			}
			elseif($num > 1)
			{
				$this->logoutUser();
				throw new Exception\LoginErrorException("Login fehlgeschlagen - Sicherheitsrisiko endeckt! Bitte manuell einloggen.");
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
				$this->userdata	= $row;
				return true;
			}
			else
			{		
				throw new Exception\LoginErrorException("Anmeldeinformationen fehlerhaft! Bitte die Eingaben &uuml;berpr&uuml;fen.");
				
			}
		}
		else 
		{
			throw new Exception\LoginErrorException("Anmeldung fehlgeschlagen. Keine Accountinformationen &uuml;bermittelt.");
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
			
			throw new Exception\LoginErrorException("Dieses Benutzerkonto wurde von Admin: <strong>" . $admin['username'] . "</strong> am <strong>" . date("d.m.Y", $row['date']) . "
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
			throw new Exception\LoginErrorException("Dieses Benutzerkonto wurde nicht aktiviert. Bitte Maileingang &uuml;berpr&uuml;fen und Konto aktivieren.");
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
		$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);

	}
	
	public function getUserGroups($userId)
	{
		$groups	= array();
		
		$sql	= "SELECT * FROM user_group_members WHERE userdataUserId = " . $userId;
		$res	= $this->db->query($sql) or die($this->db->error);
		
		while($row = $res->fetch_assoc())
		{
			$groups[]	= $row['userGroupsId'];
		}
		
		return $groups;
	}
	
	public function getUserRoles($userId)
	{
		$roles		= array();
		$usrgrps	= $this->getUserGroups($userId);
		
		
		//Rollen, die durch Gruppe auf den User angewandt werden können.
		foreach($usrgrps as $grp_id)
		{
			$sql_grp_rls	= "SELECT * FROM acl_role_members WHERE referenceId = " . $grp_id . " AND reference = 'grp'";
			$res_grp_rls	= $this->db->query($sql_grp_rls) or die($this->db->error);				
			
			while($row_grp_rls = $res_grp_rls->fetch_array())
			{
				array_push($roles, $row_grp_rls['aclRolesRoleId']);
			}
		}
		//Rollen, die direkt auf den User referenzieren.
		$sql_usr_rls	= "SELECT * FROM acl_role_members WHERE referenceId = " . $userId . " AND reference = 'usr'";
		$res_usr_rls	= $this->db->query($sql_usr_rls) or die($this->db->error);
		
		while($row_usr_rls = $res_usr_rls->fetch_array())
		{
			if(!in_array($row_usr_rls['aclRolesRoleId'], $roles))
			{
				array_push($roles, $row_usr_rls['aclRolesRoleId']);
			}
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
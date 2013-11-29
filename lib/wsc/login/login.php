<?php
namespace wsc\login;
use wsc\login\exception as Exception;

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
			$that	= unserialize($_SESSION['user']);
			
			$this->userdata			= $that->userdata;
			$this->usergroups		= $that->usergroups;
			$this->userroles		= $that->userroles;
			$this->userpermissions	= $that->userpermissions;
		}
	}
	
	
	/**
	 * Erzeugt eine neue Sitzung für den Benutzer und speichert seine Daten in der Session, um nicht immer wieder 
	 * neu aus der Datenbank geladen werden zu müssen.
	 *
	 * @since 1.3
	 */
	public function newSession()
	{
		$_SESSION['recognizedUser'] = true;
		$this->userdata	= self::getUserData($this->userdata['id']);
		$this->getUserGroups();
		$this->getUserRoles();
		$this->getUserPermissions();
		
		$_SESSION['user']	= serialize($this);
		
	}
	/**
	 * Loggt den Benutzer ein, wenn alle Sicherheitschecks bestanden wurden und schreibt einen Eintrag ins Login-Protokoll
	 *
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
		
		$this->newSession();
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
			$this->newSession();
		}
		else
		{
			throw new Exception\LoginErrorException("Es ist kein Gastkonto vorhanden.");
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
			$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . ___FILE___ . ":" . ___LINE___ . "<br /><br />" . $this->db->error);
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
		$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: " . ___FILE___ . ":" . ___LINE___ . "<br /><br />" . $this->db->error);

	}
	
	public function getUserGroups()
	{
		$groups	= array();
		
		$sql	= "SELECT * FROM user_group_members WHERE userdataUserId = " . $this->userdata['id'];
		$res	= $this->db->query($sql)or die("SQL-Fehler in Datei: ". ___FILE___ . ":" . ___LINE___ . "<br />" . $this->db->error);
		
		while(($row = $res->fetch_assoc()) != false)
		{
			$groups[]	= $row['userGroupsId'];
		}
		
		$this->usergroups = $groups;
	}
	
	public function getUserRoles()
	{
		$roles		= array();
		$usrgrps	= $this->usergroups;
		
		
		//Rollen, die durch Gruppe auf den User angewandt werden können.
		if(!empty($usrgrps))
		{
			foreach($usrgrps as $grp_id)
			{
				$sql_grp_rls	= "	SELECT 
										* 
									FROM 
										acl_role_members 
									WHERE 
										referenceId = " . $grp_id . " AND aclReferenceType = '". $this->referenceType('group') ."'";
				
				$res_grp_rls	= $this->db->query($sql_grp_rls)or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);			
				
				while(($row_grp_rls = $res_grp_rls->fetch_array()) != false)
				{
					array_push($roles, $row_grp_rls['aclRolesId']);
				}
			}
		}
		//Rollen, die direkt auf den User referenzieren.
		$sql_usr_rls	= "	SELECT 
								* 
							FROM 
								acl_role_members 
							WHERE 
								referenceId = " . $this->userdata['id'] . " AND aclReferenceType = '". $this->referenceType('user') ."'";
		
		$res_usr_rls	= $this->db->query($sql_usr_rls)or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);
		
		while(($row_usr_rls = $res_usr_rls->fetch_array()) != false)
		{
			if(!in_array($row_usr_rls['aclRolesId'], $roles))
			{
				array_push($roles, $row_usr_rls['aclRolesId']);
			}
		}
		
		$this->userroles = $roles;
	}
	
	/**
	 * Alle Berechtiungen des Benutzers sammeln.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	public function getUserPermissions()
	{
		$permissions	= array();

		//Rechte, die direkt auf Benutzer referenzieren.
		$sql	= "	SELECT 
						* 
					FROM 
						acl_permissions 
					WHERE 
						aclReferenceType = '".$this->referenceType('user')."' 
						AND 
						aclReference = '".$this->userdata['id']."'";
		
		$res	= $this->db->query($sql)or die("SQL-Fehler in Datei: " .___FILE___. ":" . ___LINE___ . "<br /><br />" . $this->db->error);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			while(($row = $res->fetch_assoc()) != false)
			{
				$permissions[]	= $row;
			}
		}
		
		//Rechte, die auf Benutzergruppe referenzieren.
		
		if(count($this->usergroups) > 0)
		{
			foreach($this->usergroups as $usergroup)
			{
				$sql	= "	SELECT
								*
							FROM
								acl_permissions
							WHERE
								aclReferenceType = '".$this->referenceType('group')."'
								AND
								aclReference = '".$usergroup."'";
					
				$res	= $this->db->query($sql)or die("SQL-Fehler in Datei: ". ___FILE___ . ":" . ___LINE___ . "<br />" . $this->db->error);
				$num	= $res->num_rows;
					
				if($num > 0)
				{
					while(($row = $res->fetch_assoc()) != false)
					{
						if($this->searchPermission($row, $permissions) == false)
						{
							$permissions[]	= $row;
						}
					}
				}
			}
		}
		//Rechte, die auf Rolle referenzieren.
		if(!empty($this->userroles))
		{
			$rolepermissions_container	= array();
			
			foreach ($this->userroles as $userrole)
			{
				$rolepermissions_container[]	= $this->getRolePermission($userrole);
			}
			if(!empty($rolepermissions_container))
			{
				foreach($rolepermissions_container as $rolepermissions)
				{
					if(is_array($rolepermissions))
					{
						foreach($rolepermissions as $rolepermission)
						{
							if($this->searchPermission($rolepermission, $permissions) == false)
							{
								$permissions[]	= $rolepermission;
							}
						}
					}
				}
			}
		}
		$this->userpermissions	= $permissions;
	}
	
	/**
	 * Gibt die Berechtigung einer Rolle inklusive der Elternrollen zurück.
	 *
	 * @return (array) Berechtigungen
	 * @since 1.0
	 */
	private function getRolePermission($role)
	{
		$permissions	= array();
		
		$sql_role	= "SELECT * FROM acl_roles WHERE id = '".$role."'";
		$res_role	= $this->db->query($sql_role)or die("SQL-Fehler in Datei: ". ___FILE___ . ":" . ___LINE___ . "<br />" . $this->db->error);
		$num_role	= $res_role->num_rows;
		
		if($num_role > 0)
		{
			$row_role	= $res_role->fetch_assoc();
			$sql_perm	= "	SELECT
							*
						FROM
							acl_permissions
						WHERE
							aclReferenceType = '".$this->referenceType('role')."'
							AND
							aclReference = '".$row_role['id']."'";
				
			$res_perm	= $this->db->query($sql_perm)or die("SQL-Fehler in Datei: ". ___FILE___ . ":" . ___LINE___ . "<br />" . $this->db->error);
			$num_perm	= $res_perm->num_rows;
			
			if($num_perm > 0)
			{
				while(($row_perm = $res_perm->fetch_assoc()) != false)
				{
					if($this->searchPermission($row_perm, $permissions) == false)
					{
						$permissions[]	= $row_perm;
					}
				}
			}
			
			if($row_role['parentId'] != NULL)
			{
				$this->getRolePermission($row_role['parentId']);
			}
			
			if(empty($permissions))
			{
				return false;
			}
			
		}
		return $permissions;
	}
	
	
	private function searchPermission($needle, $permissions)
	{
		foreach($permissions as $permission)
		{
			if($permission['aclLinkPrivilegesId'] == $needle['aclLinkPrivilegesId'])
			{
				return true;
			}
			else
			{
				return false;
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
		
		$res	= mysql_query($sql)or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . mysql_error());
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
	
		$res	= mysql_query($sql)or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);
		$row	= mysql_fetch_array($res);
		
		return $row;
	}
	
	private function referenceType($type)
	{
		//ReferenzTyp festellen
	
		$sql_ref_typ	= "SELECT * FROM acl_reference_types WHERE type = '". $type ."' LIMIT 1";
		$res_ref_typ	= $this->db->query($sql_ref_typ)or die("SQL-Fehler in Datei: " . __FILE__ . ":" . ___LINE___ . "<br /><br />" . $this->db->error);
		$row_ref_typ	= $res_ref_typ->fetch_assoc();
		$num_ref_typ	= $res_ref_typ->num_rows;
	
		if($num_ref_typ == 1)
		{
			return $row_ref_typ['id'];
		}
	}
	private function checkForgottenData()
	{
		
	}
	
	public function sendMail()
	{
		
	}
}
?>
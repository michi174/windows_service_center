<?php

namespace wsc\acl;
use wsc\database\Database as Database;
use wsc\systemnotification\SystemNotification as Systemnotification;


/**
 * ACL (2013 - 08 - 21)
 *
 * ACL (Access Control List) Klasse um die Berechtigung einer Rolle, Gruppe oder Benutzer festzustellen und zu modifizieren.
 *
 * @author 		michi_000
 * @name 		ACL
 * @version		0.1 alpha
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */

class Acl
{
	private $db		= NULL;
	private $notify	= NULL;
	
	public function __construct()
	{
		$this->db		= Database::getInstance();
		$this->notify	= new Systemnotification("warning");
	}
	
	public function allow()
	{
		
	}
	
	public function deny()
	{
		
	}
	
	public function isAllowed($role, $ressource, $privilege)
	{
		$sql	= "SELECT * FROM user_group_members WHERE userdataUserId = ";
	}
	public function getAllResources()
	{
		$sql	= "SELECT * FROM acl_ressources";
		$res	= $this->db->query($sql) or die("SQL-Fehler: ".$this->db->error." on ".__FILE__.":".__LINE__);
		
		while($row = $res->fetch_assoc())
		{
			$ressources[]	= $row;	
		}
		
		return $ressources;
	}

	private function getUserRole()
	{
		
	}
}
?>
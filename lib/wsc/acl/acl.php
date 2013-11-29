<?php

namespace wsc\acl;
use wsc\database\Database as Database;


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
	private $user	= NULL;
	
	private $last_resourcelink	= NULL;
	
	public function __construct($user = NULL)
	{
		$this->db		= Database::getInstance();
		$this->user		= $user;
	}
	
	public function allow()
	{
		
	}
	
	public function deny()
	{
		
	}
	public function isAllowed($reference, $role, $ressource, $privilege)
	{
		
	}
	public function guard()
	{
		if($this->checkResourcelink() === true)
		{
			//
		}
	}
	
	/**
	 * Überprüft, ob eine Resourcen-Privileg Kombination in der Datenbank vorhanden ist.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	public function checkResourcelink($resource = NULL, $privileg = NULL)
	{		
		if(is_null($privileg) && is_null($resource))
		{
			if(isset($_GET[DEFAULT_LINK]))
			{
				$resource	= $_GET[DEFAULT_LINK];
				
				if(isset($_GET[DEFAULT_ACTION]))
				{
					$privileg	= $_GET[DEFAULT_ACTION];
				}
			}
		}
		
		if(!is_null($privileg) && !is_null($resource))
		{
			$privileg	= $this->db->getDataByField("acl_privileges", "name", $privileg);
			$resource	= $this->db->getDataByField("acl_resources", "name", $resource);
			
			$sql	= "
						SELECT
							*
						FROM
							acl_link_privileges
						WHERE
							aclResourcesId = '".$resource['id']."'
							AND
							aclPrivilegesId ='".$privileg['id']."'
						";
			$res	= $this->db->query($sql)or die("SQL-Fehler: ".$this->db->error." on ".__FILE__.":".__LINE__);
			$row	= $res->fetch_assoc();
			$num	= $res->num_rows;
			
			if($num == 1)
			{
				$this->last_resourcelink = $row['id'];
				
				return true;
			}
		}
		return false;
	}
	
	public function getAllResources()
	{
		$sql	= "SELECT * FROM acl_resources";
		$res	= $this->db->query($sql) or die("SQL-Fehler: ".$this->db->error." on ".__FILE__.":".__LINE__);
		
		while(($row = $res->fetch_assoc()) != false)
		{
			$ressources[]	= $row;	
		}
		
		return $ressources;
	}
	public function getAllPrivileges()
	{
		$sql	= "SELECT * FROM acl_privileges";
		$res	= $this->db->query($sql) or die("SQL-Fehler: ".$this->db->error." on ".__FILE__.":".__LINE__);
	
		while(($row = $res->fetch_assoc()) != false)
		{
			$privileges[]	= $row;
		}
	
		return $privileges;
	}
}
?>
<?php

namespace wsc\acl;
use wsc\database\Database as Database;
use wsc\user\User;


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
	
	private $last_perm	= array();
	
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
	
	public function guard()
	{
		//Überrpüfen, ob Ressourcenlink vorhanden ist.
		if($this->checkResourcelink() === true)
		{
			//Berechtigung des Benutzers prüfen
			
			//wenn berechtigung vorhanden
			//return true
			//sonst
			//return false
		}
		
		//sonst
		//return true
	}
	
	/**
	 * Überprüft ob der Benutzer, Rolle oder die Gruppe eine Berechtigung auf eine Ressource hat.
	 * 
	 * Wird ein Benutzerobjekt übergeben, wird direkt auf die Berechtigung des Benutzers zurückgegriffen.
	 * Andernfalls werden die Berechtigungen aus der Datenbank geholt.
	 * 
	 * @param mixed		$reference
	 * @param string 	$ressource
	 * @param string 	$privilege
	 * @param string	$reference_type
	 */
	
	
	public function hasPermission($reference, $resource, $privilege, $reference_type = NULL)
	{
		if(is_string($resource))
		{
			$resource	= $this->resourceString2resourceID($resource);
		}
			
		if(is_string($privilege))
		{
			
			$privilege	= $this->privilegeString2privilegID($privilege);
		}

		$resourcelink	= $this->getResourceLink($resource, $privilege);
		
		if($reference instanceof User)
		{
			return $this->searchPermission($resourcelink, $reference->permissions);
		}
		else
		{
			if(is_string($reference_type) && ($this->referenceType($reference_type) !== false))
			{
				if(is_numeric($reference))
				{
					$permissions	= $this->getPermissionByReference($reference_type, $reference);
					return $this->searchPermission($resourcelink, $permissions);					
				}
				else 
				{
					die(__CLASS__ ."::".__METHOD__.": Falscher Datentyp bei \$reference");
				}
				
			}
			else
			{
				die(__CLASS__ ."::".__METHOD__." kann den Referenztyp nicht feststellen!");
			}
		}
	}
	
	/**
	 * Überprüft, ob eine Resourcen-Privileg Kombination in der Datenbank vorhanden ist.
	 *
	 * @return (bool) true oder (bool) false
	 * @since 1.0
	 */
	public function checkResourcelink($resource = NULL, $privilege = NULL)
	{		
		if(is_null($privilege) && is_null($resource))
		{
			if(isset($_GET[DEFAULT_LINK]))
			{
				$resource	= $_GET[DEFAULT_LINK];
				
				if(isset($_GET[DEFAULT_ACTION]))
				{
					$privilege	= $_GET[DEFAULT_ACTION];
				}
			}
		}
		
		if(!is_null($privilege) && !is_null($resource))
		{
			$privilege	= $this->privilegeString2privilegID($privilege);
			$resource	= $this->resourceString2resourceID($resource);
			
			
			$right		= $this->getResourceLink($resource, $privilege);
			
			if($right !== false)
			{
				$this->last_resourcelink = $right['id'];
				
				return true;
			}
		}
		return false;
	}
	
	private function resourceString2resourceID($resource)
	{
		if(is_string($resource))
		{
			$resource	= $this->db->getDataByField("acl_resources", "name", $resource);
			
			return $resource['id'];
		}
	}
	private function privilegeString2privilegID($privilege)
	{
		if(is_string($privilege))
		{
			$privilege	= $this->db->getDataByField("acl_privileges", "name", $privilege);
				
			return $privilege['id'];
		}
	}
	
	public function getResourceLink($resource, $privilege)
	{
		$sql	= "
						SELECT
							*
						FROM
							acl_link_privileges
						WHERE
							aclResourcesId = '".$resource."'
							AND
							aclPrivilegesId ='".$privilege."'
						";
		$res	= $this->db->query($sql)or die("SQL-Fehler: ".$this->db->error." on ".__FILE__.":".__LINE__);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			$row	= $res->fetch_assoc();
			return $row; 
		}
		else 
			return false;
	}
	
	public function getPermissionByReference($type, $reference, &$permissions = null)
	{
	
		$reference_type		= $this->db->getDataByField("acl_reference_types", "type", $type);
		$reference_table	= $reference_type['referenceTable'];
		

		//Überprüfen, ob Referenz existiert.
		$sql	= "SELECT * FROM " . $reference_table . " WHERE id = '". $reference ."'";
		$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: ". __FILE__ . ":" . __LINE__ . "<br />" . $this->db->error);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			$reference	= $res->fetch_assoc();
			
			$sql_perm	= "	SELECT
								*
							FROM
								acl_permissions
							WHERE
								aclReferenceType = '".$reference_type['id']."'
								AND
								aclReference = '".$reference['id']."'";
			
			$res_perm	= $this->db->query($sql_perm)or die("SQL-Fehler in Datei: ". ___FILE___ . ":" . ___LINE___ . "<br />" . $this->db->error);
			$num_perm	= $res_perm->num_rows;
			
			if($num_perm > 0)
			{
				while(($row_perm = $res_perm->fetch_assoc()) != false)
				{
					$sql_link	= "SELECT * FROM acl_link_privileges WHERE id = ".$row_perm['aclLinkPrivilegesId'];
					$res_link	= $this->db->query($sql_link) or die("SQL-Fehler in Datei: ". __FILE__ . ":" . __LINE__ . "<br />" . $this->db->error);
					$row_link	= $res_link->fetch_array();
					
					if($this->searchPermission($row_link, $permissions) == false)
					{
						$permissions[]	= $row_link;
					}
				}
			}
			if($reference['parentId'] != 0)
			{
				$this->getPermissionByReference($type, $reference['parentId'], $permissions);
			}
		}
		return $permissions;
		
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
		else
			return false;
	}
	
	public function searchPermission($needle, $permissions)
	{
		if(!empty($permissions))
		{
			foreach($permissions as $permission)
			{
				if($permission['id'] == $needle['id'])
				{
					return true;
				}
			}
		}
		return false;
	}
}
?>
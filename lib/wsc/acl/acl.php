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
		if($this->checkResourcelink() === true)
		{
			//
		}
	}
	
	public function isAllowed($reference, $role, $ressource, $privilege)
	{
		
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
					if($this->searchPermission($row_perm, $permissions) == false)
					{
						$permissions[]	= $row_perm;
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
	}
	
	public function searchPermission($needle, $permissions)
	{
		if(!empty($permissions))
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
	}
}
?>
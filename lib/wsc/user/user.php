<?php

namespace wsc\user;
use wsc\database\Database;
use wsc\acl\Acl;

/**
 *
 * @author Michi
 *        
 */
class User 
{
	private $userid		= NULL;
	private $db			= NULL;
	
	public $data		= array();
	public $groups		= array();
	public $roles		= array();
	public $permissions	= array();
	
	public function __construct($userid)
	{
		if(!is_null($userid))
			$this->userid	= $userid;
		else 
			die('Keine UserID uebergeben');
		
		$this->db	= Database::getInstance();
		
		$this->initUser();
	}
	
	private function initUser()
	{
		$this->getData();
		$this->getGroups();
		$this->getRoles();
		$this->getPermissions();
	}
	
	public function getData()
	{
		$sql	= "SELECT * FROM userdata WHERE id	= " . $this->userid;
		$res	= $this->db->query($sql) or die("SQL-Fehler in Datei: ". __FILE__ . ":" . __LINE__ . "<br />" . $this->db->error);
		$row	= $res->fetch_assoc();

		$this->data	= $row;
	}
	
	public function getGroups()
	{
		$groups	= array();
		
		$sql	= "SELECT * FROM user_group_members WHERE userdataUserId = " . $this->userid;
		$res	= $this->db->query($sql)or die("SQL-Fehler in Datei: ". ___FILE___ . ":" . ___LINE___ . "<br />" . $this->db->error);
		
		while(($row = $res->fetch_assoc()) != false)
		{
			$groups[]	= $row['userGroupsId'];
		}
		
		$this->groups = $groups;
	}
	
	public function getRoles()
	{
		$roles		= array();
		
		if(empty($this->groups))
		{
			$this->getGroups();
		}
		
		//Rollen, die durch Gruppe auf den User angewandt werden können.
		if(!empty($this->groups))
		{
			foreach($this->groups as $grp_id)
			{
				$sql_grp_rls	= "	SELECT
										*
									FROM
										acl_role_members
									WHERE
										referenceId = " . $grp_id . " 
										AND 
										aclReferenceType = '". $this->db->getDataByField('acl_reference_types', 'type', 'group')['id'] ."'";
		
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
								referenceId = " . $this->data['id'] . " AND aclReferenceType = '". $this->db->getDataByField('acl_reference_types', 'type', 'user')['id'] ."'";
		
		$res_usr_rls	= $this->db->query($sql_usr_rls)or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $this->db->error);
		
		while(($row_usr_rls = $res_usr_rls->fetch_array()) != false)
		{
			if(!in_array($row_usr_rls['aclRolesId'], $roles))
			{
				array_push($roles, $row_usr_rls['aclRolesId']);
			}
		}
		
		$this->roles = $roles;
		
	}
	
	public function getPermissions()
	{
		$acl				= new Acl;
		$permissions		= array();
		$double_permission	= array();
		
		$userpermissions	= NULL;
		$grouppermissions	= array();
		$rolepermissions	= array();
		
		//Falls keine Gruppen und Rollen vorhanden, sicherheitshalber nochmal prüfen, ob welche vorhanden sind.
		if(empty($this->groups))
			$this->getGroups();
		
		if(empty($this->roles))
			$this->getRoles();
		
		
		//Rechte, die direkt auf Benutzer referenzieren.
		$userpermissions	= $acl->getPermissionByReference('user', $this->data['id']);
		
		//Rechte, die auf Benutzergruppe referenzieren.
		if(count($this->groups) > 0)
		{
			foreach($this->groups as $group)
			{
				$grouppermissions[]	= $acl->getPermissionByReference('group', $group);
			}
		}
		
		//Rechte, die auf Rolle referenzieren.
		if(!empty($this->roles))
		{				
			foreach ($this->roles as $role)
			{
				$rolepermissions[]	= $acl->getPermissionByReference('role', $role);
			}
		}
		
		//Alle Rechte in ein Array zusammenführen, doppelte werden gleich entfernt.
		if(!empty($userpermissions))
		{
			foreach($userpermissions as $userpermission)
			{
				if($acl->searchPermission($userpermission, $permissions) == false)
				{
					$permissions[]	= $userpermission;
				}
				else
				{
					$double_permission[]	= $userpermission;
				}
			}
		}
		
		if(!empty($grouppermissions))
		{
			foreach ($grouppermissions as $grouppermission_container)
			{
				if(is_array($grouppermission_container))
				{
					foreach ($grouppermission_container as $grouppermission)
					{
						if($acl->searchPermission($grouppermission, $permissions) == false)
						{
							$permissions[]	= $grouppermission;
						}
						else
						{
							$double_permission[]	= $grouppermission;
						}
					}
				}
			}
		}
		if(!empty($rolepermissions))
		{
			foreach ($rolepermissions as $rolepermission_container)
			{
				if(is_array($rolepermission_container))
				{
					foreach ($rolepermission_container as $rolepermission)
					{
						if($acl->searchPermission($rolepermission, $permissions) == false)
						{
							$permissions[]	= $rolepermission;
						}
						else
						{
							$double_permission[]	= $rolepermission;
						}
					}
				}
			}
		}
		$this->permissions	= $permissions;		
	}
}
?>
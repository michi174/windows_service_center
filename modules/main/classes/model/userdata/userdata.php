<?php

namespace model\userdata;

use wsc\model\Model_abstract;
use wsc\application\Application;

/**
 *
 * @author Michi
 *        
 */
class userdata extends Model_abstract 
{
	private $db;
	
	public function __construct()
	{
		$this->db	= Application::getInstance()->load("Database");
	}
	
	public function getUserDataByID($userid)
	{
		$result	= $this->db->query("SELECT * FROM userdata WHERE id = ".$userid) or die($this->db->error . __FILE__);
		
		return $result->fetch_assoc();
		
	}
	
	public function checkUserData4Login($account, $authorisation)
	{
		//do something..
	}
	
	
}

?>
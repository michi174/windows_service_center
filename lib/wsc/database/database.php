<?php
namespace wsc\database;

class Database extends \mysqli
{
	private static $object = NULL;
	
	public function __construct()
	{
		parent::__construct(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE);
	}
	
	public static function getInstance()
	{
		if(self::$object instanceof \wsc\config\Config)
		{
			return self::$object;
		}
		else
		{
			$object			= new Database(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE);
			self::$object	= $object;
	
			return $object;
		}
	}
	
	public function getDataByID($table, $id)
	{
		$query	= "SELECT * FROM " . $table . " WHERE id = '". $id ."'";
		$result	= $this->query($query) or die("Query: `". $query . "` meldet einen Fehler!<br /><br />" . $this->error);
		$data	= $result->fetch_assoc();
		
		return $data;
	}
	
	public function getDataByField($table, $field, $value)
	{
		$query	= "SELECT * FROM " . $table . " WHERE " . $field . " = '" . $value . "'";
		$result	= $this->query($query) or die("Query: `". $query . "` meldet einen Fehler!<br /><br />" . $this->error);
		$data	= $result->fetch_assoc();
		
		return $data;
	}
}

?>
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
		if(self::$object instanceof Config)
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
		$query	= "SELECT * FROM " . $table . " WHERE id = " . $id;
		$result	= $this->query($query);
		$data	= $result->fetch_assoc();
		
		return $data;
	}
}

?>
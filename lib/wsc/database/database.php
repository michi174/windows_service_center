<?php
namespace wsc\database;

class Database extends \mysqli
{
	public function __construct()
	{
		parent::__construct(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE);
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
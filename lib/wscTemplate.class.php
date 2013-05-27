<?php
class wscTemplate
{
	protected $delimeter_left	= "{";
	protected $delimeter_right	= "}";
	
	protected $tpl				= array();
	protected $tpl_dir			= null;
	
	protected $tpl_vars			= array();
	
	protected $db				= null;
	
	const CACHE_TIMEOUT_RANGE	= 3600;
	
	
	public function __construct(Database $database = null)
	{
		if(!is_null($database))
		{
			if ($database instanceof Database)
			{
				$this->db	= $database;
				echo "DB_CONN_SUCCESS <br />";
			}
			else
			{
				throw new DatabaseError_Exception("(".gettype($database).") \$database muss ein Objekt vom Typ Database sein.");
			}
		}
	}
	
	protected function cache()
	{
		
	}
	
	protected function compileCode()
	{
		
	}
	
	
	
	public function assign()
	{
	
	}
	public function display()
	{
		
	}
	
	
}
class SyntaxError_Exception extends Exception
{
	public function __construct($message, $code = null)
	{
		parent::__construct($message, $code);
	}
}
class FileError_Exception extends Exception
{
	public function __construct($message, $code = null)
	{
		parent::__construct($message, $code);
	}
}
class DatabaseError_Exception extends Exception
{
	public function __construct($message, $code = null)
	{
		parent::__construct($message, $code);
	}
}

?>
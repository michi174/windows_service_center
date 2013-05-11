<?php
class CMS 
{
	protected $parentID	= NULL;
	protected $parent	= NULL;
	protected $action	= NULL;
	protected $child	= NULL;
	
	protected $data			= array();
	protected $child_data	= array(array());
	
	protected $db		= NULL;
	
	
	public function __construct($action, $parent, $parentID, $child = false)
	{
		$this->db		= new Database();
		
		$this->parent	= $parent;
		$this->parentID	= $parentID;
		$this->action	= $action;
		$this->child	= $child;
	}
	
	public function getData()
	{
		$sql	= "SELECT * FROM " . $this->action . " WHERE parent = '" . $this->parentID . "'";
		$res	= $this->db->query($sql); //or die($this->db->error);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			while(($data = $res->fetch_assoc()) == true)
			{
				if($this->child !== false)
				{
					$child_sql	= "SELECT * FROM " . $this->child . " WHERE parent = '". $data['id'] ." ' ";
					$child_res	= $this->db->query($child_sql);
					$child_num	= $child_res->num_rows;
					
					if($child_num > 0)
					{
						while(($child_data = $child_res->fetch_assoc()) == true)
						{
							$this->child_data[$data['id']][] = $child_data;
						}
					}
				}
				$this->data[]	= $data;
			}
			return $this->data;
		}
		else
		{
			return false;
		}
	}
	
	public function printData()
	{
		echo "<br><h3>Funktion: printData() - [public]</h3>";
		
		if($this->getData() !== false)
		{
			echo "<strong>" . $this->action. "</strong><br><br>";
			foreach($this->data as $key => $data)
			{
				echo $data['title']."<br>";	
				if ($this->child !== false)
				{
					if(!empty($this->child_data[$data['id']]))
					{
						foreach($this->child_data[$data['id']] as $child_key => $child_data)
						{
							echo "<blockquote><a href=\"?site=tips&action=overview&parent=" . $child_data['id'] . "\">" . $child_data['title']. "</a><br></blockquote>";
						}
					}
					else
					{
						echo "<blockquote>Es gibt keine Child-Elemente<br></blockquote>";
					}
				}
			}
		}
		else
		{
			echo "Keine Elemente vorhanden.";
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public static function getDetailData($id, $print = false)
	{
		$db			= new Database();
		
		$sql		= "SELECT * FROM posts WHERE id = " . $id;
		$res		= $db->query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . $db->error);
		$num		= $res->num_rows;

		if($num > 0)
		{
			$row		= $res->fetch_assoc();
			$template	= PLUGIN_DIR . $_REQUEST[DEFAULT_LINK] . "/templates/posts.php";
			$topicdata	= self::getTopicData($row['topic']);
			
			$title 			= $topicdata['title'];
			$subtitle		= $topicdata['subtitle'];
			$source			= new BBCode();
			$source			= $source->parseText($topicdata['source']);
			$author			= Login::getUserData($row['author']);
			$bbcode			= new BBCode(true, true, false);
			$date			= date("d. M. Y \\u\\m H:i", $row['date']) . " Uhr";
			$author			= $author['firstname'] . " " . $author['lastname'];
			$text			= $bbcode->parseText($row['text']);
			
			if($print === true)
			{
				$html_message	= include $template;
			}
			else 
			{
				return $row;
			}
		}
		else
		{
			return false;
		}
	}
	
	
	
	
	
	
	
	
	public static function getTopicData($id, $print=false)
	{
		$db		= new Database();
		
		$template	= PLUGIN_DIR . $_REQUEST[DEFAULT_LINK] . "/templates/topics.php";
		
		$sql	= "SELECT * FROM topics WHERE id = " . $id;
		$res	= $db->query($sql);
		$num	= $res->num_rows;
		
		$title = "<span style=\"color:#F00\">Testtitel vom Board</span>";		// -> ANPASSEN! 
		
		
		if($num > 0)
		{
			if($print === true)
			{
				include $template;
			}
			else 
			{
				return $res->fetch_assoc();	
			}
		}
		else 
		{
			return false;
		}
	}
	//-------------------------

	public static function getCategoryData($id, $print, $show_boards)
	{
		$db		= new Database();
		
		$template	= PLUGIN_DIR . $_REQUEST[DEFAULT_LINK] . "/templates/categories.php";
		
		$sql	= "SELECT * FROM categories WHERE id = " . $id;
		$res	= $db->query($sql);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			if($print === true)
			{
				include $template;
			}
			else
			{
				return $res->fetch_assoc();
			}
		}
		else
		{
			return false;
		}
	}
	
	
	
	
	public static function getBoardData($id, $print)
	{
		$db		= new Database();
		
		$template	= PLUGIN_DIR . $_REQUEST[DEFAULT_LINK] . "/templates/categories.php";
		
		$sql	= "SELECT * FROM boards WHERE id = " . $id;
		$res	= $db->query($sql);
		$num	= $res->num_rows;
		
		if($num > 0)
		{
			if($print === true)
			{
				include $template;
			}
			else
			{
				return $res->fetch_assoc();
			}
		}
		else
		{
			return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	protected function updateClicks($id)
	{
		$sql	= "UPDATE topics SET clicks = clicks+1 WHERE id = " . $id;
		$res	= mysql_query($sql) or die("SQL-Fehler in Datei: " . __FILE__ . ":" . __LINE__ . "<br /><br />" . mysql_error());
	}
}

?>
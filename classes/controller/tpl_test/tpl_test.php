<?php

namespace controller\tpl_test;

use wsc\controller\controller_abstract;
use wsc\view\view_template;
use wsc\database\Database;
use wsc\functions\tools\Tools;
use wsc\template\NewTemplate;
use wsc\view\View_php;

/**
 *
 * @author Michi
 *        
 */
class tpl_test extends controller_abstract {
	
	private $model_userdata;
	private $model_login_protocol;
	private $model_cms_topics;
	
	private function init()
	{
		//$this->model_userdata			= new Model_userdata;
		//$this->model_login_protocol	= new Model_login_protocol;
		//$this->model_cms_topics		= new Model_cms_topics;
	}
	
	public function newtemplate_action()
	{
		$view	= new View_php();
		$tpl	= new NewTemplate();
		
		$tpl->setTemplateDir("test/");
		$tpl->setTemplate("tplnew.html");
		$output	= $tpl->render();
		//var_dump($tpl->getErrors());
		
		$view->add($output);
		
		
	}
	

	public function view_action()
	{
		$view	= new view_template();

		$db	= Database::getInstance();
		
		$sql_users	= "SELECT * FROM userdata";
		$sql_logins	= "SELECT DISTINCT userdata.username, login_protocol.ip, login_protocol.time, userdata.username FROM login_protocol JOIN userdata ON login_protocol.userid = userdata.id  ORDER BY time DESC LIMIT 5";
		$sql_areas	= "SELECT * FROM areas";
		
		$sql_sub_login	= "SELECT * FROM login_protocol WHERE userid = '{users.id}' ORDER BY time DESC LIMIT 5";
		$sql_sub_topics	= "SELECT * FROM cms_topics WHERE author = '{users.id}'";
		
		$sql_sub_area_topics	= "SELECT * FROM cms_topics WHERE parent = '{plugins.id}'";
		
		$sql_count		= "SELECT COUNT(username) as test FROM userdata ORDER BY id ASC LIMIT 5";
		$sql_distinct	= "SELECT DISTINCT userid as test FROM login_protocol";
		
		$zeit = time();
		
		$sql_us_dta	= "SELECT * FROM userdata";
		$res_us_dta	= $db->query($sql_us_dta);
		
		while(($row_us_dta = $res_us_dta->fetch_assoc()) == true )
		{
			$data[]	= $row_us_dta;
		}
		
		//Test Vars
		$view->assignVar("datum", strftime("%A, %d. %B %Y", $zeit));
		$view->assignVar("zeit", date("H:i:s", $zeit));
		$view->assignVar("zeitzone", date("T (e)", $zeit));
		$view->assignVar("zeitunterschied", "GMT ".date("P", $zeit));
		$view->assignVar("kw", date("W", $zeit));
		$view->assignVar("kt", date("w", $zeit));
		$view->assignVar("browser", $_SERVER["HTTP_USER_AGENT"]);
		$view->assignVar("vorname", "Michael");
		$view->assignVar("nachname", "Strasser");
		$view->assignVar("alter", "22");
		$view->assignVar("geschlecht", "m&auml;nnlich");
		
		//Test ArrayKey
		$view->assignVar("testarray", 		array(	"vn" => "Michael",
				"nn" => "Strasser"
		));
		
		//Test Foreach
		$view->assignVar("testname", 		array(	"Michael" 	=> "Strasser",
				"Dominik" 	=> "Gintenreiter",
				"Mathias" 	=> "Zauner",
				"Martin" 	=> "Wimplinger"
		));
		$view->assignVar("testnamen_sub", 	array(	"Ramona" 	=> "Strasser",
				"Michael" 	=> "Gintenreiter",
				"Martina" 	=> "Zauner",
				"Stefanie" 	=> "Wimplinger"
		));
		
		//Testarray für Kombination aus Array und Foreach
		$view->assignVar("userary", $data);

		
		//Test Datarow
		$view->assignDatarow("users", $sql_users, array("vorname" => "firstname","nachname" => "lastname", "geschlecht" => "sexuality", "reg_datum" => "registration", "id" => "id"));
		$view->assignDatarow("logins", $sql_logins, array("zeit" => "time", "ip" => "ip", "benutzer" => "username"));
		$view->assignDatarow("plugins", $sql_areas, array("name" => "display_name", "id" => "id"));
		$view->assignDatarow("sql_test", $sql_distinct, array("ID" => "test"));
		
		//Test Subrow
		$view->assignSubrow("login", "users", $sql_sub_login, array("zeit" => "time"));
		$view->assignSubrow("topics", "users", $sql_sub_topics, array("titel" => "title"));
		$view->assignSubrow("areatopics", "plugins", $sql_sub_area_topics, array("titel" => "title"));
		
		//Test Functions
		$view->assignFunction("login.zeit", 'date("d.m.Y \u\m H:i", {var})');
		$view->assignFunction("users.nachname", 'strtoupper("{var}")');
		$view->assignFunction("users.reg_datum", 'date("d.m.Y \u\m H:i",{var})');
		$view->assignFunction("logins.zeit", 'date("d.m.Y \u\m H:i", {var})');

		return $view;
	}


	/**
	 * (non-PHPdoc)
	 *
	 * @see \wsc\controller\controller_abstract::default_action()
	 *
	 */
	public function default_action()
	{
		Tools::internalRedirect("error", "default");
	}
}

?>
<?php
use wsc\database\Database;

//MODEL der Template Klasse
$notify		= new wsc\systemnotification\SystemNotification();
$db			= Database::getInstance();

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

$temp = new wsc\template\Template($db);

$temp->setTemplateDir("test/");
$temp->addTemplate("tpl_test.html");

//Statische Variablen ersetzen
$temp->assign("datum", strftime("%A, %d. %B %Y", $zeit));
$temp->assign("zeit", date("H:i:s", $zeit));
$temp->assign("zeitzone", date("T (e)", $zeit));
$temp->assign("zeitunterschied", "GMT ".date("P", $zeit));
$temp->assign("kw", date("W", $zeit));
$temp->assign("kt", date("w", $zeit));
$temp->assign("browser", $_SERVER["HTTP_USER_AGENT"]);
$temp->assign("vorname", "Michael");
$temp->assign("nachname", "Strasser");
$temp->assign("alter", "22");
$temp->assign("geschlecht", "m&auml;nnlich");

//Testarray für parseArrayKeys
$temp->assign("testarray", 		array(	"vn" => "Michael", 
										"nn" => "Strasser"
));

//2 Testarrays für Foreach
$temp->assign("testname", 		array(	"Michael" 	=> "Strasser",
										"Dominik" 	=> "Gintenreiter",
										"Mathias" 	=> "Zauner",
										"Martin" 	=> "Wimplinger"
));
$temp->assign("testnamen_sub", 	array(	"Ramona" 	=> "Strasser",
										"Michael" 	=> "Gintenreiter",
										"Martina" 	=> "Zauner",
										"Stefanie" 	=> "Wimplinger"
));

//Testarray für Kombination aus Array und Foreach
$temp->assign("userary", $data);

$temp->assignDatarow("users", $sql_users, array("vorname" => "firstname","nachname" => "lastname", "geschlecht" => "sexuality", "reg_datum" => "registration", "id" => "id"));
$temp->assignDatarow("logins", $sql_logins, array("zeit" => "time", "ip" => "ip", "benutzer" => "username"));
$temp->assignDatarow("plugins", $sql_areas, array("name" => "display_name", "id" => "id"));
$temp->assignDatarow("sql_test", $sql_distinct, array("ID" => "test"));

$temp->assignSubrow("login", "users", $sql_sub_login, array("zeit" => "time"));
$temp->assignSubrow("topics", "users", $sql_sub_topics, array("titel" => "title"));
$temp->assignSubrow("areatopics", "plugins", $sql_sub_area_topics, array("titel" => "title"));

$temp->assignFunction("login.zeit", 'date("d.m.Y \u\m H:i", {var})');
$temp->assignFunction("users.nachname", 'strtoupper("{var}")');
$temp->assignFunction("users.reg_datum", 'date("d.m.Y \u\m H:i",{var})');
$temp->assignFunction("logins.zeit", 'date("d.m.Y \u\m H:i", {var})');

$temp->display();
?>


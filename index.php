<?php
use wsc\database\Database;
use wsc\auth\Auth;
use wsc\pluginmanager\PluginManager;
use wsc\acl\Acl;
use wsc\application\Application;

session_start();
date_default_timezone_set('Europe/Vienna');
setlocale (LC_ALL, 'deu');

require_once '../framework/config.php';
require_once 'autoloader.php';

//Anwendung konfigurieren
$config->set("project_dir", "windows_service_center");
$config->set("abs_project_path", $config->get("doc_root")."/".$config->get("project_dir"));
$config->readIniFile($config->get("abs_project_path").'/admin/config.ini');
$config->set("forward_link", $_SERVER['QUERY_STRING']);

//Anwendung starten
$app		= Application::getInstance();

//Module registrieren
$app->register("Database", Database::getInstance());
$app->register("Acl", new Acl($app));
$app->register("Auth", new Auth($app));

try 
{
	$db			= $app->load("Database");
	$auth		= $app->load("Auth");
	$acl		= $app->load("Acl");
	$controller	= $app->load("FrontController");
}
catch (Exception $e)
{
	die("Es ist ein Fehler aufgetreten in: <br />
	<strong>". $e->getFile()." Zeile: ".$e->getLine()."</strong><br /><br />
	Meldung: <br />".$e->getMessage()."<br /><br />
	Backtrace: <br />".nl2br($e->getTraceAsString()));
}

$blacklist	= array("");

$controller->addSubController("head", $blacklist);
$controller->addSubController("header", $blacklist);
$controller->addSubController("livetiles" ,$blacklist);
$controller->addSubController("content_start");
$controller->addSubController("content_end");
$controller->addSubController("footer", $blacklist);

$plugins	= PluginManager::getPlugins(false);

if(isset($_GET['logout']))
{
	$auth->logout();
	$app->load("Response")->redirect("?".str_replace('&logout', "", $config->get("forward_link")));
}
if(isset($_POST['login_x']))
{
	$username	= $_POST['username'];
	$password	= $_POST['password'];
	$cookie		= (isset($_POST['save_login'])) ? true : false;

	$auth->login($username, $password, $cookie);
}

$user		= $auth->getUser();
$page_error	= NULL;

//Application ausführen.
$app->run();
//Provisorische Ausgabe zum schnellen Testen ohne Controller.
	
if(isset($_REQUEST[$config->get("default_link")]) && !empty($_REQUEST[$config->get("default_link")]))
{
	switch($_REQUEST[$config->get("default_link")])
	{
		case 'addcat':
			include('addcategorie.php');
			break;
		case 'addtext':
			include('addtext.php');
			break;
	}
}
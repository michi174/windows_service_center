<?php
	use wsc\frontcontroller\Frontcontoller;
	
	session_start();

	require_once $_SERVER["DOCUMENT_ROOT"].'/framework/config.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/windows_service_center/admin/config.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/windows_service_center/lib/wsc/functions/functions.php';
	
	$config	= wsc\config\config::getInstance();
	
	$config->set("document_root", $_SERVER["DOCUMENT_ROOT"]);
	$config->set("project_path", $config->get("document_root")."/windows_service_center") ;
	$config->set("lib_path", $config->get("project_path")."/lib");
	
	$config->readIniFile($config->get("document_root").'/windows_service_center/admin/config.ini');
	$config->set("forward_link", $_SERVER['QUERY_STRING']);
	
	$controller	= new Frontcontoller();
	$controller->run();
	
	$forward_link	= $_SERVER['QUERY_STRING'];
	$referer_site	= (isset($_REQUEST[DEFAULT_LINK])) ? $_REQUEST[DEFAULT_LINK] : NULL;
	
	
	$db			= wsc\database\Database::getInstance();
	$plug		= new wsc\pluginmanager\PluginManager;
	$auth		= new wsc\auth\Auth($db);
	$plugins	= wsc\pluginmanager\PluginManager::getPlugins(false);
	
	$http_request	= wsc\http_request\Http_request::getInstance();
	
	if(isset($_GET['logout']))
	{
		$auth->logout();
		header('Location:?'.str_replace('&logout', "", $forward_link));
		
		
	}
	if(isset($_POST['login_x']))
	{
		$username	= $_POST['username'];
		$password	= $_POST['password'];
		$cookie		= (isset($_POST['save_login'])) ? true : false;
	
		$auth->login($username, $password, $cookie);
	}
	
	$user	= $auth->getUser();
	$acl	= new wsc\acl\Acl();
	
	if(isset($_GET['check_plugins']))
	{
		$plug->checkPlugins();
	}
	

	
?>
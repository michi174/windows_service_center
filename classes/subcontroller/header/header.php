<?php

namespace subcontroller\header;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;
use wsc\application\Application;
use wsc\pluginmanager\PluginManager;

/**
 *
 * @author Michi
 *        
 */
class header extends Subcontroller_abstract 
{
	private $application;
	
	public function __construct()
	{
		$this->application = Application::getInstance();
	}
	private function build()
	{

	}
	public function runBeforeMain()
	{
		$auth		= $this->application->load("Auth");
		$plugins	= PluginManager::getPlugins(false);
		
		$user		= $auth->getUser();
		$acl		= $this->application->load("Acl");
		
		$view		= new View_template(true);
		
		
		$view->assignVar("LOGGED_IN", $auth->isLoggedIn());
		$view->assignVar("FIRSTNAME", $user->data['firstname']);
		$view->assignVar("BACKEND_VIEW", $acl->hasPermission($user, "backend", "view"));
		$view->assignVar("PLUGINS", $plugins);
		
		
		return $view;
	}
}

?>